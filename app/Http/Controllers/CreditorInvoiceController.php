<?php

namespace App\Http\Controllers;

use App\Models\CreditorInvoice;
use Illuminate\Http\Request;

class CreditorInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $invoices = CreditorInvoice::with('creditor')
            ->where('invoice_date', $date)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.pos.creditors.invoices', compact(
            'invoices',
            'date'
        ));
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
