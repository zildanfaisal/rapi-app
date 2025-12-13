@extends('layouts.app')

@section('title', __('Dashboard'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Dashboard') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-lg rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">Selamat Datang, {{ $user->name }}! 👋</h3>
                    <p class="text-indigo-100 mt-1">{{ $user->email }}</p>
                    <p class="text-sm text-indigo-100 mt-2">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Produk -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</p>
                    <p class="text-xs {{ $productGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $productGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                        </svg>
                        {{ $productGrowth >= 0 ? '+' : '' }}{{ number_format(abs($productGrowth), 1) }}% dari bulan lalu
                    </p>
                </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Customer -->
           <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Customer</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalCustomers) }}</p>
                        <p class="text-xs {{ $customerGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($customerGrowth >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            {{ $customerGrowth >= 0 ? '+' : '' }}{{ number_format(abs($customerGrowth), 1) }}% dari bulan lalu
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Invoice -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Invoice</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalInvoices) }}</p>
                        <p class="text-xs {{ $invoiceGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($invoiceGrowth >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            {{ $invoiceGrowth >= 0 ? '+' : '' }}{{ number_format(abs($invoiceGrowth), 1) }}% dari bulan lalu
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pendapatan Bulan Ini -->
             <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">
                            Rp {{ number_format($totalRevenue / 1000000, 1) }}M
                        </p>
                        <p class="text-xs {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($revenueGrowth >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format(abs($revenueGrowth), 1) }}% dari bulan lalu
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Top Customers Chart -->
             <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Top 5 Customer</h3>
                    <div class="text-xs text-gray-500">Berdasarkan Point Terbanyak</div>
                </div>
                <div style="height: 300px;">
                    <canvas id="topCustomersChart"></canvas>
                </div>
            </div>

            <!-- Invoice Status Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Status Invoice</h3>
                    <div class="text-xs text-gray-500">Bulan Ini</div>
                </div>
                <div style="height: 300px;">
                    <canvas id="invoiceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Top Customers -->
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Invoice Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentInvoices as $invoice)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-blue-600">INV</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $invoice->invoice_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $invoice->customer->nama_customer ?? 'Customer' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                                    @if($invoice->status_pembayaran == 'lunas')
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full mt-1">Lunas</span>
                                    @elseif($invoice->status_pembayaran == 'belum lunas')
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full mt-1">Pending</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full mt-1">Cancel</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>Belum ada invoice</p>
                        </div>
                    @endforelse
                </div>
                @if($recentInvoices->count() > 0)
                <div class="p-4 border-t border-gray-100">
                    <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat Semua Invoice →
                    </a>
                </div>
                @endif
            </div>

            <!-- Top Customers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Customer Terbaik</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($topCustomers as $index => $customer)
                        @php
                            $gradients = [
                                'from-blue-500 to-purple-500',
                                'from-purple-500 to-pink-500',
                                'from-pink-500 to-red-500',
                                'from-orange-500 to-yellow-500',
                                'from-green-500 to-teal-500'
                            ];
                            $gradient = $gradients[$index] ?? 'from-gray-500 to-gray-600';
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br {{ $gradient }} rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $customer->nama_customer }}</p>
                                        <p class="text-sm text-gray-500">{{ $customer->no_hp ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-yellow-600">
                                        {{ number_format($customer->point ?? 0) }} pts
                                    </p>
                                    <p class="text-xs text-gray-500">Total Point</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p>Belum ada data customer</p>
                        </div>
                    @endforelse
                </div>
                @if($topCustomers->count() > 0)
                <div class="p-4 border-t border-gray-100">
                    <a href="{{ route('customers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat Semua Customer →
                    </a>
                </div>
                @endif
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <!-- Tambah Invoice -->
                @can('finance.input.create')
                <a href="{{ route('invoices.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Invoice</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-4 rounded-lg opacity-50 cursor-not-allowed">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-400">Tambah Invoice</span>
                </div>
                @endcan

                <!-- Tambah Customer -->
                @can('customers.create')
                <a href="{{ route('customers.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Customer</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-4 rounded-lg opacity-50 cursor-not-allowed">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-400">Tambah Customer</span>
                </div>
                @endcan

                <!-- Tambah Produk -->
                @can('products.create')
                <a href="{{ route('products.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Produk</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-4 rounded-lg opacity-50 cursor-not-allowed">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-400">Tambah Produk</span>
                </div>
                @endcan

                <!-- Lihat Laporan Keuangan -->
                @can('finance.history')
                <a href="{{ route('finance-records.history') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Laporan Keuangan</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-4 rounded-lg opacity-50 cursor-not-allowed">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-400">Laporan Keuangan</span>
                </div>
                @endcan

            </div>
        </div>

    </div>
</div>
@endsection


@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Top Customers Chart - Bar Chart
    const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');

    @php
        $customerLabels = $topCustomersChart['labels'] ?? [];
        $customerPoints = $topCustomersChart['points'] ?? [];
    @endphp

    new Chart(topCustomersCtx, {
        type: 'bar',
        data: {
            labels: @json($customerLabels),
            datasets: [{
                label: 'Total Point',
                data: @json($customerPoints),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderColor: [
                    'rgb(99, 102, 241)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                    'rgb(251, 146, 60)',
                    'rgb(34, 197, 94)'
                ],
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Point: ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Invoice Status Chart - Doughnut Chart
    const invoiceCtx = document.getElementById('invoiceChart').getContext('2d');

    @php
        $invoiceLunas = $invoiceStatusData['lunas'] ?? 0;
        $invoicePending = $invoiceStatusData['pending'] ?? 0;
        $invoiceCancel = $invoiceStatusData['cancel'] ?? 0;
        $totalInvoiceChart = $invoiceLunas + $invoicePending + $invoiceCancel;
    @endphp

    new Chart(invoiceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Pending', 'Cancel'],
            datasets: [{
                data: [
                    {{ $invoiceLunas }},
                    {{ $invoicePending }},
                    {{ $invoiceCancel }}
                ],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' invoice';

                            // Hitung persentase
                            const total = {{ $totalInvoiceChart }};
                            if (total > 0) {
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                label += ' (' + percentage + '%)';
                            }

                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
