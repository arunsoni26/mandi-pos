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
        $query = Customer::select('id', 'name', 'email', 'mobile', 'status', 'hide_dashboard');

        // Filters
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $customers = $query->get();

        // Format Data
        $data = $customers->map(function ($row) {
            return [
                'name' => $row->name,
                'email' => $row->email,
                'mobile' => $row->mobile,
                
                'status_toggle' => '
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox"
                            class="form-check-input toggle-status"
                            data-id="'.$row->id.'" '.($row->status ? 'checked' : '').'>
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
    
    public function form(Request $request)
    {
        $customer = $request->customerId ? Customer::findOrFail($request->customerId) : null;
        return view('admin.customers.partials.add-edit-form', compact('customer'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $request->user_id,
            'mobile' => 'required|number|max:255',
            'password' => $request->id ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Save to customers table
            if ($request->id) {
                $customer = Customer::findOrFail($request->id);
            } else {
                $customer = new Customer();
            }

            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->mobile = $request->mobile;
            $customer->customer_type = "Active Creditor";
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
