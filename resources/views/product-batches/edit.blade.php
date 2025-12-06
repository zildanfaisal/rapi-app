@extends('layouts.app')

@section('title', __('Edit Batch Produk'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Edit Batch Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">

                <h3 class="mb-4">{{ __('Edit Batch Produk') }}</h3>

                <form method="POST" action="{{ route('product-batches.update', $productBatch->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Barcode --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Barcode Produk</label>
                        <input type="text" name="barcode" id="barcode"
                            value="{{ old('barcode', $productBatch->product->barcode ?? '') }}"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Masukkan barcode produk">
                        <div id="product-name" class="text-gray-500 text-sm mt-1">
                            {{ $productBatch->product->nama_produk ?? '' }}
                        </div>
                    </div>


                    {{-- Kode Batch --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Batch Number</label>
                        <div class="flex gap-2">
                            <input type="text" name="batch_number" id="batch_number"
                                   maxlength="5" minlength="5"
                                   required
                                   value="{{ old('batch_number', $productBatch->batch_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                            <button type="button"
                                    onclick="generateBatchCode()"
                                    class="mt-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Generate
                            </button>
                        </div>
                    </div>

                    {{-- Tanggal Masuk --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk"
                               value="{{ old('tanggal_masuk', $productBatch->tanggal_masuk) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Tanggal Expired --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired"
                               value="{{ old('tanggal_expired', $productBatch->tanggal_expired) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Qty Masuk --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Qty Masuk</label>
                        <input type="number" name="quantity_masuk"
                               value="{{ old('quantity_masuk', $productBatch->quantity_masuk) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Qty Sekarang --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Qty Sekarang</label>
                        <input type="number" name="quantity_sekarang"
                               value="{{ old('quantity_sekarang', $productBatch->quantity_sekarang) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Supplier --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input type="text" name="supplier"
                               value="{{ old('supplier', $productBatch->supplier) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active" {{ old('status', $productBatch->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ old('status', $productBatch->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="sold_out" {{ old('status', $productBatch->status) == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>

                    <a href="{{ route('product-batches.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Batal
                    </a>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateBatchCode() {
        const code = Math.floor(Math.random() * 100000)
            .toString()
            .padStart(5, '0');

        document.getElementById('batch_number').value = code;
    }
</script>
@endpush
