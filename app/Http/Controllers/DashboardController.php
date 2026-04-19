<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
        $customers = Customer::all();
        $users = User::all();

        // Default: current year
        $year = $request->year ?? date('Y');

        // ✅ Total Sales & Pieces
        $totals = DB::table('creditor_invoice_items')
            ->select(
                DB::raw('SUM(total) as total_sales'),
                DB::raw('SUM(pieces) as total_pieces')
            )
            ->whereNull('deleted_at')
            ->first();

        // ✅ Total Creditors
        $totalCreditors = DB::table('customers')
            ->whereIn('customer_type', ['Active Creditor', 'Raw Creditor'])
            ->count();

        // ✅ Total Debtors
        $totalDebtors = DB::table('customers')
            ->where('customer_type', 'debitor')
            ->count();

        // ✅ Monthly Sales Trend
        $monthlySales = DB::table('creditor_invoices as ci')
            ->join('creditor_invoice_items as cii', 'cii.creditor_invoice_id', '=', 'ci.id')
            ->select(
                DB::raw('MONTH(ci.invoice_date) as month'),
                DB::raw('SUM(cii.total) as total')
            )
            ->whereYear('ci.invoice_date', $year)
            ->whereNull('ci.deleted_at')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Format for 12 months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $monthlySales[$i] ?? 0;
        }

        // ✅ Top Creditors
        $topCreditors = DB::table('creditor_invoices as ci')
            ->join('customers as c', 'c.id', '=', 'ci.creditor_id')
            ->join('creditor_invoice_items as cii', 'cii.creditor_invoice_id', '=', 'ci.id')
            ->select('c.name', DB::raw('SUM(cii.total) as total'))
            ->whereNull('ci.deleted_at')
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ✅ Product Distribution
        $products = DB::table('creditor_invoice_items')
            ->select('product_name', DB::raw('SUM(total) as total'))
            ->whereNull('deleted_at')
            ->groupBy('product_name')
            ->get();
            
        return view('admin.dashboard', [
            'customers' => $customers,
            'users' => $users,
            'totalSales' => $totals->total_sales ?? 0,
            'totalPieces' => $totals->total_pieces ?? 0,
            'totalCreditors' => $totalCreditors,
            'totalDebtors' => $totalDebtors,
            'monthlySales' => $months,
            'topCreditors' => $topCreditors,
            'products' => $products,
            'year' => $year
        ]);
    }
}
