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
                ->whereDate('invoice_date', $date);

            // Optional filter
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $invoices = $query->get();

            $data = $invoices->map(function ($row) {
                return [
                    'invoice'        => invoiceNumber($row),
                    'invoice_date'   => \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    'creditor_name'  => $row->creditor->name ?? '-',
                    'actions'        => '
                    <a href="' . route('admin.pos.creditors.invoices.print', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary">
                        Print
                    </a>
                '
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
