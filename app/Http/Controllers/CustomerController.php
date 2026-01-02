<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customers.index');
    }

    public function list(Request $request)
    {
        $query = Customer::with('group') // Eager load group relation
            ->select('id', 'name', 'email', 'pan', 'father_name', 'client_type_status', 'group_id', 'status', 'hide_dashboard');

        // Filters
        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->code) {
            $query->where('code', 'LIKE', "%{$request->code}%");
        }

        $customers = $query->get();

        // Format Data
        $data = $customers->map(function ($row) {
            return [
                'name' => $row->name,
                'email' => $row->email,
                'pan' => $row->pan ?? '-',
                'fathers_name' => $row->father_name ?? '-',
                'client_type_status' => ucfirst($row->client_type_status),
                'group' => $row->group->name ?? '-',
                
                'status_toggle' => '
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox"
                            class="form-check-input toggle-status"
                            data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
                    </div>
                ',

                'dashboard_toggle' => '
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox"
                            class="form-check-input toggle-dashboard"
                            data-id="'.$row->id.'" '.($row->hide_dashboard ? 'checked' : '').'>
                    </div>
                ',

                'actions' => view('admin.customers.partials.actions', compact('row'))->render()
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = !$customer->status;
        $customer->save();
        return response()->json(['success' => true]);
    }

    public function toggleDashboard($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->hide_dashboard = !$customer->hide_dashboard;
        $customer->save();
        return response()->json(['success' => true]);
    }
    
    public function form(Request $request)
    {
        $customer = $request->customerId ? Customer::findOrFail($request->customerId) : null;
        return view('admin.customers.partials.add-edit-form', compact('customer'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gst_name' => 'nullable|string|max:255',
            'pan' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:50|unique:customers,code,' . $request->id,
            'client_type_status' => 'nullable|string|max:50',
            'fathers_name' => 'nullable|string|max:255',
            'group_id' => 'nullable|integer|exists:customer_groups,id',
            'email' => 'nullable|email|max:255|unique:users,email,' . $request->user_id,
            'password' => $request->id ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Save to users table
            if ($request->user_id) {
                $user = User::find($request->user_id);
            } else {
                $user = new User();
            }

            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            } else {
                $user->password = bcrypt(123456);
            }
            $user->role_id = 3;
            $user->save();

            // Save to customers table
            if ($request->id) {
                $customer = Customer::findOrFail($request->id);
            } else {
                $customer = new Customer();
                $customer->user_id = $user->id;
            }

            $customer->name = $request->name;
            $customer->gst_name = $request->gst_name;
            $customer->pan = $request->pan;
            $customer->code = $request->code;
            $customer->client_type_status = $request->client_type_status;
            $customer->father_name = $request->fathers_name;
            $customer->group_id = $request->group_id;
            $customer->email = $request->email;
            $customer->city = $request->city;
            $customer->dob = $request->dob;
            $customer->gst = $request->gst;
            $customer->aadhar = $request->aadhar;
            $customer->address = $request->address;
            $customer->updated_by = auth()->id();
            $customer->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Customer saved successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function view(Request $request)
    {
        $customer = Customer::findOrFail($request->custId);
        return view('admin.customers.partials.view', compact('customer'));
    }

    public function creditors(Request $request)
    {
        return Customer::whereIn('customer_type', ['Active Creditor', 'Raw Creditor'])
            ->where('name', 'like', "%{$request->q}%")
            ->limit(20)
            ->get(['id', 'name']);
    }

    public function debtors(Request $request)
    {
        return Customer::where('customer_type', 'Debitor')
            ->where('name', 'like', "%{$request->q}%")
            ->limit(20)
            ->get(['id', 'name']);
    }

}
