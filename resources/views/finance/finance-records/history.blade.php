@extends('layouts.app')

@section('title', __('Riwayat Keuangan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Riwayat Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Filter -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('finance-records.history') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Periode</label>
                    <select name="periode" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Semua Periode</option>
                        @foreach($availablePeriods as $p)
                            <option value="{{ $p }}" {{ $periode == $p ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($p . '-01')->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                    <a href="{{ route('finance-records.history') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
                $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
                $saldo = $totalPemasukan - $totalPengeluaran;
            @endphp

            @if($budgetTarget)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="text-sm text-purple-600 mb-1">Target Bulanan</div>
                <div class="text-2xl font-bold text-purple-700">Rp {{ number_format($budgetTarget->budget_bulanan, 0, ',', '.') }}</div>
            </div>
            @endif

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600 mb-1">Total Pemasukan</div>
                <div class="text-2xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600 mb-1">Total Pengeluaran</div>
                <div class="text-2xl font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 mb-1">Saldo</div>
                <div class="text-2xl font-bold {{ $saldo >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
                @if($budgetTarget)
                <div class="text-xs text-gray-500 mt-1">
                    {{ $saldo >= 0 ? 'Surplus' : 'Defisit' }}
                    {{ number_format(abs(($saldo / $budgetTarget->budget_bulanan) * 100), 1) }}%
                </div>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Riwayat Keuangan') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTables">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Periode</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Tipe</th>
                                <th class="px-4 py-2 border">Kategori</th>
                                <th class="px-4 py-2 border">Jumlah</th>
                                <th class="px-4 py-2 border">Deskripsi</th>
                                <th class="px-4 py-2 border">Dibuat Oleh</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($financeRecords as $fr)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($fr->periode . '-01')->format('M Y') }}</td>
                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 border">
                                        @if($fr->tipe === 'income')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Pemasukan</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">{{ $fr->kategori }}</td>
                                    <td class="px-4 py-2 border text-right">
                                        <span class="{{ $fr->tipe === 'income' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                            {{ $fr->tipe === 'income' ? '+' : '-' }} Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border text-left">{{ $fr->deskripsi ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $fr->user->name ?? 'Unknown' }}</td>
                                </tr>
                             @empty
                                <tr class="text-center">
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border">Belum Ada Anggaran</td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#dataTables');
</script>
@endpush
