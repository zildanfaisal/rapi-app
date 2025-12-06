@extends('layouts.app')

@section('title', __('Products'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Products') }}</h2>
@endsection

@section('content')
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4">{{ __('Products') }}</h3>
                        <a href="{{ route('products.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Products
                        </a>
                    </div>
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Barcode</th>
                                <th class="px-4 py-2 border">Nama Produk</th>
                                <th class="px-4 py-2 border">Kategori</th>
                                <th class="px-4 py-2 border">Harga</th>
                                <th class="px-4 py-2 border">Satuan</th>
                                <th class="px-4 py-2 border">Foto Produk</th>
                                <th class="px-4 py-2 border">Stok</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        @if($products->isEmpty())
                            <tbody>
                                <tr>
                                    <td colspan="10" class="py-6">
                                        <div class="text-center text-gray-600 text-lg font-medium">
                                            Belum Ada Product.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endif

                        <tbody>
                            @foreach ($products as $p)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex flex-col items-center justify-center">
                                            {{-- Barcode --}}
                                            <div class="flex justify-center">
                                                {!! DNS1D::getBarcodeHTML($p->barcode, 'C128', 2, 60) !!}
                                            </div>

                                            {{-- Kode Barcode --}}
                                            <div class="text-sm font-bold mt-2">
                                                {{ $p->barcode }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 border">{{ $p->nama_produk }}</td>
                                    <td class="px-4 py-2 border">{{ $p->kategori }}</td>
                                    <td class="px-4 py-2 border"> Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">{{ $p->satuan }}</td>
                                    <td class="px-4 py-2 border">
                                        @if($p->foto_produk)
                                            <img src="{{ asset('storage/' . $p->foto_produk) }}" alt="{{ $p->nama_produk }}" class="w-16 h-16 object-cover mx-auto">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border"> {{ $p->batches->sum('quantity_sekarang') }}</td>
                                    <td class="px-4 py-2 border">
                                        @if($p->batches->sum('quantity_sekarang') >= $p->min_stok_alert)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">{{ $p->status }}</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">{{ $p->status }}</span>
                                        @endif    
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('products.edit', $p->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline;">
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
@endsection