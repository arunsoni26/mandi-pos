<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\CreditorInvoice;
use App\Models\CreditorInvoiceItem;
use App\Models\DebtorInvoice;
use App\Models\DebtorInvoiceItem;

class POSController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.pos.pos');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $cart = collect($request->cart);
            $today = now()->toDateString();

            /* ===============================
               DAILY CREDITOR INVOICE
            =============================== */

            $creditor = CreditorInvoice::firstOrCreate(
                ['invoice_date' => $today],
                ['total_amount' => 0, 'total_wage' => 0, 'grand_total' => 0]
            );

            foreach ($cart as $row) {
                CreditorInvoiceItem::create([
                    'creditor_invoice_id' => $creditor->id,
                    'product_name' => $row['product'],
                    'pieces' => $row['pieces'],
                    'weight' => $row['weight'],
                    'rate' => $row['rate'],
                    'total' => $row['total'],
                    'customer_name' => $row['customer'],
                ]);
            }

            $cTotals = CreditorInvoiceItem::where('creditor_invoice_id', $creditor->id)
                ->selectRaw('SUM(pieces) as pcs, SUM(total) as amount')
                ->first();

            $creditor->update([
                'total_amount' => $cTotals->amount,
                'total_wage' => $cTotals->pcs * env('WAGE_PER_PIECE'),
                'grand_total' => $cTotals->amount + ($cTotals->pcs * env('WAGE_PER_PIECE')),
            ]);

            /* ===============================
               DAILY DEBTOR INVOICES
            =============================== */

            $groupedCustomers = $cart->groupBy('customer');

            foreach ($groupedCustomers as $customer => $items) {

                $debtor = DebtorInvoice::firstOrCreate(
                    ['customer_name' => $customer, 'invoice_date' => $today],
                    ['total_amount' => 0, 'total_wage' => 0, 'grand_total' => 0]
                );

                foreach ($items as $item) {
                    DebtorInvoiceItem::create([
                        'debtor_invoice_id' => $debtor->id,
                        'product_name' => $item['product'],
                        'pieces' => $item['pieces'],
                        'weight' => $item['weight'],
                        'rate' => $item['rate'],
                        'total' => $item['total'],
                    ]);
                }

                $dTotals = DebtorInvoiceItem::where('debtor_invoice_id', $debtor->id)
                    ->selectRaw('SUM(pieces) as pcs, SUM(total) as amount')
                    ->first();

                $debtor->update([
                    'total_amount' => $dTotals->amount,
                    'total_wage' => $dTotals->pcs * env('WAGE_PER_PIECE'),
                    'grand_total' => $dTotals->amount + ($dTotals->pcs * env('WAGE_PER_PIECE')),
                ]);
            }
        });

        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'message' => 'Invoice Saved'
            ]
        );
    }
}
