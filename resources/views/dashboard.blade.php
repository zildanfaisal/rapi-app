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
                        <p class="text-3xl font-bold text-gray-800 mt-2">248</p>
                        <p class="text-xs text-green-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            +12% dari bulan lalu
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
                        <p class="text-3xl font-bold text-gray-800 mt-2">1,423</p>
                        <p class="text-xs text-green-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            +8% dari bulan lalu
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
                        <p class="text-3xl font-bold text-gray-800 mt-2">892</p>
                        <p class="text-xs text-green-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            +23% dari bulan lalu
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
                        <p class="text-3xl font-bold text-gray-800 mt-2">Rp 124.5M</p>
                        <p class="text-xs text-red-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            -3% dari bulan lalu
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

            <!-- Revenue Chart -->
             <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Pendapatan & Pengeluaran</h3>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>6 Bulan Terakhir</option>
                        <option>12 Bulan Terakhir</option>
                        <option>Tahun Ini</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Invoice Status Chart -->
             <!-- Invoice Status Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Status Invoice</h3>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Bulan Ini</option>
                        <option>3 Bulan Terakhir</option>
                        <option>Tahun Ini</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="invoiceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Invoice Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600">INV</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">INV-2024-001234</p>
                                    <p class="text-sm text-gray-500">PT. Maju Jaya Abadi</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">Rp 24.500.000</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full mt-1">Lunas</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600">INV</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">INV-2024-001233</p>
                                    <p class="text-sm text-gray-500">CV. Berkah Sejahtera</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">Rp 15.750.000</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full mt-1">Pending</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600">INV</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">INV-2024-001232</p>
                                    <p class="text-sm text-gray-500">Toko Bangunan Sentosa</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">Rp 32.100.000</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full mt-1">Lunas</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600">INV</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">INV-2024-001231</p>
                                    <p class="text-sm text-gray-500">PT. Jaya Konstruksi</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">Rp 18.900.000</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full mt-1">Overdue</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100">
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua Invoice →</a>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Produk Terlaris</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                                    1
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">PVC Pipe 3 inch</p>
                                    <p class="text-sm text-gray-500">342 terjual</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">Rp 125.000</p>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white font-bold">
                                    2
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">PVC Elbow 90°</p>
                                    <p class="text-sm text-gray-500">289 terjual</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">Rp 45.000</p>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold">
                                    3
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">PVC Tee Junction</p>
                                    <p class="text-sm text-gray-500">256 terjual</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">Rp 38.000</p>
                        </div>
                    </div>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-yellow-500 rounded-lg flex items-center justify-center text-white font-bold">
                                    4
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">PVC Reducer</p>
                                    <p class="text-sm text-gray-500">198 terjual</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">Rp 52.000</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100">
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua Produk →</a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Invoice</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Customer</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Tambah Produk</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Lihat Laporan</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan',
                data: [85000000, 92000000, 78000000, 105000000, 98000000, 124500000],
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Pengeluaran',
                data: [45000000, 52000000, 48000000, 58000000, 51000000, 62000000],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Invoice Status Chart
    const invoiceCtx = document.getElementById('invoiceChart').getContext('2d');
    new Chart(invoiceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Pending', 'Overdue'],
            datasets: [{
                data: [654, 189, 49],
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
