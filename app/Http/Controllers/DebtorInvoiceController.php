<?php

namespace App\Http\Controllers;

use App\Models\DebtorInvoice;
use Illuminate\Http\Request;

class DebtorInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $invoices = DebtorInvoice::with('debitor')
            ->where('invoice_date', $date)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.pos.debitors.invoices', compact(
            'invoices',
            'date'
        ));
    }

    public function print(DebtorInvoice $invoice)
    {
        $invoice->load([
            'debitor'
        ]);

        return view('admin.pos.debitors.print', compact('invoice'));
    }
}
