@extends('layouts.app')

@section('title', __('Detail Pembelian'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Pembelian') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-2 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Detail Pembelian') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('pembelian.index') }}" class="inline-block px-3 py-1.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('Kembali') }}</a>
                    </div>
                </div>

                <div class="p-2 space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Nomor Invoice</div>
                            <div class="font-semibold">{{ $pembelian->invoice_number }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Supplier</div>
                            <div class="font-semibold">{{ $pembelian->supplier->nama_supplier ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tanggal Pembelian</div>
                            <div class="font-semibold">{{ $pembelian->tanggal_pembelian }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">User</div>
                            <div class="font-semibold">{{ $pembelian->user->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Pembayaran</div>
                            @php $status = $pembelian->status_pembayaran; @endphp
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
                        </div>
                        <div>
                            <div class="text-gray-600">Metode Pembayaran</div>
                            <div class="font-semibold">{{ ucfirst($pembelian->metode_pembayaran ?? '-') }}</div>
                        </div>
                        {{-- <div>
                            <div class="text-gray-600">Status Setor</div>
                            @php $isSetor = ($pembelian->status_setor ?? 'belum') === 'sudah'; @endphp
                            <span class="inline-block px-2 py-1 rounded text-xs {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $isSetor ? 'Setor' : 'Belum' }}</span>
                        </div> --}}
                        <div>
                            <div class="text-gray-600">Bukti Pembayaran</div>
                            {!! $pembelian->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$pembelian->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Produk</th>
                                    <th class="px-4 py-2 border">Batch Number</th>
                                    <th class="px-4 py-2 border">Kuantitas</th>
                                    <th class="px-4 py-2 border">Harga Beli</th>
                                    <th class="px-4 py-2 border">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->items as $item)
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $item->product->nama_produk ?? 'Produk #'.$item->product_id }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $item->batch->batch_number ?? '-' }}
                                            @if($item->batch && $item->batch->tanggal_expired)
                                                <div class="text-xs text-gray-500 mt-1">Exp: {{ \Carbon\Carbon::parse($item->batch->tanggal_expired)->format('d/m/Y') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="4" class="px-4 py-2 border text-right font-bold">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2 border font-bold text-center">
                                        Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($pembelian->status_pembayaran === 'cancelled' && $pembelian->alasan_cancel)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-gray-600 mb-1">Alasan Batal</div>
                        <div class="font-semibold">{{ $pembelian->alasan_cancel }}</div>
                    </div>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        @can('pembelian.update')
                        <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Edit Pembelian') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection