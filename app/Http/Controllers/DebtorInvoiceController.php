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
                return [
                    'invoice'        => invoiceNumber($row),
                    'invoice_date'   => \Carbon\Carbon::parse($row->invoice_date)->format('d M Y'),
                    'debitor_name'  => $row->debitor->name ?? '-',
                    'actions'        => '
                    <a href="' . route('admin.pos.debitors.invoices.print', $row->id) . '" 
                       class="btn btn-sm btn-secondary">
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
}
