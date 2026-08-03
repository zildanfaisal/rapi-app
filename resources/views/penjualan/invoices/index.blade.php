@extends('layouts.app')

@section('title', __('Penjualan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Penjualan') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- ================= ATAS / SUMMARY ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            {{-- Rasio Lunas --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="text-sm text-green-700 mb-1">Rasio Lunas / Transaksi</div>
                <div class="text-4xl font-extrabold text-green-800 tracking-tight">
                    {{ number_format($paidCount ?? 0) }}
                    <span class="text-green-600">/</span>
                    {{ number_format($totalCount ?? 0) }}
                </div>
                <div class="mt-2 text-xs text-green-700 space-y-0.5">
                    <div>Lunas: <b>{{ number_format($paidCount ?? 0) }}</b> transaksi</div>
                    <div>Total: <b>{{ number_format($totalCount ?? 0) }}</b> transaksi</div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="text-[11px] text-green-600">
                            Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Filter Tanggal --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium">Dari</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                                   class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                        <div>
                            <label class="text-sm font-medium">Sampai</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                                   class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Filter
                        </button>
                        <a href="{{ route('invoices.index') }}"
                           class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

        </div>

        {{-- ================= CARD PEMASUKAN ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-700 mb-1">Total Pemasukan (Lunas)</div>
                <div class="text-2xl font-bold text-green-800">
                    Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-700 mb-1">Total Pemasukan (Sudah Disetor)</div>
                <div class="text-2xl font-bold text-blue-800">
                    Rp {{ number_format($totalSetor ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ================= MAIN WRAPPER ================= --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER TABLE --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Penjualan</h3>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto sm:justify-end items-center">
                        <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center gap-2">
                            <input type="hidden" name="date_from" value="{{ $dateFrom ?? '' }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo ?? '' }}">
                            <select name="status_pembayaran" class="px-3 py-2.5 border rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="paid" @selected(($statusFilter ?? '') === 'paid')>Lunas</option>
                                <option value="partial" @selected(($statusFilter ?? '') === 'partial')>Cicilan</option>
                                <option value="unpaid" @selected(($statusFilter ?? '') === 'unpaid')>Belum Lunas</option>
                                <option value="cancelled" @selected(($statusFilter ?? '') === 'cancelled')>Batal</option>
                                <option value="overdue" @selected(($statusFilter ?? '') === 'overdue')>Terlambat</option>
                            </select>
                        </form>
                        @can('view_reports')
                        <a href="{{ route('invoices.report.items', array_filter(['date_from' => $dateFrom ?? null, 'date_to' => $dateTo ?? null, 'status_pembayaran' => $statusFilter ?? null])) }}"
                           class="inline-flex items-center justify-center
                                  px-4 py-2.5 bg-green-600 text-white
                                  rounded-lg hover:bg-green-700">
                            Laporan Terjual
                        </a>
                        @endcan
                        <a href="{{ route('invoices.create') }}"
                           class="inline-flex items-center justify-center
                                  px-4 py-2.5 bg-blue-600 text-white
                                  rounded-lg hover:bg-blue-700">
                            + Tambah Penjualan
                        </a>
                    </div>
                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Invoice</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Produk</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Qty</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Pembeli</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Total</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Bukti Pembayaran</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $i)
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-left font-semibold">{{ $i->invoice_number }}</td>
                                <td class="border px-3 py-2 text-left">
                                    {{ $i->items->pluck('product.nama_produk')->join(', ') }}
                                </td>
                                <td class="border px-3 py-2">{{ $i->items->sum('quantity') }}</td>
                                <td class="border px-3 py-2 text-left">{{ $i->customer->nama_customer ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $i->tanggal_invoice }}</td>
                                <td class="border px-3 py-2 text-right">
                                    Rp {{ number_format($i->grand_total,0,',','.') }}
                                </td>
                                <td class="border px-3 py-2">
                                    @if($i->status_pembayaran === 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                    @elseif($i->status_pembayaran === 'partial')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Cicilan</span>
                                    @elseif($i->status_pembayaran === 'unpaid')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                    @elseif($i->status_pembayaran === 'overdue')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                    @elseif($i->status_pembayaran === 'cancelled')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($i->status_pembayaran) }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    {!! $i->bukti_setor
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$i->bukti_setor).'">Lihat</a>'
                                        : '-' !!}
                                </td>
                                <td class="border px-3 py-2">
                                    <div class="flex flex-col space-y-1">
                                        <a href="{{ route('invoices.show', $i) }}"
                                        class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Detail
                                        </a>

                                        <a href="{{ route('invoices.edit', $i) }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline">
                                        Edit
                                        </a>

                                        @if(in_array($i->status_pembayaran, ['unpaid', 'partial']) && $i->sisa_tagihan > 0)
                                            <button type="button"
                                                class="btn-pelunasan text-green-600 hover:text-green-800 hover:underline text-left"
                                                data-url="{{ route('invoices.pembayaran.store', $i) }}"
                                                data-sisa="{{ $i->sisa_tagihan }}"
                                                data-sisa-format="{{ number_format($i->sisa_tagihan, 0, ',', '.') }}"
                                                data-invoice="{{ $i->invoice_number }}">
                                                Pelunasan
                                            </button>
                                        @endif

                                        <form
                                            id="delete-form-{{ $i->id }}"
                                            action="{{ route('invoices.destroy', $i) }}"
                                            method="POST"
                                            class="delete-invoice-form"
                                            data-invoice-id="{{ $i->id }}"
                                            data-invoice-qty="{{ $i->items->sum('quantity') }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="restore_stock" id="restore-stock-{{ $i->id }}" value="0">
                                            <button type="button"
                                                onclick="confirmDelete({{ $i->id }}, {{ $i->items->sum('quantity') }})"
                                                class="text-red-600 hover:text-red-800 hover:underline text-left">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

        {{-- ================= MOBILE CARD ================= --}}
        <div class="block lg:hidden mt-4">

            <div class="flex justify-between mb-3">
                <div class="text-sm">
                    Show
                    <select id="mobilePerPage" class="border rounded text-sm mx-1">
                        <option value="5">5</option>
                        <option value="10">10</option>
                    </select>
                    entries
                </div>
            </div>

            <div id="mobileCards" class="space-y-3">
                @foreach($invoices as $i)
                <div class="mobile-card border rounded-lg bg-white shadow">

                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <div class="text-xs text-gray-500">Invoice</div>
                        <div class="font-semibold">{{ $i->invoice_number }}</div>
                        <div class="text-sm text-gray-600">{{ $i->customer->nama_customer ?? '-' }}</div>
                    </div>

                    <div class="px-4 py-3 text-sm space-y-1">
                        <div>Produk: {{ $i->items->pluck('product.nama_produk')->join(', ') }}</div>
                        <div>Qty: {{ $i->items->sum('quantity') }}</div>
                        <div>Total: <b>Rp {{ number_format($i->grand_total,0,',','.') }}</b></div>
                        <div>Tanggal: {{ $i->tanggal_invoice }}</div>
                        <div>Status:
                            @if($i->status_pembayaran === 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                            @elseif($i->status_pembayaran === 'partial')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Cicilan</span>
                            @elseif($i->status_pembayaran === 'unpaid')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                            @elseif($i->status_pembayaran === 'overdue')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                            @elseif($i->status_pembayaran === 'cancelled')
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($i->status_pembayaran) }}</span>
                            @endif
                        </div>
                        <div>Bukti Pembayaran:
                            {!! $i->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$i->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                        <a href="{{ route('invoices.show', $i) }}"
                           class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2">
                            Detail
                        </a>
                        <a href="{{ route('invoices.edit',$i) }}"
                           class="flex-1 border border-indigo-600 text-indigo-600 rounded text-center py-2">
                            Edit
                        </a>
                        @if(in_array($i->status_pembayaran, ['unpaid', 'partial']) && $i->sisa_tagihan > 0)
                            <button type="button"
                                class="btn-pelunasan flex-1 border border-green-600 text-green-600 rounded text-center py-2"
                                data-url="{{ route('invoices.pembayaran.store', $i) }}"
                                data-sisa="{{ $i->sisa_tagihan }}"
                                data-sisa-format="{{ number_format($i->sisa_tagihan, 0, ',', '.') }}"
                                data-invoice="{{ $i->invoice_number }}">
                                Pelunasan
                            </button>
                        @endif
                        <form
                            id="delete-form-mobile-{{ $i->id }}"
                            action="{{ route('invoices.destroy',$i) }}"
                            method="POST"
                            class="flex-1">
                            @csrf @method('DELETE')
                            <input type="hidden" name="restore_stock" id="restore-stock-mobile-{{ $i->id }}" value="0">
                            <button type="button"
                                onclick="confirmDelete({{ $i->id }}, {{ $i->items->sum('quantity') }})"
                                class="w-full border border-red-600 text-red-600 rounded py-2">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>

           <div class="flex flex-col gap-3 mt-4">
    <div id="mobileInfo" class="text-sm text-gray-600 text-center"></div>

    <div class="flex justify-center w-full">
        <div id="mobilePagination" class="flex gap-1"></div>
    </div>
</div>

        </div>

    </div>
    <div id="modal-pelunasan" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-gray-900/50" data-close-pelunasan></div>
            <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Pelunasan Invoice <span id="pelunasan-invoice"></span></h3>
                    <button type="button" data-close-pelunasan class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
                </div>
                <p class="mb-4 text-sm text-gray-600">Sisa tagihan: <strong id="pelunasan-sisa"></strong></p>
                <form id="form-pelunasan" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="jumlah_bayar_display" class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                            <input type="text" id="jumlah_bayar_display" class="flex-1 w-full rounded-none rounded-r-md border-gray-300" autocomplete="off" required>
                            <input type="hidden" name="jumlah_bayar" id="jumlah_bayar">
                        </div>
                    </div>
                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" id="tanggal_bayar" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="mt-1 block w-full rounded-md border-gray-300" required>
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    <div id="bukti_pembayaran_wrapper">
                        <label for="bukti_pembayaran" class="block text-sm font-medium text-gray-700">Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <input type="text" name="catatan" id="catatan" placeholder="Opsional" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" data-close-pelunasan class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ===== DELETE WITH STOCK RESTORE CONFIRMATION =====
function confirmDelete(invoiceId, qty) {
    Swal.fire({
        title: 'Kembalikan stok?',
        html: `Apakah stok sebanyak <b>${qty}</b> item ingin dikembalikan ke produk?`,
        icon: 'question',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Ya, kembalikan stok',
        denyButtonText: 'Tidak, hapus saja',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16a34a',
        denyButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed || result.isDenied) {
            const restoreValue = result.isConfirmed ? '1' : '0';

            // Set nilai restore_stock di SEMUA form dengan id ini (desktop & mobile)
            const restoreDesktop = document.getElementById(`restore-stock-${invoiceId}`);
            const restoreMobile  = document.getElementById(`restore-stock-mobile-${invoiceId}`);
            if (restoreDesktop) restoreDesktop.value = restoreValue;
            if (restoreMobile)  restoreMobile.value  = restoreValue;

            // Konfirmasi akhir sebelum hapus
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: result.isConfirmed
                    ? `Data akan dihapus dan stok ${qty} item akan dikembalikan.`
                    : 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // Submit form yang ditemukan (desktop atau mobile)
                    const form = document.getElementById(`delete-form-${invoiceId}`)
                                 || document.getElementById(`delete-form-mobile-${invoiceId}`);
                    if (form) form.submit();
                }
            });
        }
    });
}

// ===== DATATABLE & MOBILE PAGINATION =====
document.addEventListener('DOMContentLoaded', () => {

    const modalPelunasan = document.getElementById('modal-pelunasan');
    const formPelunasan = document.getElementById('form-pelunasan');
    const jumlahBayarDisplay = document.getElementById('jumlah_bayar_display');
    const jumlahBayar = document.getElementById('jumlah_bayar');
    const metodePembayaran = document.getElementById('metode_pembayaran');
    const buktiPembayaranWrapper = document.getElementById('bukti_pembayaran_wrapper');
    const buktiPembayaranInput = document.getElementById('bukti_pembayaran');

    function toggleBuktiPembayaran() {
        const isTunai = metodePembayaran.value === 'tunai';
        buktiPembayaranWrapper.style.display = isTunai ? 'none' : '';
        if (isTunai) {
            buktiPembayaranInput.value = '';
        }
    }

    document.querySelectorAll('.btn-pelunasan').forEach((button) => {
        button.addEventListener('click', () => {
            formPelunasan.action = button.dataset.url;
            document.getElementById('pelunasan-invoice').textContent = button.dataset.invoice;
            document.getElementById('pelunasan-sisa').textContent = `Rp ${button.dataset.sisaFormat}`;
            jumlahBayar.value = '';
            jumlahBayarDisplay.value = '';
            metodePembayaran.value = 'tunai';
            toggleBuktiPembayaran();
            modalPelunasan.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-close-pelunasan]').forEach((button) => {
        button.addEventListener('click', () => modalPelunasan.classList.add('hidden'));
    });

    jumlahBayarDisplay.addEventListener('input', function () {
        const value = this.value.replace(/\D/g, '');
        jumlahBayar.value = value;
        this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
    });

    metodePembayaran.addEventListener('change', toggleBuktiPembayaran);
    toggleBuktiPembayaran();

    let dataTable = null;
    const cards = [...document.querySelectorAll('.mobile-card')];
    const info = document.getElementById('mobileInfo');
    const pagination = document.getElementById('mobilePagination');
    const perPageSelect = document.getElementById('mobilePerPage');

    let perPage = parseInt(perPageSelect.value);
    let currentPage = 1;

    function renderMobile(){
        const total = cards.length;
        const pages = Math.ceil(total / perPage);
        const start = (currentPage-1)*perPage;
        const end = start + perPage;

        cards.forEach((c,i)=>c.style.display = i>=start && i<end ? 'block':'none');
        info.textContent = `Showing ${start+1} to ${Math.min(end,total)} of ${total} entries`;
        renderPagination(pages);
    }

    function renderPagination(pages){
        pagination.innerHTML='';
        const max=5;
        let s=Math.max(1,currentPage-2);
        let e=Math.min(pages,s+max-1);

        const btn=(t,d,a,cb)=>{
            const b=document.createElement('button');
            b.textContent=t;
            b.disabled=d;
            b.className=`px-3 py-1 text-sm border rounded ${a?'bg-blue-600 text-white':''}`;
            b.onclick=cb;
            return b;
        };

        pagination.appendChild(btn('Prev',currentPage===1,false,()=>{currentPage--;renderMobile();}));
        for(let i=s;i<=e;i++){
            pagination.appendChild(btn(i,false,i===currentPage,()=>{currentPage=i;renderMobile();}));
        }
        pagination.appendChild(btn('Next',currentPage===pages,false,()=>{currentPage++;renderMobile();}));
    }

    perPageSelect.onchange=()=>{perPage=parseInt(perPageSelect.value);currentPage=1;renderMobile();};

    function handleResponsive(){
        if(window.innerWidth>=1024){
            if(!dataTable){
                dataTable=new DataTable('#dataTables',{responsive:true});
            }
        }else{
            if(dataTable){
                dataTable.destroy();
                dataTable=null;
            }
            renderMobile();
        }
    }

    handleResponsive();
    window.addEventListener('resize',handleResponsive);
});
</script>
@endpush
