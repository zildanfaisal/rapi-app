@extends('layouts.app')

@section('title', __('Penjualan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Penjualan') }}</h2>
@endsection

@section('content')
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Row 1: Lunas Count & Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Penjualan Lunas Count -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-sm text-green-700 mb-1">Rasio Lunas / Transaksi</div>
                    <div class="text-4xl font-extrabold text-green-800 tracking-tight">
                        {{ number_format($paidCount ?? 0, 0, ',', '.') }}
                        <span class="text-green-600">/</span>
                        {{ number_format($totalCount ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-green-700 space-y-0.5">
                        <div>Lunas: <span class="font-semibold">{{ number_format($paidCount ?? 0, 0, ',', '.') }}</span> transaksi</div>
                        <div>Total transaksi: <span class="font-semibold">{{ number_format($totalCount ?? 0, 0, ',', '.') }}</span></div>
                        @if(($dateFrom ?? null) || ($dateTo ?? null))
                            <div class="text-[11px] text-green-600">Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}</div>
                        @endif
                    </div>
                </div>

                <!-- Filter Tanggal -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Dari Tanggal
                                </label>
                                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                                    class="w-full px-3 py-2.5 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sampai Tanggal
                                </label>
                                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                                    class="w-full px-3 py-2.5 border rounded-lg">
                            </div>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <button type="submit"
                                    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Filter
                            </button>
                            <a href="{{ route('invoices.index') }}"
                            class="px-4 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Cards: Lunas vs Sudah Disetor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-700 mb-1">Total Pemasukan (Lunas)</div>
                    <div class="text-2xl font-bold text-green-800">Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm text-blue-700 mb-1">Total Pemasukan (Sudah Disetor)</div>
                    <div class="text-2xl font-bold text-blue-800">Rp {{ number_format($totalSetor ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4">{{ __('Penjualan') }}</h3>
                        <a href="{{ route('invoices.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Penjualan
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                   <table class="min-w-full border border-gray-300" id="dataTables">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nomor Invoice</th>
                                <th class="px-4 py-2 border">Produk</th>
                                <th class="px-4 py-2 border">Quantity</th>
                                <th class="px-4 py-2 border">Pembeli</th>
                                <th class="px-4 py-2 border">Tanggal Invoice</th>
                                <th class="px-4 py-2 border">Tanggal Jatuh Tempo</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status Pembayaran</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoices as $c)
                                <tr class="text-center hover:bg-gray-50 cursor-pointer" data-href="{{ route('invoices.show', $c) }}">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ $c->invoice_number }}</td>
                                    <td class="px-4 py-2 border">{{ $c->items->map(fn($item) => $item->product->nama_produk ?? '')->join(', ') }}</td>
                                    <td class="px-4 py-2 border">{{ $c->items->sum('quantity') }}</td>
                                    <td class="px-4 py-2 border">{{ $c->customer->nama_customer ?? '' }}</td>
                                    <td class="px-4 py-2 border">{{ $c->tanggal_invoice }}</td>
                                    <td class="px-4 py-2 border">{{ $c->tanggal_jatuh_tempo }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($c->grand_total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $c->status_pembayaran; @endphp
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
                                        <a href="{{ route('invoices.edit', $c->id) }}" class="text-blue-600 hover:underline" onclick="event.stopPropagation()">Edit</a>
                                        <form action="{{ route('invoices.destroy', $c->id) }}" method="POST" style="display:inline;" data-confirm-delete onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
                                        </form>
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
    new DataTable('#dataTables');
    document.querySelector('#dataTables tbody')?.addEventListener('click', async function(e){
        const tr = e.target.closest('tr[data-href]');
        if (!tr) return;
        const href = tr.getAttribute('data-href');
        if (!href) return;
        try {
            const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            const html = await res.text();
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            document.body.appendChild(wrapper);
        } catch (err) {
            console.error('Failed to load detail modal', err);
        }
    });
</script>
@endpush
