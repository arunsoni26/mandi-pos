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
                    'invoice'       => '<span '.$rowStyle.'>' . invoiceNumber($row) . '</span>',
                    'invoice_date'  => '<span '.$rowStyle.'>' . \Carbon\Carbon::parse($row->invoice_date)->format('d M Y') . '</span>',
                    'creditor_name' => '<span '.$rowStyle.'>' . ($row->creditor->name ?? '-') . '</span>',
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

            return response()->json(['data' => $data]);
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
