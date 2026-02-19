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
                ->withSum('items as total_pieces', 'pieces')
                ->withSum('items as total_weight', 'weight')
                ->withSum('items as total_amount', 'total')
                ->whereDate('invoice_date', $date);

            // Optional filter
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $invoices = $query->get();

            // ✅ Footer totals
            $footer = [
                'items_charge_sum'      => 0,
                'percentage_charge_sum' => 0,
                'total_charge_sum'      => 0,
            ];
            
            $data = $invoices->map(function ($row) use (&$footer) {
                $itemsAmount  = $row->total_amount ?? 0;

                $percentage = $row->inv_percentage;
                $displayPerc = $percentage !== null ? $percentage . '%' : 'N/A';
                $inputValue = $percentage !== null ? $percentage : 0;

                $invPercentage   = $row->inv_percentage ?? 0;

                // ✅ Calculate percentage charge FROM invoice table percentage
                $percentageCharge = ($itemsAmount * $invPercentage) / 100;
                $footer['percentage_charge_sum'] += $percentageCharge;

                return [
                    'invoice'           => invoiceNumber($row),
                    'invoice_date'      => \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    'debitor_name'      => $row->debitor->name ?? '-',
                    'pieces'            => $row->total_pieces ?? 0,
                    'weight'            => number_format($row->total_weight ?? 0, 2),
                    'inv_perc'          => '
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
                    'perc_charge'       => number_format($percentageCharge ?? 0, 2),
                    'total_amount'      => number_format($row->total_amount ?? 0, 2),
                    'actions'           => '
                        <a href="' . route('admin.pos.debitors.invoices.print', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary">
                            Print
                        </a>
                        '
                ];
            });
            $grandPieces = $invoices->sum('total_pieces');
            $grandWeight = $invoices->sum('total_weight');
            $grandPercentageCharge = $footer['percentage_charge_sum'];
            $grandAmount = $invoices->sum('total_amount');

            return response()->json([
                'data' => $data,
                'grandTotals' => [
                    'pieces' => $grandPieces,
                    'weight' => number_format($grandWeight, 2),
                    'percentageCharge' => number_format($grandPercentageCharge, 2),
                    'amount' => number_format($grandAmount, 2),
                ]
            ]);
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
