@extends('layouts.app')

@section('title', __('Tambah Batch Produk'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Tambah Batch Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">

                <h3 class="mb-4">{{ __('Tambah Batch Produk') }}</h3>

                <form method="POST" action="{{ route('product-batches.store') }}">
                    @csrf

                    {{-- barcode --}}
                    <div class="mb-4">
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode Produk</label>
                        
                        <input type="text" name="barcode" id="barcode"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                    focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            placeholder="Masukkan Barcode Produk"
                            required>
                    </div>



                    {{-- Kode Batch --}}
                   <div class="mb-4">
                        <label for="batch_number" class="block text-sm font-medium text-gray-700">Batch Number</label>

                        <div class="flex gap-2">
                            <input type="text" name="batch_number" id="batch_number" required
                             maxlength="5" minlength="5"
                            title="Kode batch harus terdiri dari 5 angka"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">

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
                        <input type="date" name="tanggal_masuk" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Tanggal Expired --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Expired</label>
                        <input type="date" name="tanggal_expired" id="tanggal_expired" disabled
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                    </div>

                    {{-- Qty Masuk --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kuantitas Masuk</label>
                        <input type="number" id="qty_masuk" name="quantity_masuk" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                   {{-- Qty Sekarang (readonly) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kuantitas Sekarang</label>
                        <input type="number" id="qty_sekarang" name="quantity_sekarang" readonly
                            class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Supplier --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input type="text" name="supplier"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="sold_out">Sold Out</option>
                        </select>
                    </div>

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
    document.getElementById('qty_masuk').addEventListener('input', function() {
        document.getElementById('qty_sekarang').value = this.value;
    });
    const tanggalMasuk = document.querySelector('input[name="tanggal_masuk"]');
    const tanggalExpired = document.getElementById('tanggal_expired');

    tanggalExpired.disabled = true;

    tanggalMasuk.addEventListener('change', function () {
        const masuk = this.value;

        if (masuk) {
            tanggalExpired.disabled = false;     
            tanggalExpired.min = masuk;          

          
            if (tanggalExpired.value && tanggalExpired.value < masuk) {
                tanggalExpired.value = '';
            }
        } else {
            tanggalExpired.disabled = true;      
            tanggalExpired.value = '';
        }
    });


</script>
@endpush

