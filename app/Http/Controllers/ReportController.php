<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CreditorInvoiceItem;
use App\Models\DebtorInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREDITOR REPORT PAGE + AJAX
    |--------------------------------------------------------------------------
    */
    public function creditorWiseReport(Request $request)
    {
        // ✅ Return Blade page on normal request
        if (!$request->ajax()) {
            return view('admin.reports.creditor-wise');
        }

        $query = DB::table('creditor_invoices as ci')
            ->join('customers as c', 'c.id', '=', 'ci.creditor_id')
            ->join('creditor_invoice_items as cii', 'cii.creditor_invoice_id', '=', 'ci.id')
            ->select(
                'ci.creditor_id',
                'c.name as creditor_name',
                DB::raw('SUM(cii.pieces) as total_pieces'),
                DB::raw('SUM(cii.total) as total_invoice_amount')
            )
            ->whereIn('c.customer_type', ['Active Creditor', 'Raw Creditor'])
            ->whereNull('ci.deleted_at')
            ->whereNull('cii.deleted_at');

        // ✅ Date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);

            if (count($dates) === 2) {
                $query->whereBetween('ci.invoice_date', [$dates[0], $dates[1]]);
            }
        }

        $reports = $query
            ->groupBy('ci.creditor_id', 'c.name')
            ->orderBy('c.name')
            ->get();

        return response()->json([
            'data' => $reports,
            'grandTotals' => [
                'total_pieces' => $reports->sum('total_pieces'),
                'total_amount' => number_format($reports->sum('total_invoice_amount'), 2)
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREDITOR EXPORT CSV
    |--------------------------------------------------------------------------
    */
    public function exportCreditorWise(Request $request)
    {
        $query = DB::table('creditor_invoices as ci')
            ->join('customers as c', 'c.id', '=', 'ci.creditor_id')
            ->join('creditor_invoice_items as cii', 'cii.creditor_invoice_id', '=', 'ci.id')
            ->select(
                'c.name as creditor_name',
                DB::raw('SUM(cii.pieces) as total_pieces'),
                DB::raw('SUM(cii.total) as total_invoice_amount')
            )
            ->whereIn('c.customer_type', ['Active Creditor', 'Raw Creditor'])
            ->whereNull('ci.deleted_at')
            ->whereNull('cii.deleted_at');

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('ci.invoice_date', [$dates[0], $dates[1]]);
            }
        }

        $data = $query->groupBy('c.id', 'c.name')->get();

        $filename = "creditor_report.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Creditor Name', 'Total Pieces', 'Total Amount']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->creditor_name,
                    $row->total_pieces,
                    $row->total_invoice_amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | DEBTOR REPORT PAGE + AJAX
    |--------------------------------------------------------------------------
    */
    public function debtorWiseReport(Request $request)
    {
        // ✅ Return Blade page
        if (!$request->ajax()) {
            return view('admin.reports.debtor-wise');
        }

        $query = DB::table('debtor_invoices as di')
            ->join('customers as c', 'c.id', '=', 'di.debtor_customer_id')
            ->join('debtor_invoice_items as dii', 'dii.debtor_invoice_id', '=', 'di.id')
            ->select(
                'di.debtor_customer_id',
                'c.name as debtor_name',
                DB::raw('SUM(dii.pieces) as total_pieces'),
                DB::raw('SUM(dii.total) as total_invoice_amount')
            )
            ->where('c.customer_type', 'debitor')
            ->whereNull('di.deleted_at')
            ->whereNull('dii.deleted_at');

        // ✅ Date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);

            if (count($dates) === 2) {
                $query->whereBetween('di.invoice_date', [$dates[0], $dates[1]]);
            }
        }

        $reports = $query
            ->groupBy('di.debtor_customer_id', 'c.name')
            ->orderBy('c.name')
            ->get();

        return response()->json([
            'data' => $reports,
            'grandTotals' => [
                'total_pieces' => $reports->sum('total_pieces'),
                'total_amount' => number_format($reports->sum('total_invoice_amount'), 2)
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DEBTOR EXPORT CSV
    |--------------------------------------------------------------------------
    */
    public function exportDebtorWise(Request $request)
    {
        $query = DB::table('debtor_invoices as di')
            ->join('customers as c', 'c.id', '=', 'di.debtor_customer_id')
            ->join('debtor_invoice_items as dii', 'dii.debtor_invoice_id', '=', 'di.id')
            ->select(
                'c.name as debtor_name',
                DB::raw('SUM(dii.pieces) as total_pieces'),
                DB::raw('SUM(dii.total) as total_invoice_amount')
            )
            ->where('c.customer_type', 'debitor')
            ->whereNull('di.deleted_at')
            ->whereNull('dii.deleted_at');

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('di.invoice_date', [$dates[0], $dates[1]]);
            }
        }

        $data = $query->groupBy('c.id', 'c.name')->get();

        $filename = "debtor_report.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Debtor Name', 'Total Pieces', 'Total Amount']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->debtor_name,
                    $row->total_pieces,
                    $row->total_invoice_amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}