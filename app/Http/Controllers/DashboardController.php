<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // === STATS CARDS DATA ===

        // Total Produk
        $totalProducts = Product::count();
        $totalProductsLastMonth = Product::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $productGrowth = $totalProductsLastMonth > 0
            ? (($totalProducts - $totalProductsLastMonth) / $totalProductsLastMonth) * 100
            : 0;

        // Total Customer
        $totalCustomers = Customer::count();
        $totalCustomersLastMonth = Customer::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $customerGrowth = $totalCustomersLastMonth > 0
            ? (($totalCustomers - $totalCustomersLastMonth) / $totalCustomersLastMonth) * 100
            : 0;

        // Total Invoice
        $totalInvoices = Invoice::count();
        $totalInvoicesLastMonth = Invoice::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $invoiceGrowth = $totalInvoicesLastMonth > 0
            ? (($totalInvoices - $totalInvoicesLastMonth) / $totalInvoicesLastMonth) * 100
            : 0;

        $totalRevenue = Invoice::where('status_pembayaran', 'lunas')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('grand_total');

        $totalRevenueLastMonth = Invoice::where('status_pembayaran', 'lunas')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('grand_total');

        $revenueGrowth = $totalRevenueLastMonth > 0
            ? (($totalRevenue - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100
            : 0;
        // === CHARTS DATA ===

        // Revenue Chart - 6 Bulan Terakhir
        $revenueChartData = $this->getRevenueChartData();

        // Invoice Status Chart
        $invoiceStatusData = $this->getInvoiceStatusData();

        // === RECENT INVOICES & TOP CUSTOMERS ===

        // Recent Invoices - 5 Terbaru
        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top Customers - 5 Teratas berdasarkan point terbanyak
        $topCustomers = Customer::orderBy('point', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalProducts',
            'productGrowth',
            'totalCustomers',
            'customerGrowth',
            'totalInvoices',
            'invoiceGrowth',
            'totalRevenue',
            'revenueGrowth',
            'revenueChartData',
            'invoiceStatusData',
            'recentInvoices',
            'topCustomers'
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

            $revenue = Invoice::where('status_pembayaran', 'lunas')
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
            $lunas = Invoice::where('status_pembayaran', 'lunas')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $pending = Invoice::where('status_pembayaran', 'belum lunas')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $cancel = Invoice::where('status_pembayaran', 'cancel')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            return [
                'lunas' => $lunas,
                'pending' => $pending,
                'cancel' => $cancel
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
