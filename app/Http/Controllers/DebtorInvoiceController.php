<?php

namespace App\Http\Controllers;

use App\Models\DebtorInvoice;
use Illuminate\Http\Request;

class DebtorInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        if ($request->ajax()) {

            $query = DebtorInvoice::with('debitor')
                ->whereDate('invoice_date', $date);

            // Optional filter
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $invoices = $query->get();

            $data = $invoices->map(function ($row) {
                $percentage = $row->inv_percentage;
                $displayPerc = $percentage !== null ? $percentage . '%' : 'N/A';
                $inputValue = $percentage !== null ? $percentage : 0;

                return [
                    'invoice'        => invoiceNumber($row),
                    'invoice_date'   => \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    'debitor_name'  => $row->debitor->name ?? '-',
                    'inv_perc'        => '
                        <div class="inv-perc-wrapper" data-id="'.$row->id.'">

                            <span class="inv-perc-text">
                                '.$displayPerc.'
                            </span>

                            <i class="bi bi-pencil-square text-primary ms-2 cursor-pointer edit-inv-perc"></i>

                            <div class="inv-perc-edit d-none mt-1">
                                <input type="number"
                                    class="form-control form-control-sm inv-perc-input d-inline-block"
                                    style="width:80px"
                                    value="'.$inputValue.'">

                                <button class="btn btn-sm btn-success ms-1 save-inv-perc">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </div>
                        ',
                    'actions'        => '
                        <a href="' . route('admin.pos.debitors.invoices.print', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary">
                            Print
                        </a>
                        '
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('admin.pos.debitors.invoices', compact('date'));
    }

    public function print(DebtorInvoice $invoice)
    {
        $invoice->load([
            'debitor'
        ]);

        return view('admin.pos.debitors.print', compact('invoice'));
    }

    public function updatePercentage(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:debtor_invoices,id',
            'invoice_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $invoice = DebtorInvoice::find($request->id);
        $invoice->inv_percentage = $request->invoice_percentage;
        $invoice->save();

        return response()->json(['success' => true]);
    }
}
