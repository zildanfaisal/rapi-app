@extends('layouts.app')

@section('title', __('Pembelian'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Pembelian') }}</h2>
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
                <form method="GET" action="{{ route('pembelian.index') }}" class="grid grid-cols-1 gap-4">
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
                        <a href="{{ route('pembelian.index') }}"
                           class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

        </div>

        {{-- ================= CARD PENGELUARAN =================
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-700 mb-1">Total Pengeluaran (Lunas)</div>
                <div class="text-2xl font-bold text-green-800">
                    Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-700 mb-1">Total Pengeluaran (Sudah Disetor)</div>
                <div class="text-2xl font-bold text-blue-800">
                    Rp {{ number_format($totalSetor ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div> --}}

        {{-- ================= MAIN WRAPPER ================= --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER TABLE --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Pembelian</h3>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto sm:justify-end items-center">
                        <form method="GET" action="{{ route('pembelian.index') }}" class="flex items-center gap-2">
                            <input type="hidden" name="date_from" value="{{ $dateFrom ?? '' }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo ?? '' }}">
                            <select name="status_pembayaran" class="px-3 py-2.5 border rounded-lg text-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="paid" @selected(($statusFilter ?? '') === 'paid')>Lunas</option>
                                <option value="unpaid" @selected(($statusFilter ?? '') === 'unpaid')>Belum Lunas</option>
                                <option value="cancelled" @selected(($statusFilter ?? '') === 'cancelled')>Batal</option>
                                <option value="overdue" @selected(($statusFilter ?? '') === 'overdue')>Terlambat</option>
                            </select>
                        </form>
                        @can('pembelian.create')
                        <a href="{{ route('pembelian.create') }}"
                           class="inline-flex items-center justify-center
                                  px-4 py-2.5 bg-blue-600 text-white
                                  rounded-lg hover:bg-blue-700">
                            + Tambah Pembelian
                        </a>
                        @endcan
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
                                <th class="px-3 py-2 border text-left text-xs uppercase">Supplier</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Total</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Bukti Pembayaran</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelians as $p)
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-left font-semibold">{{ $p->invoice_number }}</td>
                                <td class="border px-3 py-2 text-left">
                                    {{ $p->items->pluck('product.nama_produk')->join(', ') }}
                                </td>
                                <td class="border px-3 py-2">{{ $p->items->sum('quantity') }}</td>
                                <td class="border px-3 py-2 text-left">{{ $p->supplier->nama_supplier ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $p->tanggal_pembelian }}</td>
                                <td class="border px-3 py-2 text-right">
                                    Rp {{ number_format($p->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2">
                                    @if($p->status_pembayaran === 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                    @elseif($p->status_pembayaran === 'partial' || $p->status_pembayaran === 'unpaid')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                    @elseif($p->status_pembayaran === 'overdue')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                    @elseif($p->status_pembayaran === 'cancelled')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($p->status_pembayaran) }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    {!! $p->bukti_setor
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$p->bukti_setor).'">Lihat</a>'
                                        : '-' !!}
                                </td>
                                 <td class="border px-3 py-2 whitespace-nowrap">
                                     <div class="flex items-center justify-center gap-3">
                                         <a href="{{ route('pembelian.show', $p) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm hover:underline">
                                             Detail
                                         </a>

                                         @if($p->sisa_tagihan > 0 && $p->status_pembayaran !== 'cancelled')
                                         <button type="button" 
                                                 class="text-green-600 hover:text-green-900 font-semibold text-sm hover:underline btn-pelunasan"
                                                 data-id="{{ $p->id }}"
                                                 data-invoice="{{ $p->invoice_number }}"
                                                 data-sisa="{{ $p->sisa_tagihan }}"
                                                 data-url="{{ route('pembelian.bayar', $p->id) }}">
                                             Pelunasan
                                         </button>
                                         @endif
 
                                         @can('pembelian.update')
                                         <a href="{{ route('pembelian.edit', $p) }}"
                                            class="text-blue-600 hover:text-blue-900 font-semibold text-sm hover:underline">
                                             Edit
                                         </a>
                                         @endcan
 
                                         @can('pembelian.delete')
                                         <form action="{{ route('pembelian.destroy', $p) }}" method="POST" data-confirm-delete class="inline m-0">
                                             @csrf
                                             @method('DELETE')
                                             <button class="text-red-600 hover:text-red-900 font-semibold text-sm hover:underline">
                                                 Hapus
                                             </button>
                                         </form>
                                         @endcan
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
                        @foreach($pembelians as $p)
                        <div class="mobile-card border rounded-lg bg-white shadow">

                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="text-xs text-gray-500">Invoice</div>
                                <div class="font-semibold">{{ $p->invoice_number }}</div>
                                <div class="text-sm text-gray-600">{{ $p->supplier->nama_supplier ?? '-' }}</div>
                            </div>

                            <div class="px-4 py-3 text-sm space-y-1">
                                <div>Produk: {{ $p->items->pluck('product.nama_produk')->join(', ') }}</div>
                                <div>Qty: {{ $p->items->sum('quantity') }}</div>
                                <div>Total: <b>Rp {{ number_format($p->grand_total, 0, ',', '.') }}</b></div>
                                <div>Tanggal: {{ $p->tanggal_pembelian }}</div>
                                <div>Status:
                                    @if($p->status_pembayaran === 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">🟢 Lunas</span>
                                    @elseif($p->status_pembayaran === 'partial' || $p->status_pembayaran === 'unpaid')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">🔴 Belum Lunas</span>
                                    @elseif($p->status_pembayaran === 'overdue')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">🟡 Terlambat</span>
                                    @elseif($p->status_pembayaran === 'cancelled')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">⚫ Batal</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">{{ ucfirst($p->status_pembayaran) }}</span>
                                    @endif
                                </div>
                                <div>Sisa Tagihan: <b>{{ $p->sisa_tagihan > 0 ? 'Rp ' . number_format($p->sisa_tagihan, 0, ',', '.') : '-' }}</b></div>
                                <div>Bukti Pembayaran:
                                    {!! $p->bukti_setor
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$p->bukti_setor).'">Lihat</a>'
                                        : '-' !!}
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex flex-wrap gap-2">
                                <a href="{{ route('pembelian.show', $p) }}"
                                   class="flex-1 bg-indigo-50 text-indigo-600 rounded text-center py-2 min-w-[70px] text-sm">
                                    👁 Detail
                                </a>
                                @if($p->sisa_tagihan > 0 && $p->status_pembayaran !== 'cancelled')
                                <button type="button"
                                        class="flex-1 bg-green-50 text-green-600 rounded py-2 btn-pelunasan min-w-[70px] text-sm"
                                        data-id="{{ $p->id }}"
                                        data-invoice="{{ $p->invoice_number }}"
                                        data-sisa="{{ $p->sisa_tagihan }}"
                                        data-url="{{ route('pembelian.bayar', $p->id) }}">
                                    💰 Bayar
                                </button>
                                @endif
                                @can('pembelian.update')
                                <a href="{{ route('pembelian.edit', $p) }}"
                                   class="flex-1 bg-blue-50 text-blue-600 rounded text-center py-2 min-w-[70px] text-sm">
                                    ✏ Edit
                                </a>
                                @endcan
                                @can('pembelian.delete')
                                <form action="{{ route('pembelian.destroy', $p) }}" method="POST" data-confirm-delete class="flex-1 min-w-[40px]">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full bg-red-50 text-red-600 rounded py-2 text-sm text-center">🗑</button>
                                </form>
                                @endcan
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
        </div>
    </div>
</div>
{{-- Modal Pelunasan --}}
<div id="modalPelunasan" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeModalPelunasan()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formPelunasan" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start w-full">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">
                                Pelunasan Pembelian - <span id="modalInvoiceNumber" class="font-bold"></span>
                            </h3>
                            
                            <div class="space-y-4 text-sm">
                                <div>
                                    <div class="text-gray-600">Sisa Tagihan</div>
                                    <div id="modalSisaTagihanText" class="text-lg font-bold text-red-600"></div>
                                </div>

                                <div>
                                    <label for="modal_jumlah_bayar_display" class="block font-medium text-gray-700">Jumlah Bayar</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            Rp
                                        </span>
                                        <input type="text" id="modal_jumlah_bayar_display" required
                                            class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                            autocomplete="off">
                                        <input type="hidden" name="jumlah_bayar" id="modal_jumlah_bayar" required min="1">
                                    </div>
                                </div>

                                <div>
                                    <label for="modal_tanggal_bayar" class="block font-medium text-gray-700">Tanggal Bayar</label>
                                    <input type="date" name="tanggal_bayar" id="modal_tanggal_bayar" value="{{ date('Y-m-d') }}" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="modal_metode_pembayaran" class="block font-medium text-gray-700">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="modal_metode_pembayaran" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                        <option value="tunai">Tunai (Cash)</option>
                                        <option value="transfer">Transfer (TF)</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>

                                <div id="modal_bukti_setor_wrapper">
                                    <label for="modal_bukti_setor" class="block font-medium text-gray-700">Bukti Pembayaran</label>
                                    <input type="file" name="bukti_setor" id="modal_bukti_setor" accept="image/*"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="modal_catatan" class="block font-medium text-gray-700">Catatan</label>
                                    <input type="text" name="catatan" id="modal_catatan" placeholder="Catatan pembayaran..."
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                        Simpan Pembayaran
                    </button>
                    <button type="button" onclick="closeModalPelunasan()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let dataTable = null;
    const cards = [...document.querySelectorAll('.mobile-card')];
    const info = document.getElementById('mobileInfo');
    const pagination = document.getElementById('mobilePagination');
    const perPageSelect = document.getElementById('mobilePerPage');

    let perPage = parseInt(perPageSelect?.value || 5);
    let currentPage = 1;

    function renderMobile(){
        const total = cards.length;
        if (total === 0) {
            if (info) info.textContent = 'Showing 0 to 0 of 0 entries';
            if (pagination) pagination.innerHTML = '';
            return;
        }
        const pages = Math.ceil(total / perPage);
        const start = (currentPage-1)*perPage;
        const end = start + perPage;

        cards.forEach((c,i)=>c.style.display = i>=start && i<end ? 'block':'none');
        if (info) info.textContent = `Showing ${start+1} to ${Math.min(end,total)} of ${total} entries`;
        renderPagination(pages);
    }

    function renderPagination(pages){
        if (!pagination) return;
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
        for(let i=s;i<=(e || 1);i++){
            pagination.appendChild(btn(i,false,i===currentPage,()=>{currentPage=i;renderMobile();}));
        }
        pagination.appendChild(btn('Next',currentPage===pages || pages === 0,false,()=>{currentPage++;renderMobile();}));
    }

    if (perPageSelect) {
        perPageSelect.onchange=()=>{perPage=parseInt(perPageSelect.value);currentPage=1;renderMobile();};
    }

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

    // Format Rupiah Helper
    function formatRupiahHelper(num) {
        num = num.toString();
        if (num.indexOf('.') !== -1) num = num.split('.')[0];
        if (num.indexOf(',') !== -1) num = num.split(',')[0];
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiahHelper(str) {
        return (str || '').toString().replace(/[^0-9]/g, '');
    }

    // Toggle modal bukti setor wrapper
    function toggleModalBuktiSetor() {
        const metode = document.getElementById('modal_metode_pembayaran').value;
        const wrapper = document.getElementById('modal_bukti_setor_wrapper');
        if (metode === 'tunai') {
            wrapper.style.display = 'none';
            document.getElementById('modal_bukti_setor').value = '';
        } else {
            wrapper.style.display = '';
        }
    }
    document.getElementById('modal_metode_pembayaran').addEventListener('change', toggleModalBuktiSetor);

    // Modal Pelunasan Logic
    window.openModalPelunasan = function(url, invoice, sisa) {
        const modal = document.getElementById('modalPelunasan');
        const form = document.getElementById('formPelunasan');
        const invoiceSpan = document.getElementById('modalInvoiceNumber');
        const sisaText = document.getElementById('modalSisaTagihanText');
        const inputJumlah = document.getElementById('modal_jumlah_bayar');
        const inputJumlahDisplay = document.getElementById('modal_jumlah_bayar_display');

        form.action = url;
        invoiceSpan.textContent = invoice;
        sisaText.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);
        
        inputJumlah.max = sisa;
        inputJumlah.value = '';
        inputJumlahDisplay.value = '';

        document.getElementById('modal_metode_pembayaran').value = 'tunai';
        toggleModalBuktiSetor();

        modal.classList.remove('hidden');
    }

    document.getElementById('modal_jumlah_bayar_display').addEventListener('input', function(e) {
        let val = unformatRupiahHelper(e.target.value);
        if (val === '') { 
            e.target.value = ''; 
            document.getElementById('modal_jumlah_bayar').value = '';
            return; 
        }
        e.target.value = formatRupiahHelper(val);
        document.getElementById('modal_jumlah_bayar').value = val;
    });

    // Form Pelunasan submit validation
    document.getElementById('formPelunasan').addEventListener('submit', function(e) {
        const valInput = parseFloat(document.getElementById('modal_jumlah_bayar').value || 0);
        const maxInput = parseFloat(document.getElementById('modal_jumlah_bayar').max || 0);
        if (valInput > maxInput) {
            e.preventDefault();
            const kelebihan = valInput - maxInput;
            Swal.fire({
                icon: 'error',
                title: 'Kelebihan Pembayaran',
                text: `Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ${formatRupiahHelper(maxInput)}). Kelebihan Rp ${formatRupiahHelper(kelebihan)}.`,
                confirmButtonColor: '#2563eb'
            });
        }
    });

    window.closeModalPelunasan = function() {
        const modal = document.getElementById('modalPelunasan');
        modal.classList.add('hidden');
    }

    // Attach click events for buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-pelunasan');
        if (btn) {
            const url = btn.getAttribute('data-url');
            const invoice = btn.getAttribute('data-invoice');
            const sisa = btn.getAttribute('data-sisa');
            openModalPelunasan(url, invoice, sisa);
        }
    });
});
</script>
@endpush