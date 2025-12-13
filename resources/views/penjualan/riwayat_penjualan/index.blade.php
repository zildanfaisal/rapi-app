@extends('layouts.app')

@section('title', __('Riwayat Transaksi'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Riwayat Transaksi') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left: Summary -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Ringkasan</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format(($riwayat->count() ?? 0), 0, ',', '.') }} transaksi</div>
                @php
                    $countInv = $riwayat->where('type','invoice')->count();
                    $countSJ = $riwayat->where('type','surat_jalan')->count();
                @endphp
                <div class="mt-2 text-xs text-gray-600 space-y-0.5">
                    <div>Invoice: <span class="font-semibold">{{ number_format($countInv, 0, ',', '.') }}</span></div>
                    <div>Surat Jalan (Lunas): <span class="font-semibold">{{ number_format($countSJ, 0, ',', '.') }}</span></div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="text-[11px] text-gray-500">Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}</div>
                    @endif
                </div>
            </div>
            <!-- Right: Filter -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('riwayat-penjualan.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select name="filter" class="w-full px-3 py-2.5 border rounded-lg">
                                <option value="" {{ ($filter ?? '')=='' ? 'selected' : '' }}>Semua</option>
                                <option value="invoice" {{ ($filter ?? '')=='invoice' ? 'selected' : '' }}>Invoice</option>
                                <option value="surat_jalan" {{ ($filter ?? '')=='surat_jalan' ? 'selected' : '' }}>Surat Jalan (Lunas)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        <a href="{{ route('riwayat-penjualan.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ __('Riwayat Transaksi') }}
                    </h3>

                    <a href="{{ route('riwayat-penjualan.pdf', request()->query()) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-block px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        {{ __('Export PDF') }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTablesRiwayat">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Jenis</th>
                                <th class="px-4 py-2 border">Nomor</th>
                                <th class="px-4 py-2 border">Pelanggan</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $i => $row)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $row['type'] === 'invoice' ? 'Invoice' : 'Surat Jalan' }}</td>
                                    <td class="px-4 py-2 border">{{ $row['nomor'] ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $row['customer'] ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $row['tanggal'] ?? '-' }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($row['grand_total'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $row['status'] ?? null; @endphp
                                        @if (($row['type'] ?? '') === 'invoice')
                                            @if ($status === 'paid')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                                            @elseif ($status === 'unpaid')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                                            @elseif ($status === 'overdue')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Terlambat</span>
                                            @elseif ($status === 'cancelled')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                                            @else
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                            @endif
                                        @else
                                            @if ($status === 'lunas')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                                            @elseif ($status === 'pending')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                                            @elseif ($status === 'cancel')
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                                            @else
                                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ $row['link'] }}" class="text-blue-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
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
    new DataTable('#dataTablesRiwayat');
</script>
@endpush
