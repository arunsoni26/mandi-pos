<?php

namespace App\Http\Controllers;

use App\Models\CreditorInvoice;
use Illuminate\Http\Request;

class CreditorInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        if ($request->ajax()) {

            $query = CreditorInvoice::with([
                    'creditor',
                    'downloads.user'
                ])
                ->withTrashed()
                ->withSum('items as total_pieces', 'pieces')
                ->withSum('items as total_weight', 'weight')
                ->withSum('items as total_amount', 'total')
                ->whereDate('invoice_date', $date);

            // Optional filter
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $invoices = $query->get();
            
            $data = $invoices->map(function ($row) {
                $isDeleted = !is_null($row->deleted_at);

                $deleteMsg = "'Are you sure you want to cancel this invoice?'";

                $rowStyle = $isDeleted
                    ? 'style="color:#dc3545; text-decoration: line-through;"'
                    : '';

                $statusBadge = $isDeleted
                    ? '<span class="badge bg-danger">Cancelled</span>'
                    : '<span class="badge bg-success">Active</span>';

                $cancelBtn = $isDeleted
                    ? '<button class="btn btn-sm btn-danger" disabled>Cancelled</button>'
                    : '
                        <a href="' . url('admin/pos/invoice/delete') . '/' . $row->id . '"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm(' . $deleteMsg . ')">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    ';

                $historyBtn = '
                        <button 
                            class="btn btn-sm btn-info view-history d-flex align-items-center flex-nowrap"
                            data-id="'.$row->id.'"
                            data-history=\''.json_encode($row->downloads).'\'
                        >
                            <i class="bi bi-clock-history me-1"></i> <span>'.$row->downloads->count().'</span>
                        </button>
                    ';

                $updateBtn = $isDeleted
                    ? ''
                    : '
                        <a href="' . url('admin/pos/edit-pos') . '/' . $row->id . '"
                        class="btn btn-sm btn-secondary">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    ';
                if(!in_array(auth()->user()->role_id, [1,2])) {
                    $updateBtn = '';
                    $historyBtn = '';
                    $cancelBtn = '';
                }
                

                return [
                    'invoice'       => '<span '.$rowStyle.'>' . str_replace('INV', 'INVC', invoiceNumber($row)) . '</span>',
                    'invoice_date'  => '<span '.$rowStyle.'>' . \Carbon\Carbon::parse($row->invoice_date)->format('d M Y') . '</span>',
                    'creditor_name' => '<span '.$rowStyle.'>' . ($row->creditor->name ?? '-') . '</span>',
                    'pieces'        => '<span '.$rowStyle.'>' . ($row->total_pieces ?? 0) . '</span>',
                    'wages'        => '<span '.$rowStyle.'>' . number_format($row->total_wage ?? 0, 2) . '</span>',
                    'amount'        => '<span '.$rowStyle.'>' . number_format($row->total_amount ?? 0, 2) . '</span>',
                    'additional_charges'        => '<span '.$rowStyle.'>' . number_format($row->additional_charges ?? 0, 2) . '</span>',
                    'total'        => '<span '.$rowStyle.'>' . number_format($row->grand_total ?? 0, 2) . '</span>',
                    'status'        => $statusBadge,
                    'actions'       => '
                        <div class="d-flex align-items-center flex-nowrap">
                            <a href="' . route('admin.pos.creditors.invoices.print', $row->id) . '"
                            target="_blank"
                            class="btn btn-sm btn-success">
                                <i class="bi bi-printer"></i>
                            </a>
                            ' . $updateBtn . '
                            ' . $historyBtn . '
                            ' . $cancelBtn . '
                        </div>
                    ',
                ];
            });
            
            $activeInvoices = $invoices->whereNull('deleted_at');

            $grandPieces = $activeInvoices->sum('total_pieces');
            $grandWage   = $activeInvoices->sum('total_wage');
            $grandAmount = $activeInvoices->sum('total_amount');
            $grandAdditionalCharges = $activeInvoices->sum('additional_charges');
            $grandTotal  = $activeInvoices->sum('grand_total');

            return response()->json([
                'data' => $data,
                'grandTotals' => [
                    'pieces' => $grandPieces,
                    'wages' => number_format($grandWage, 2),
                    'amount' => number_format($grandAmount, 2),
                    'additional_charges' => number_format($grandAdditionalCharges, 2),
                    'grandTotal' => number_format($grandTotal, 2),
                ]
            ]);
        }

        return view('admin.pos.creditors.invoices', compact('date'));
    }

    public function export(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $query = CreditorInvoice::with('creditor')
            // ->withTrashed()
            ->withSum('items as total_pieces', 'pieces')
            ->withSum('items as total_weight', 'weight')
            ->withSum('items as total_amount', 'total')
            ->whereDate('invoice_date', $date);

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();

        $fileName = "creditor_invoices_{$date}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($invoices, $date) {
            $file = fopen('php://output', 'w');
            // Title row (same number of columns)
            fputcsv($file, [
                'Creditor Invoice - ' . \Carbon\Carbon::parse(time: $date)->format('d M Y'),
                '', '', '', '', '', '', '', ''
            ]);

            // Empty row
            fputcsv($file, []);

            // 🔹 Header Row
            fputcsv($file, [
                'Invoice No',
                'Date',
                'Creditor',
                'Pieces',
                'Wages',
                'Amount',
                'Additional Charges',
                'Total',
                'Status'
            ]);

            foreach ($invoices as $row) {
                $isDeleted = !is_null($row->deleted_at);

                fputcsv($file, [
                    str_replace('INV', 'INVC', invoiceNumber($row)),
                    \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    $row->creditor->name ?? '-',
                    $row->total_pieces ?? 0,
                    number_format($row->total_wage ?? 0, 2),
                    number_format($row->total_amount ?? 0, 2),
                    number_format($row->additional_charges ?? 0, 2),
                    number_format($row->grand_total ?? 0, 2),
                    $isDeleted ? 'Cancelled' : 'Active',
                ]);
            }

            // 🔹 Grand Totals (only active invoices)
            $activeInvoices = $invoices->whereNull('deleted_at');

            fputcsv($file, []); // empty row

            fputcsv($file, [
                'TOTAL',
                '',
                '',
                $activeInvoices->sum('total_pieces'),
                number_format($activeInvoices->sum('total_wage'), 2),
                number_format($activeInvoices->sum('total_amount'), 2),
                number_format($activeInvoices->sum('additional_charges'), 2),
                number_format($activeInvoices->sum('grand_total'), 2),
                ''
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(CreditorInvoice $invoice)
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
            'creditor',
            'items.debtorCustomer'
        ]);

        return view('admin.pos.creditors.print', compact('invoice'));
    }
}
