@extends('layouts.app')

@section('title', __('Riwayat Keuangan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Riwayat Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
                $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
                $saldo = $totalPemasukan - $totalPengeluaran;
            @endphp

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
                                    <td class="px-4 py-2 border">Belum Ada Product.</td>
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
