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

            $query = CreditorInvoice::with('creditor')
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
                            Cancel
                        </a>
                    ';

                return [
                    'invoice'       => '<span '.$rowStyle.'>' . str_replace('INV', 'INVC', invoiceNumber($row)) . '</span>',
                    'invoice_date'  => '<span '.$rowStyle.'>' . \Carbon\Carbon::parse($row->invoice_date)->format('d M Y') . '</span>',
                    'creditor_name' => '<span '.$rowStyle.'>' . ($row->creditor->name ?? '-') . '</span>',
                    'pieces'        => '<span '.$rowStyle.'>' . ($row->total_pieces ?? 0) . '</span>',
                    'wages'        => '<span '.$rowStyle.'>' . number_format($row->total_wage ?? 0, 2) . '</span>',
                    'amount'        => '<span '.$rowStyle.'>' . number_format($row->total_amount ?? 0, 2) . '</span>',
                    'total'        => '<span '.$rowStyle.'>' . number_format($row->grand_total ?? 0, 2) . '</span>',
                    'status'        => $statusBadge,
                    'actions'       => '
                        <a href="' . route('admin.pos.creditors.invoices.print', $row->id) . '"
                        target="_blank"
                        class="btn btn-sm btn-secondary">
                            Print
                        </a>
                        ' . $cancelBtn . '
                    ',
                ];
            });
            $grandPieces = $invoices->sum('total_pieces');
            $grandWage = $invoices->sum('total_wage');
            $grandAmount = $invoices->sum('total_amount');
            $grandTotal = $invoices->sum('grand_total');

            return response()->json([
                'data' => $data,
                'grandTotals' => [
                    'pieces' => $grandPieces,
                    'wages' => number_format($grandWage, 2),
                    'amount' => number_format($grandAmount, 2),
                    'grandTotal' => number_format($grandTotal, 2),
                ]
            ]);
        }

        return view('admin.pos.creditors.invoices', compact('date'));
    }

    public function print(CreditorInvoice $invoice)
    {
        $invoice->load([
            'creditor',
            'items.debtorCustomer'
        ]);

        return view('admin.pos.creditors.print', compact('invoice'));
    }
}
