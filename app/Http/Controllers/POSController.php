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
        $invoiceData = [];

        $today = now()->format('Y-m-d');
        $creditorType = $request->creditorType ?? 'Raw Creditor';

        $additionalCharge = $request->additionalCharge;

        /* 1️⃣ Resolve Creditor */
        $creditor = Customer::where('id', (int) $request->creditorId)
            ->firstOrFail();
        
        // dd($creditor->id);

        if ($creditor && isset($creditor->id)) {
            /* 2️⃣ Creditor Daily Invoice */
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
            
            /* 🔥 DELETE OLD ITEMS (VERY IMPORTANT) */
            CreditorInvoiceItem::where('creditor_invoice_id', $creditorInvoice->id)->delete();

            $debtorInvoiceIds = DebtorInvoice::where([
                'creditor_id' => $creditor->id,
                'invoice_date' => $today
            ])->pluck('id');

            // dd($debtorInvoiceIds);

            DebtorInvoiceItem::whereIn('debtor_invoice_id', $debtorInvoiceIds)->delete();

            /* 🔄 Reset totals */
            $creditorInvoice->update([
                'total_amount' => 0,
                'total_wage' => 0,
                'grand_total' => 0
            ]);

            $creditorTotal = 0;
            $creditorPieces = 0;

            foreach ($request->cart as $row) {

                if (empty($row['product']) || empty($row['debtor_customer_id'])) {
                    continue;
                }

                /* Resolve Debtor */
                if (is_numeric((string) $row['debtor_customer_id'])) {
                    $debtor = Customer::findOrFail((int)$row['debtor_customer_id']);
                } else {
                    $debtor = Customer::where('name', trim($row['debtor_customer_id']))
                        ->where('customer_type', 'Debitor')
                        ->first();
                    if (!isset($debtor->id) && !$debtor) {
                        $debtor = Customer::create([
                            'name' => trim($row['debtor_customer_id']),
                            'customer_type' => 'Debitor'
                        ]);
                    }
                }

                if ($debtor && isset($debtor->id)) {
                    /* Debtor Invoice (Daily) */
                    $debtorInvoice = DebtorInvoice::firstOrCreate(
                        [
                            'debtor_customer_id' => $debtor->id,
                            'invoice_date' => $today
                        ],
                        [
                            'total_amount' => 0,
                            'total_wage' => 0,
                            'grand_total' => 0,
                            'additional_charges' => 0
                        ]
                    );
                    $debtorInvoice->creditor_id = $creditor->id;
                    $debtorInvoice->save();
                    // dd( $debtorInvoice );

                    $pieces = (int) $row['pieces'];
                    $weight = (float) $row['weight'];
                    $rate   = (float) $row['rate'];
                    $total  = $weight * $rate;
                    $wage   = $pieces * 9;

                    CreditorInvoiceItem::create([
                        'creditor_invoice_id' => $creditorInvoice->id,
                        'product_name' => $row['product'],
                        'pieces' => $pieces,
                        'weight' => $row['weight'],
                        'rate' => $rate,
                        'total' => $total,
                        'debtor_customer_id' => $debtor->id
                    ]);

                    DebtorInvoiceItem::create([
                        'debtor_invoice_id' => $debtorInvoice->id,
                        // 'creditor_id' => $creditor->id,
                        'product_name' => $row['product'],
                        'pieces' => $pieces,
                        'weight' => $row['weight'],
                        'rate' => $rate,
                        'total' => $total
                    ]);
                        
                    /* 🔥 Collect data for frontend invoice */
                    $invoiceData['items'][] = [
                        'product'       => $row['product'],
                        'pieces'        => $pieces,
                        'weight'        => $row['weight'],
                        'rate'          => $rate,
                        'total'         => $total,
                        'wage'          => $wage,
                        'debtor_id'     => $debtor->id,
                        'debtor_name'   => $debtor->name,
                        'creditor_name' => $creditor->name
                    ];

                    $creditorTotal += $total;
                    $creditorPieces += $pieces;

                    $debtorInvoice->increment('total_amount', $total);
                    $debtorInvoice->increment('total_wage', $wage);
                }
            }

            $creditorInvoice->update(attributes: [
                'total_amount' => $creditorTotal,
                'total_wage' => $creditorPieces * 9,
                'additional_charges' => $additionalCharge,
                'grand_total' => $creditorTotal - ($creditorPieces * 9) - $additionalCharge
            ]);

            DebtorInvoice::where('invoice_date', $today)->each(function ($inv) {
                $inv->update([
                    'grand_total' => $inv->total_amount
                ]);
            });
                
            /* Invoice summary */
            $invoiceData['summary'] = [
                'creditor_name' => $creditor->name,
                'invoice_date'  => $today,
                'total_amount'  => $creditorTotal,
                'total_wage'    => $creditorPieces * 9,
                'grand_total'   => $creditorTotal - ($creditorPieces * 9)
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Invoices saved successfully',
                'invoice' => $invoiceData,
                'creditor_invoice_url' => route('admin.pos.creditors.invoices.print', $creditorInvoice->id)
            ]);
        }
    }

    public function loadTodayInvoice($creditorId)
    {
        $today = now()->toDateString();

        $invoice = CreditorInvoice::where([
            'creditor_id' => $creditorId,
            'invoice_date' => $today
        ])->first();

        if (!$invoice) {
            return response()->json([
                'exists' => false,
                'cart' => []
            ]);
        }

        $items = CreditorInvoiceItem::with('debtorCustomer')
            ->where('creditor_invoice_id', $invoice->id)
            ->get();

        $cart = [];
        $rowId = 0;
        foreach ($items as $ikey => $item) {
            $rowId = $ikey;

            $cart[$rowId] = [
                'product' => $item->product_name,
                'pieces' => $item->pieces,
                'weight' => $item->weight,
                'rate' => $item->rate,
                'total' => $item->total,
                'debtor_customer_id' => $item->debtor_customer_id,
                'debtor_customer_name' => $item->debtorCustomer->name
            ];
        }

        // dd($cart);

        return response()->json([
            'exists' => true,
            'cart' => $cart,
            'rowId' => $rowId
        ]);
    }
}
