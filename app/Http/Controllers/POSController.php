<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        dd($request->all());
        DB::transaction(function () use ($request) {

            $today = now()->toDateString();

            /* -----------------------------
            | 1️⃣ Resolve Creditor
            |----------------------------- */
            $creditor = Customer::firstOrCreate(
                [
                    'name' => $request->creditor_id,
                    'customer_type' => "Raw Creditor" // Active / Raw
                ],
                ['status' => 'active']
            );

            /* -----------------------------
            | 2️⃣ Creditor Daily Invoice
            |----------------------------- */
            $creditorInvoice = CreditorInvoice::firstOrCreate(
                [
                    'creditor_id' => $creditor->id,
                    'invoice_date' => $today
                ],
                [
                    'total_amount' => 0,
                    'total_wage' => 0,
                    'grand_total' => 0
                ]
            );

            $creditorTotal = 0;
            $creditorPieces = 0;

            /* -----------------------------
            | 3️⃣ Loop Cart Rows
            |----------------------------- */
            foreach ($request->cart as $row) {

                if (empty($row['product']) || empty($row['customer'])) {
                    continue;
                }

                /* -----------------------------
                | 4️⃣ Resolve Debtor
                |----------------------------- */
                $debtor = Customer::firstOrCreate(
                    [
                        'name' => $row['customer'],
                        'customer_type' => 'debtor'
                    ],
                    ['status' => 'active']
                );

                /* -----------------------------
                | 5️⃣ Debtor Daily Invoice
                |  (ONLY debtor + date)
                |----------------------------- */
                $debtorInvoice = DebtorInvoice::firstOrCreate(
                    [
                        'debtor_customer_id' => $debtor->id,
                        'invoice_date' => $today
                    ],
                    [
                        'total_amount' => 0,
                        'total_wage' => 0,
                        'grand_total' => 0
                    ]
                );

                $pieces = (int) $row['piece'];
                $rate   = (float) $row['rate'];
                $total  = $pieces * $rate;
                $wage   = $pieces * 9;

                /* -----------------------------
                | 6️⃣ Save Creditor Item
                |----------------------------- */
                CreditorInvoiceItem::create([
                    'creditor_invoice_id' => $creditorInvoice->id,
                    'product_name' => $row['product'],
                    'pieces' => $pieces,
                    'weight' => $row['weight'],
                    'rate' => $rate,
                    'total' => $total,
                    'debtor_customer_id' => $debtor->id
                ]);

                /* -----------------------------
                | 7️⃣ Save Debtor Item
                |  (with creditor_id)
                |----------------------------- */
                DebtorInvoiceItem::create([
                    'debtor_invoice_id' => $debtorInvoice->id,
                    'creditor_id' => $creditor->id,
                    'product_name' => $row['product'],
                    'pieces' => $pieces,
                    'weight' => $row['weight'],
                    'rate' => $rate,
                    'total' => $total
                ]);

                /* -----------------------------
                | 8️⃣ Accumulate Totals
                |----------------------------- */
                $creditorTotal += $total;
                $creditorPieces += $pieces;

                $debtorInvoice->increment('total_amount', $total);
                $debtorInvoice->increment('total_wage', $wage);
            }

            /* -----------------------------
            | 9️⃣ Final Totals
            |----------------------------- */
            $creditorInvoice->update([
                'total_amount' => $creditorTotal,
                'total_wage' => $creditorPieces * 9,
                'grand_total' => $creditorTotal + ($creditorPieces * 9)
            ]);

            DebtorInvoice::where('invoice_date', $today)->each(function ($inv) {
                $inv->update([
                    'grand_total' => $inv->total_amount + $inv->total_wage
                ]);
            });
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Invoices saved successfully'
        ]);
    }

}
