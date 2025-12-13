@extends('layouts.app')

@section('title', __('Detail Produk'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Detail Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

            <h3 class="mb-6 font-semibold text-lg">{{ __('Detail Produk') }}</h3>
{{-- FOTO --}}
<div class="mb-8">
    <label class="block text-sm font-medium text-gray-700">Foto Produk</label>
    <img
        src="{{ asset('storage/' . $product->foto_produk) }}"
        class="mt-2 w-40 h-40 object-cover rounded-md border"
        alt="Foto Produk"
    >
</div>

            {{-- DETAIL PRODUK --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">

    {{-- KIRI --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            {{ $product->nama_produk }}
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            Rp {{ number_format($product->harga, 0, ',', '.') }}
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Barcode</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            {{ $product->barcode }}
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            @if($product->latestBatch)
                Rp {{ number_format($product->latestBatch->harga_beli, 0, ',', '.') }}
            @else
                <span class="text-gray-400">Belum ada batch</span>
            @endif
        </div>
    </div>


   

    <div>
        <label class="block text-sm font-medium text-gray-700">Satuan</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            {{ $product->satuan }}
        </div>
    </div>

     <div>
        <label class="block text-sm font-medium text-gray-700">Keuntungan</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
             @if($product->latestBatch)
                Rp {{ number_format($product->harga - $product->latestBatch->harga_beli, 0, ',', '.') }}
            @else
                <span class="text-gray-400">Belum ada batch</span>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Kategori</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            {{ $product->kategori }}
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Min Stok Alert</label>
        <div class="mt-1 p-2 border rounded-md bg-gray-100">
            {{ $product->min_stok_alert }}
        </div>
    </div>

    {{-- STATUS --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <div class="mt-1">
            @if($product->batches->sum('quantity_sekarang') >= $product->min_stok_alert)
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    {{ $product->status }}
                </span>
            @else
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                    {{ $product->status }}
                </span>
            @endif
        </div>
    </div>

</div>


            {{-- RIWAYAT STOK (FULL BAWAH) --}}
            <div class="mt-12">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    Riwayat Pemasukan & Pengeluaran Stok
                </h3>

                <div class="overflow-x-auto">
                    <table id="riwayatStokTable" class="min-w-full border border-gray-200 text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Batch</th>
                                <th class="px-4 py-2 border">Jenis</th>
                                <th class="px-4 py-2 border">Quantity</th>
                                <th class="px-4 py-2 border">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatStok ?? [] as $row)
                                <tr class="text-center">
                                    <td class="px-4 py-2 border">
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $row['batch_number'] }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        @if ($row['type'] === 'masuk')
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Masuk
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                                Keluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border font-semibold">
                                        {{ $row['type'] === 'masuk' ? '+' : '-' }}{{ $row['quantity'] }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $row['keterangan'] }}
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
    new DataTable('#riwayatStokTable', {
        perPage: 10,
        perPageSelect: [10, 25, 50],
        searchable: true,
        sortable: true,
        labels: {
            placeholder: "Cari...",
            perPage: "data / halaman",
            noRows: "Belum ada riwayat stok",
            info: "Menampilkan {start} - {end} dari {rows} data",
        }
    });
</script>
@endpush

