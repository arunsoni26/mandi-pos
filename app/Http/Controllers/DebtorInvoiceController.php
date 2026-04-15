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

            $query = DebtorInvoice::with([
                    'debitor',
                    'downloads.user'
                ])
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

                $historyBtn = '
                        <button 
                            class="btn btn-sm btn-info view-history"
                            data-history=\''.json_encode($row->downloads).'\'
                        >
                            <i class="bi bi-clock-history"></i> '.$row->downloads->count().'
                        </button>
                    ';

                if(!in_array(auth()->user()->role_id, [1,2])) {
                    $historyBtn = '';
                }

                return [
                    'invoice'           => str_replace('INV', 'INVD', invoiceNumber($row)),
                    'invoice_date'      => \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    'debitor_name'      => $row->debitor->name ?? '-',
                    'pieces'            => $row->total_pieces ?? 0,
                    'amount'            => number_format($row->total_amount ?? 0, 2),
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
                    'total'      => number_format(($row->total_amount + $percentageCharge) ?? 0, 2),
                    'actions'           => '
                        <a href="' . route('admin.pos.debitors.invoices.print', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary">
                            Print
                        </a>
                        '.$historyBtn.'
                        '
                ];
            });
            $grandPieces = $invoices->sum('total_pieces');
            $totalAmount = $invoices->sum('total_amount');
            $grandPercentageCharge = $footer['percentage_charge_sum'];
            $grandTotal = $invoices->sum('total_amount') + $grandPercentageCharge;

            return response()->json([
                'data' => $data,
                'grandTotals' => [
                    'pieces' => $grandPieces,
                    'amount' => number_format($totalAmount, 2),
                    'percentageCharge' => number_format($grandPercentageCharge, 2),
                    'grandTotal' => number_format($grandTotal, 2),
                ]
            ]);
        }

        return view('admin.pos.debitors.invoices', compact('date'));
    }

    public function export(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $query = DebtorInvoice::with('debitor')
            ->withSum('items as total_pieces', 'pieces')
            ->withSum('items as total_weight', 'weight')
            ->withSum('items as total_amount', 'total')
            ->whereDate('invoice_date', $date);

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();

        $fileName = "debtor_invoices_{$date}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($invoices, $date) {
            $handle = fopen('php://output', 'w');
            $formattedDate = \Carbon\Carbon::parse($date)->format('d M Y');

            // ✅ Top header row
            fputcsv($handle, ["Debtor Invoice - {$formattedDate}"]);

            // Optional: empty row for spacing
            fputcsv($handle, []);

            // ✅ Column headings
            fputcsv($handle, [
                'Invoice',
                'Date',
                'Debitor Name',
                'Pieces',
                'Amount',
                'Percentage (%)',
                'Percentage Charge',
                'Total'
            ]);

            $grandPieces = 0;
            $totalAmount = 0;
            $totalPercentageCharge = 0;

            foreach ($invoices as $row) {

                $itemsAmount = $row->total_amount ?? 0;
                $percentage = $row->inv_percentage ?? 0;

                $percentageCharge = ($itemsAmount * $percentage) / 100;
                $total = $itemsAmount + $percentageCharge;

                // ✅ Write row
                fputcsv($handle, [
                    str_replace('INV', 'INVD', invoiceNumber($row)),
                    \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    $row->debitor->name ?? '-',
                    $row->total_pieces ?? 0,
                    number_format($itemsAmount, 2),
                    $percentage,
                    number_format($percentageCharge, 2),
                    number_format($total, 2),
                ]);

                // ✅ Totals
                $grandPieces += $row->total_pieces ?? 0;
                $totalAmount += $itemsAmount;
                $totalPercentageCharge += $percentageCharge;
            }

            // ✅ Footer row
            fputcsv($handle, []); // empty line
            fputcsv($handle, [
                'TOTAL',
                '',
                '',
                $grandPieces,
                number_format($totalAmount, 2),
                '',
                number_format($totalPercentageCharge, 2),
                number_format($totalAmount + $totalPercentageCharge, 2),
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(DebtorInvoice $invoice)
    {
        $user = auth()->user();

        // ✅ Check if employee already downloaded
        if ($user->role_id == 3) {

            $alreadyDownloaded = $invoice->downloads()
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyDownloaded) {
                return '<script>
                    alert("You can only download this invoice once.");
                    window.close();
                </script>';
            }
        }

        // ✅ Save download history (for ALL roles)
        $invoice->downloads()->create([
            'user_id' => $user->id,
            'downloaded_at' => now(),
        ]);

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
