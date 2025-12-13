<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\GstYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerDocumentController extends Controller
{
    public function index(Customer $customer)
    {
        $years = GstYear::orderBy('label','desc')->get();
        return view('admin.customers.docs.index', compact('customer','years'));
    }

    public function list(Request $request, Customer $customer)
    {
        $q = CustomerDocument::with(['gstYear','uploader'])
            ->where('customer_id',$customer->id);

        if ($request->filled('gst_year_id')) $q->where('gst_year_id',$request->gst_year_id);
        if ($request->filled('doc_type'))    $q->where('doc_type',$request->doc_type);

        $docs = $q->orderByDesc('created_at')->get();

        // Return in a format your DataTable expects (or simple array)
        return response()->json([
            'data' => $docs->map(function($d){
                return [
                    'year'    => $d->gstYear?->label,
                    'type'    => strtoupper($d->doc_type),
                    'desc'    => e($d->description),
                    'file'    => '<a href="'.route('admin.customers.docs.download', [$d->customer_id, $d->id]).'" class="btn btn-sm btn-outline-primary"><i class="fa fa-download"></i> Download</a>',
                    'by'      => $d->uploader?->name ?? '—',
                    'date'    => $d->created_at->format('d M Y'),
                    'actions' => '<button class="btn btn-sm btn-danger deleteDoc" data-id="'.$d->id.'"><i class="fa fa-trash"></i></button>',
                ];
            })
        ]);
    }

    // Modal form
    public function form(Request $request, Customer $customer)
    {
        $years = GstYear::orderBy('label','desc')->get();
        return view('admin.customers.docs.partials.form', [
            'customer' => $customer,
            'years'    => $years,
            'types'    => CustomerDocument::TYPES
        ]);
    }

    // Save multiple docs for one GST year + many types
    public function save(Request $request, Customer $customer)
    {
        $request->validate([
            'gst_year_id' => 'required|exists:gst_years,id',
            // Each doc type arrays are optional; we validate files if present
            'docs.*.*.file' => 'nullable|file|max:10240', // 10MB per file
            'docs.*.*.description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $uploaded = 0;

            // docs is shaped like: docs[itr][0][file], docs[itr][0][description], docs[computation][0][file], ...
            $docs = $request->input('docs', []);

            foreach (array_keys(CustomerDocument::TYPES) as $type) {
                if (!isset($docs[$type])) continue;

                $rows = $docs[$type]; // array of items for this type
                foreach ($rows as $index => $row) {
                    // Handle file input names: docs[type][index][file]
                    $fileInputName = "docs.$type.$index.file";
                    $desc          = $row['description'] ?? null;

                    if ($request->hasFile($fileInputName)) {
                        $file     = $request->file($fileInputName);
                        $filename = now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                        $path     = $file->storeAs("customers/{$customer->id}/{$request->gst_year_id}/$type", $filename, 's3');

                        CustomerDocument::create([
                            'customer_id' => $customer->id,
                            'gst_year_id' => $request->gst_year_id,
                            'doc_type'    => $type,
                            'description' => $desc,
                            'file_path'   => $path,
                            'uploaded_by' => auth()->id(),
                        ]);
                        $uploaded++;
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $uploaded ? "Uploaded $uploaded document(s)." : "No files selected.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 422);
        }
    }

    public function download(Customer $customer, $id)
    {
        $doc = CustomerDocument::where('customer_id',$customer->id)->findOrFail($id);
        return Storage::disk('s3')->download($doc->file_path);
    }

    public function destroy(Customer $customer, $id)
    {
        $doc = CustomerDocument::where('customer_id',$customer->id)->findOrFail($id);
        $doc->delete(); // soft delete
        return response()->json(['success'=>true,'message'=>'Document deleted']);
    }
}
