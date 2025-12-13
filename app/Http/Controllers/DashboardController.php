<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get current month and previous month
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Total Products
        $totalProducts = Product::count();
        $previousMonthProducts = Product::where('created_at', '<', $currentMonth)->count();
        $productGrowth = $previousMonthProducts > 0
            ? (($totalProducts - $previousMonthProducts) / $previousMonthProducts) * 100
            : 0;

        // Total Customers
        $totalCustomers = Customer::count();
        $previousMonthCustomers = Customer::where('created_at', '<', $currentMonth)->count();
        $customerGrowth = $previousMonthCustomers > 0
            ? (($totalCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100
            : 0;

        // Total Invoices
        $totalInvoices = Invoice::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $previousMonthInvoices = Invoice::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $invoiceGrowth = $previousMonthInvoices > 0
            ? (($totalInvoices - $previousMonthInvoices) / $previousMonthInvoices) * 100
            : 0;

        // Total Paid (All Time) - FIXED
        $paidInvoices = Invoice::where('status_pembayaran', 'paid')
            ->get();

        $totalPaid = $paidInvoices->sum('grand_total');
        $paidCount = $paidInvoices->count();

        // Recent Invoices
        $recentInvoices = Invoice::with('customer')
            ->latest()
            ->limit(5)
            ->get();

        // Top Customers by Points
        $topCustomers = Customer::orderBy('point', 'DESC')
            ->limit(5)
            ->get();

        // Top Customers Chart Data
        $topCustomersChart = [
            'labels' => $topCustomers->pluck('nama_customer')->toArray(),
            'points' => $topCustomers->pluck('point')->toArray(),
        ];

        // Invoice Status Data (All Time) - FIXED
        $invoiceStatusData = [
            'paid' => Invoice::where('status_pembayaran', 'paid')->count(),
            'unpaid' => Invoice::where('status_pembayaran', 'unpaid')->count(),
            'overdue' => Invoice::where('status_pembayaran', 'overdue')->count(),
            'cancelled' => Invoice::where('status_pembayaran', 'cancelled')->count(),
        ];

        // Top 5 Produk Terlaris (All Time) - FIXED
        $topProducts = InvoiceItem::select(
                'invoice_items.product_id',
                'products.nama_produk',
                DB::raw('SUM(invoice_items.quantity) as total_quantity')
            )
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('invoices', function($join) {
                $join->on('invoice_items.invoice_id', '=', 'invoices.id')
                     ->where('invoices.status_pembayaran', '!=', 'cancelled');
            })
            ->groupBy('invoice_items.product_id', 'products.nama_produk')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Format data untuk chart
        $topProductsData = [
            'labels' => $topProducts->pluck('nama_produk')->toArray(),
            'quantities' => $topProducts->pluck('total_quantity')->toArray(),
        ];

        return view('dashboard', compact(
            'user',
            'totalProducts',
            'totalCustomers',
            'totalInvoices',
            'totalPaid',
            'paidCount',
            'productGrowth',
            'customerGrowth',
            'invoiceGrowth',
            'recentInvoices',
            'topCustomers',
            'topCustomersChart',
            'invoiceStatusData',
            'topProductsData'
        ));
    }
    /**
     * Get Revenue Chart Data for last 6 months
     */
    private function getRevenueChartData()
    {
        $months = [];
        $pendapatan = [];
        $pengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $period = $date->format('Y-m');

            $months[] = $date->format('M');

            // FIX: Ganti 'lunas' jadi 'paid'
            $revenue = Invoice::where('status_pembayaran', 'paid')
                ->whereRaw("DATE_FORMAT(tanggal_invoice, '%Y-%m') = ?", [$period])
                ->sum('grand_total');
            $pendapatan[] = $revenue;

            $expense = \App\Models\FinanceRecord::where('tipe', 'expense')
                ->where('periode', $period)
                ->sum('jumlah');
            $pengeluaran[] = $expense;
        }

        return [
            'labels' => $months,
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaran
        ];
    }

    /**
     * Get Invoice Status Data for current month - DATA ASLI
     */
    private function getInvoiceStatusData()
    {
        $paid = Invoice::where('status_pembayaran', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $unpaid = Invoice::where('status_pembayaran', 'unpaid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $overdue = Invoice::where('status_pembayaran', 'overdue')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // FIX: Ganti 'canceled' jadi 'cancelled'
        $cancelled = Invoice::where('status_pembayaran', 'cancelled')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return [
            'paid' => $paid,
            'unpaid' => $unpaid,
            'overdue' => $overdue,
            'cancelled' => $cancelled
        ];
    }
    private function getTopProducts()
    {
        $topProducts = \App\Models\InvoiceItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        return $topProducts;
    }

    /**
     * Get Top 5 Products Data for Chart
     */
    private function getTopProductsData()
    {
        // Query dengan join langsung (lebih reliable)
        $topProducts = \App\Models\InvoiceItem::selectRaw('
                invoice_items.product_id,
                products.nama_produk,
                SUM(invoice_items.quantity) as total_sold
            ')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->groupBy('invoice_items.product_id', 'products.nama_produk')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $labels = [];
        $quantities = [];

        foreach ($topProducts as $item) {
            $labels[] = $item->nama_produk ?? 'Unknown';
            $quantities[] = (int) $item->total_sold;
        }

        // Debug: Log data ke laravel.log
        \Log::info('Top Products Data:', [
            'labels' => $labels,
            'quantities' => $quantities,
            'raw_data' => $topProducts->toArray()
        ]);

        return [
            'labels' => $labels,
            'quantities' => $quantities
        ];
    }
    private function getTopCustomersChartData()
    {
        $customers = Customer::orderBy('point', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $points = [];

        foreach ($customers as $customer) {
            $labels[] = $customer->nama_customer;
            $points[] = $customer->point ?? 0;
        }

        return [
            'labels' => $labels,
            'points' => $points
        ];
    }
}
