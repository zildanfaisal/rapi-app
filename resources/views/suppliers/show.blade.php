@extends('layouts.app')

@section('title', __('Detail Supplier'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Supplier') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- INFO SUPPLIER --}}
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h3 class="mb-4 font-semibold text-lg">Informasi Supplier</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Supplier</p>
                    <p class="font-medium">{{ $supplier->nama_supplier }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">No. HP</p>
                    <p class="font-medium">{{ $supplier->no_hp ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $supplier->email ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Alamat</p>
                    <p class="font-medium">{{ $supplier->alamat ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                @can('suppliers.update')
                <a href="{{ route('suppliers.edit', $supplier->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
                @endcan

                <a href="{{ route('suppliers.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Kembali</a>
            </div>
        </div>
        
        {{-- RIWAYAT PEMBELIAN --}}
        <div class="p-4 bg-white shadow rounded-lg">
            <h3 class="font-semibold text-lg mb-4">Riwayat Pembelian</h3>

            {{-- ================= DESKTOP TABLE ================= --}}
            <div class="hidden lg:block overflow-x-auto">
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
                        @foreach ($supplier->pembelians as $pemb)
                        <tr class="text-center">
                            <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                            <td class="border px-3 py-2">{{ $pemb->invoice_number }}</td>
                            <td class="border px-3 py-2">{{ $pemb->tanggal_pembelian }}</td>
                            <td class="border px-3 py-2">
                                Rp {{ number_format($pemb->grand_total,0,',','.') }}
                            </td>
                            <td class="border px-3 py-2">
                                @if($pemb->status_pembayaran === 'paid')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                @elseif($pemb->status_pembayaran === 'partial' || $pemb->status_pembayaran === 'unpaid')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                @elseif($pemb->status_pembayaran === 'overdue')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                @elseif($pemb->status_pembayaran === 'cancelled')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($pemb->status_pembayaran) }}</span>
                                @endif
                            </td>
                            <td class="border px-3 py-2">
                                <button
                                    class="text-indigo-600 hover:underline"
                                    data-items='@json($pemb->items_json)'
                                    onclick="openInvoiceModal(this)">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= MOBILE CARD ================= --}}
            <div class="block lg:hidden w-full" id="mobileCardWrapper">

                {{-- TOP --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm text-gray-600">
                        Show
                        <select id="mobilePerPage" class="mx-1 border-gray-300 rounded-md text-sm">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                        </select>
                        entries
                    </div>
                </div>

                {{-- CARDS --}}
                <div id="mobileCards" class="space-y-3">
                    @forelse ($supplier->pembelians as $pemb)
                    <div class="mobile-card bg-white border rounded-lg shadow-sm">
                        
                        <div class="px-4 py-3 bg-gray-50 border-b space-y-2">
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    No. {{ $loop->iteration }}
                                </div>
                                <div class="text-xs font-medium text-gray-700">
                                    {{ $pemb->tanggal_pembelian }}
                                </div>
                            </div>

                            <h4 class="font-semibold text-gray-900">
                                {{ $pemb->invoice_number }}
                            </h4>

                            <div class="text-sm text-gray-600">
                                Status: 
                                @if($pemb->status_pembayaran === 'paid')
                                    Lunas
                                @elseif($pemb->status_pembayaran === 'partial' || $pemb->status_pembayaran === 'unpaid')
                                    Belum Lunas
                                @elseif($pemb->status_pembayaran === 'overdue')
                                    Terlambat
                                @elseif($pemb->status_pembayaran === 'cancelled')
                                    Batal
                                @else
                                    {{ ucfirst($pemb->status_pembayaran) }}
                                @endif
                            </div>
                        </div>

                        <div class="px-4 py-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold">
                                    Rp {{ number_format($pemb->grand_total,0,',','.') }}
                                </span>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-t">
                            <button
                                class="w-full text-center px-3 py-2 border border-indigo-600 rounded text-indigo-600"
                                data-items='@json($pemb->items_json)'
                                onclick="openInvoiceModal(this)">
                                Detail
                            </button>
                        </div>

                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        Belum ada pembelian
                    </div>
                    @endforelse
                </div>

                {{-- INFO + PAGINATION --}}
                @if(count($supplier->pembelians) > 0)
                <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                    <div id="mobileInfo" class="text-sm text-gray-600"></div>
                    <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
                </div>
                @endif
            </div>

        </div>

    </div>
</div>

{{-- ================= MODAL ================= --}}
<div id="invoiceModal"
    class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center z-50">

    <div class="bg-white w-full max-w-2xl rounded-lg p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="font-semibold text-lg mb-4">Detail Pembelian</h3>

        <div class="overflow-x-auto">
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
        </div>

        <div class="text-right mt-4">
            <button onclick="closeInvoiceModal()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let dataTableInstance = null;

    const cards = Array.from(document.querySelectorAll('.mobile-card'));
    const pagination = document.getElementById('mobilePagination');
    const info = document.getElementById('mobileInfo');
    const perPageSelect = document.getElementById('mobilePerPage');

    // Skip if no cards
    if (cards.length === 0) {
        // Initialize DataTable for desktop only if no cards
        if (window.innerWidth >= 1024) {
            new DataTable('#dataTables');
        }
        return;
    }

    let perPage = parseInt(perPageSelect.value);
    let currentPage = 1;

    function renderMobile() {
        const total = cards.length;
        const totalPages = Math.ceil(total / perPage);
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        cards.forEach((card, i) => {
            card.style.display = i >= start && i < end ? 'block' : 'none';
        });

        info.textContent = `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        pagination.innerHTML = '';
        const maxVisible = 5;

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        const btn = (label, disabled, active, cb) => {
            const b = document.createElement('button');
            b.textContent = label;
            b.disabled = disabled;
            b.className = `px-3 py-1 text-sm rounded border
                ${active ? 'bg-blue-600 text-white' : 'bg-white'}
                ${disabled ? 'opacity-50' : ''}`;
            b.onclick = cb;
            return b;
        };

        pagination.appendChild(btn('Prev', currentPage === 1, false, () => {
            currentPage--; renderMobile();
        }));

        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(btn(i, false, i === currentPage, () => {
                currentPage = i; renderMobile();
            }));
        }

        pagination.appendChild(btn('Next', currentPage === totalPages, false, () => {
            currentPage++; renderMobile();
        }));
    }

    perPageSelect.addEventListener('change', () => {
        perPage = parseInt(perPageSelect.value);
        currentPage = 1;
        renderMobile();
    });

    function handleResponsive() {
        if (window.innerWidth >= 1024) {
            if (!dataTableInstance) {
                dataTableInstance = new DataTable('#dataTables');
            }
        } else {
            if (dataTableInstance) {
                dataTableInstance.destroy();
                dataTableInstance = null;
            }
            renderMobile();
        }
    }

    handleResponsive();
    window.addEventListener('resize', handleResponsive);
});

// Modal Functions
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
