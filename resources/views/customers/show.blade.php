@extends('layouts.app')

@section('title', __('Detail Pelanggan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">Detail Pelanggan</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- INFO PELANGGAN --}}
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h3 class="mb-4 font-semibold text-lg">Informasi Pelanggan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama</p>
                    <p class="font-medium">{{ $customer->nama_customer }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">No. HP</p>
                    <p class="font-medium">{{ $customer->no_hp }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $customer->email ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Poin</p>
                    <p class="font-medium">{{ $customer->point }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Alamat</p>
                    <p class="font-medium">{{ $customer->alamat }}</p>
                </div>
            </div>

            
        </div>
                
        {{-- RIWAYAT TRANSAKSI --}}
        <div class="p-4 bg-white shadow rounded-lg">
        <h3 class="font-semibold text-lg mb-4">Riwayat Transaksi</h3>

        <table class="min-w-full border" id="dataTables">
        <thead class="bg-gray-100">
        <tr>
            <th class="border px-3 py-2">No</th>
            <th class="border px-3 py-2">Invoice</th>
            <th class="border px-3 py-2">Tanggal</th>
            <th class="border px-3 py-2">Total</th>
            <th class="border px-3 py-2">Status</th>
            <th class="border px-3 py-2">Aksi</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($customer->invoices as $inv)
        <tr class="text-center">
            <td class="border px-3 py-2">{{ $loop->iteration }}</td>
            <td class="border px-3 py-2">{{ $inv->invoice_number }}</td>
            <td class="border px-3 py-2">{{ $inv->tanggal_invoice }}</td>
            <td class="border px-3 py-2">
                Rp {{ number_format($inv->grand_total,0,',','.') }}
            </td>
            <td class="border px-3 py-2">
                {{ $inv->status_pembayaran }}
            </td>
            <td class="border px-3 py-2">

                {{-- 🔥 BUTTON DETAIL --}}
                <button
            class="text-indigo-600 hover:underline"
            data-items='@json($inv->items_json)'
            onclick="openInvoiceModal(this)">
            Detail
        </button>


            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        </div>

        </div>
        </div>

        {{-- ================= MODAL ================= --}}
        <div id="invoiceModal"
            class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center z-50">

        <div class="bg-white w-full max-w-2xl rounded-lg p-6">
        <h3 class="font-semibold text-lg mb-4">Detail Invoice</h3>

        <table class="min-w-full border">
        <thead class="bg-gray-100">
        <tr>
            <th class="border px-3 py-2">Batch Number</th>
            <th class="border px-3 py-2">Produk</th>
            <th class="border px-3 py-2">Qty</th>
            <th class="border px-3 py-2">Harga</th>
            <th class="border px-3 py-2">Subtotal</th>
        </tr>
        </thead>
        <tbody id="invoiceItemBody"></tbody>
        </table>

        <div class="text-right mt-4">
        <button onclick="closeInvoiceModal()"
                class="px-4 py-2 bg-gray-300 rounded">
            Tutup
        </button>
        </div>
        </div>
        </div>
@endsection

@push('scripts')
<script>
new DataTable('#dataTables');

function openInvoiceModal(btn) {
    const modal = document.getElementById('invoiceModal');
    const tbody = document.getElementById('invoiceItemBody');

    tbody.innerHTML = '';

    const items = JSON.parse(btn.dataset.items);

    items.forEach(item => {
        tbody.innerHTML += `
            <tr class="text-center">
                 <td class="border px-3 py-2">${item.batch_number}</td>
                <td class="border px-3 py-2 text-left">${item.produk}</td>
                <td class="border px-3 py-2">${item.qty}</td>
                <td class="border px-3 py-2">
                    Rp ${Number(item.harga).toLocaleString('id-ID')}
                </td>
                <td class="border px-3 py-2">
                    Rp ${Number(item.subtotal).toLocaleString('id-ID')}
                </td>
            </tr>
        `;
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
