@extends('layouts.app')

@section('title', __('Detail Target Bulanan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Detail Target Bulanan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Informasi Target</div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div>Nama: <span class="font-semibold">{{ $monthlyTarget->name }}</span></div>
                    <div>Periode: <span class="font-semibold">{{ $monthlyTarget->start_date }} s/d {{ $monthlyTarget->end_date }}</span></div>
                    <div>Target: <span class="font-semibold">Rp {{ number_format($monthlyTarget->target_amount ?? 0, 0, ',', '.') }}</span></div>
                    <div>Sisa Menuju Target: <span class="font-semibold">Rp {{ number_format(max(0, ($monthlyTarget->target_amount ?? 0) - ($actuals ?? 0)), 0, ',', '.') }}</span></div>
                    <div>Status: 
                        @php $status = $monthlyTarget->status; @endphp
                        @if ($status === 'achieved')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Tercapai</span>
                        @elseif ($status === 'ongoing')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Berjalan</span>
                        @elseif ($status === 'missed')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Tidak Tercapai</span>
                        @else
                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                        @endif
                    </div>
                    @if(!empty($monthlyTarget->notes))
                        <div>Catatan: <span class="font-semibold">{{ $monthlyTarget->notes }}</span></div>
                    @endif
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Ringkasan</div>
                <div class="text-2xl font-bold text-gray-900">Rp {{ number_format(($actuals ?? 0), 0, ',', '.') }}</div>
                <div class="mt-2 text-xs text-gray-600">Total penjualan lunas (grand total invoice berstatus paid) dalam periode ini.</div>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <h3 class="mb-4">{{ __('Invoice dalam Periode') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTablesInvoices">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Nomor</th>
                                <th class="px-4 py-2 border">Pelanggan</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $inv->invoice_number ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->customer->nama_customer ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->tanggal_invoice }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($inv->grand_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $inv->status_pembayaran; @endphp
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
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#dataTablesInvoices');
</script>
@endpush
