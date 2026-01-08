<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customers.index');
    }

    public function list(Request $request)
    {
        $query = Customer::select('id', 'name', 'mobile', 'pan', 'address', 'status', 'hide_dashboard');

        // Filters
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $customers = $query->get();

        // Format Data
        $data = $customers->map(function ($row) {
            return [
                'name' => $row->name,
                'mobile' => $row->mobile,
                'pan' => $row->pan,
                'address' => $row->address,

                'status_toggle' => '
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox"
                            class="form-check-input toggle-status"
                            data-id="' . $row->id . '" ' . ($row->status ? 'checked' : '') . '>
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
            // 'email' => 'nullable|email|max:255|unique:users,email,' . $request->user_id,
            'mobile' => 'required|numeric|digits_between:10,15',
            'pan' => 'nullable',
            'address' => 'nullable',
            // 'password' => $request->id ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Save to customers table
            if ($request->id) {
                $customer = Customer::findOrFail($request->id);
            } else {
                $customer = new Customer();
            }

            if ($request->hasFile('profile_pic')) {
                $file     = $request->file('profile_pic');
                $filename = now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

                // Delete old image on update
                if (Storage::disk('s3')->exists("customers/{$customer->id}/profile_pics/".$filename)) {
                    Storage::disk('s3')->delete("customers/{$customer->id}/profile_pics/".$filename);
                }

                $path = $request->file('profile_pic')->storeAs(
                    "customers/{$customer->id}/profile_pics/",
                    $filename,
                    's3');
                $customer->profile_pic = $path;
            }


            $customer->name = $request->name;
            $customer->pan = $request->pan;
            $customer->address = $request->address;
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
        // dd($request->all());
        $creditors = Customer::where('customer_type', $request->type)
            ->where('name', 'like', "%{$request->searchTerm}%")
            ->limit(20)
            ->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'msg' => 'creditors fetched successfully.',
            'creditors' => $creditors
        ]);
    }

    public function debtors(Request $request)
    {
        return Customer::where('customer_type', 'Debitor')
            ->where('name', 'like', "%{$request->q}%")
            ->limit(20)
            ->get(['id', 'name']);

        // return response()->json([
        //     'code' => 200,
        //     'status' => 'success',
        //     'msg' => 'debtors fetched successfully.',
        //     'debtors' => $debtors
        // ]);
    }
}
