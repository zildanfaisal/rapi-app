@extends('layouts.app')

@section('title', __('Product Batches'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Product Batches') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Product Batches</h3>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">

                        <button
                            type="button"
                            id="excelBtn"
                            class="inline-flex items-center justify-center gap-2
               px-4 py-2.5
               bg-green-600 text-white text-sm font-medium
               rounded-lg hover:bg-green-700
               w-full sm:w-auto">
                            Export Excel
                        </button>

                        <button
                            @click="window.dispatchEvent(new CustomEvent('open-batch-report'))"
                            class="inline-flex items-center justify-center gap-2
               px-4 py-2.5
               bg-red-600 text-white text-sm font-medium
               rounded-lg hover:bg-red-700
               w-full sm:w-auto">
                            Export PDF
                        </button>

                        <a href="{{ route('product-batches.create') }}"
                            class="inline-flex items-center justify-center gap-2
              px-4 py-2.5
              bg-blue-600 text-white text-sm font-medium
              rounded-lg hover:bg-blue-700
              w-full sm:w-auto">
                            + Tambah Batch
                        </a>

                    </div>

                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Produk</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Kode Batch</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Masuk</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Kadaluwarsa</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Awal Stok</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Sisa Stok</th>

                                <th class="hidden px-3 py-2 border text-center text-xs uppercase">Harga Beli</th>
                                <th class="hidden px-3 py-2 border text-center text-xs uppercase">Total</th>

                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($batches as $b)
                            @php
                            $expired = \Carbon\Carbon::parse($b->tanggal_expired);
                            $now = \Carbon\Carbon::now();
                            $diffMonths = $now->diffInMonths($expired, false);
                            $hargaBeli = (float) ($b->product->harga_beli ?? 0);
                            $totalNilai = ((int) ($b->quantity_sekarang ?? 0)) * $hargaBeli;
                            @endphp
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-left">{{ $b->product->nama_produk }}</td>
                                <td class="border px-3 py-2 font-semibold text-left">{{ $b->batch_number }}</td>

                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($b->tanggal_masuk)->translatedFormat('F') }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($b->tanggal_expired)->translatedFormat('F') }}
                                </td>
                                <td class="border px-3 py-2">{{ $b->quantity_masuk }}</td>
                                <td class="border px-3 py-2">{{ $b->quantity_sekarang }}</td>

                                <td class="hidden border px-3 py-2">{{ $hargaBeli }}</td>
                                <td class="hidden border px-3 py-2">{{ $totalNilai }}</td>

                                <td class="border px-3 py-2">
                                    @php $status = $b->status; @endphp
                                    @if ($status === 'sold_out')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Habis Terjual</span>
                                    @elseif ($status === 'expired')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Kadaluwarsa</span>
                                    @else
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('product-batches.edit', $b->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('product-batches.destroy', $b->id) }}" method="POST" class="inline" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline ms-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden w-full" id="mobileCardWrapper">

                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="mx-1 border-gray-300 rounded text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                            entries
                        </div>
                    </div>

                    <div id="mobileCards" class="space-y-3">
                        @foreach ($batches as $b)
                        <div class="mobile-card border rounded-lg shadow-sm bg-white">

                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="text-xs text-gray-500">Batch</div>
                                <h4 class="font-semibold">{{ $b->batch_number }}</h4>
                                <div class="text-sm text-gray-600">{{ $b->product->nama_produk }}</div>
                            </div>

                            <div class="px-4 py-3 text-sm space-y-1">

                                <div>Masuk: {{ \Carbon\Carbon::parse($b->tanggal_masuk)->translatedFormat('F') }}</div>
                                <div>Kadaluwarsa: {{ \Carbon\Carbon::parse($b->tanggal_expired)->translatedFormat('F') }}</div>
                                <div>Awal Stok: {{ $b->quantity_masuk }}</div>
                                <div>Sisa Stok: {{ $b->quantity_sekarang }}</div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">

                                <a href="{{ route('product-batches.edit', $b) }}"
                                    class="flex-1 text-center
                                          px-3 py-2
                                          border border-blue-600
                                          text-blue-600
                                          rounded-md text-sm
                                          hover:bg-blue-50">
                                    Edit
                                </a>

                                <form action="{{ route('product-batches.destroy', $b) }}"
                                    method="POST"
                                    class="flex-1"
                                    data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-2
                                                   border border-red-600
                                                   text-red-600
                                                   rounded-md text-sm
                                                   hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL LAPORAN --}}
<div
    x-data="{ showBatchModal: false }"
    x-on:open-batch-report.window="showBatchModal = true"
    x-show="showBatchModal"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    style="display: none;">

    <div
        x-show="showBatchModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showBatchModal = false"
        class="absolute inset-0 bg-black/50 backdrop-blur-sm">
    </div>

    <div
        x-show="showBatchModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-90 translate-y-4"
        class="relative bg-white rounded-xl w-full max-w-md p-6 shadow-2xl z-10 mx-4">

        <h2 class="text-lg font-bold text-slate-800 mb-4">Filter Laporan Batch Produk</h2>

        <form action="{{ route('product-batches.report') }}" method="GET" target="_blank">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Filter</label>
                <select name="filter" class="w-full border-slate-300 rounded-lg p-2.5" required>
                    <option value="all">Semua</option>
                    <option value="tanggal_masuk">Tanggal Masuk</option>
                    <option value="tanggal_expired">Tanggal Kadaluwarsa</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="month" name="start_date" class="w-full border-slate-300 rounded-lg p-2.5">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="month" name="end_date" class="w-full border-slate-300 rounded-lg p-2.5">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="showBatchModal = false"
                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">
                    Batal
                </button>

                <button type="reset"
                    class="px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100">
                    Reset
                </button>

                <button type="submit" @click="showBatchModal = false"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Generate PDF
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // =======================
        // MOBILE PAGINATION
        // =======================
        const cards = [...document.querySelectorAll('.mobile-card')];
        const pagination = document.getElementById('mobilePagination');
        const info = document.getElementById('mobileInfo');
        const perPageSelect = document.getElementById('mobilePerPage');

        let perPage = parseInt(perPageSelect.value);
        let currentPage = 1;

        function renderPagination(totalPages) {
            pagination.innerHTML = '';
            const max = 5;
            let s = Math.max(1, currentPage - 2);
            let e = Math.min(totalPages, s + max - 1);

            const btn = (t, d, a, cb) => {
                const b = document.createElement('button');
                b.textContent = t;
                b.disabled = d;
                b.className = `px-3 py-1 text-sm border rounded
                ${a ? 'bg-blue-600 text-white' : 'bg-white'}
                ${d ? 'opacity-50' : ''}`;
                b.onclick = cb;
                return b;
            };

            pagination.appendChild(btn('Prev', currentPage === 1, false, () => {
                currentPage--;
                renderMobile();
            }));

            for (let i = s; i <= e; i++) {
                pagination.appendChild(btn(i, false, i === currentPage, () => {
                    currentPage = i;
                    renderMobile();
                }));
            }

            pagination.appendChild(btn('Next', currentPage === totalPages, false, () => {
                currentPage++;
                renderMobile();
            }));
        }

        function renderMobile() {
            const total = cards.length;
            const totalPages = Math.ceil(total / perPage);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            cards.forEach((c, i) => c.style.display = (i >= start && i < end) ? 'block' : 'none');
            info.textContent = `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;
            renderPagination(totalPages);
        }

        perPageSelect.onchange = () => {
            perPage = parseInt(perPageSelect.value);
            currentPage = 1;
            renderMobile();
        };

        // =======================
        // DESKTOP DATATABLE (JQUERY)
        // =======================
        function initDesktopTable() {
            if (!$.fn.DataTable.isDataTable('#dataTables')) {
                $('#dataTables').DataTable({
                    columnDefs: [{
                        targets: [7, 8], // Harga Beli & Total (hidden)
                        visible: false,
                        searchable: false
                    }]
                });
            }
            return $('#dataTables').DataTable();
        }

        function handleResponsive() {
            if (window.innerWidth >= 1024) {
                initDesktopTable();
            } else {
                if ($.fn.DataTable.isDataTable('#dataTables')) {
                    $('#dataTables').DataTable().destroy();
                }
                renderMobile();
            }
        }

        handleResponsive();
        window.addEventListener('resize', handleResponsive);

        // =======================
        // EXCEL EXPORT (PASTI DOWNLOAD)
        // =======================
        $('#excelBtn').off('click').on('click', function() {

            const table = initDesktopTable(); // <-- INI YANG KAMU KURANG (table harus ada)

            // GRAND TOTAL dari kolom "Total" index 8
            let grandTotal = 0;
            table.column(8, {
                search: 'applied'
            }).data().each(function(value) {
                grandTotal += parseFloat(value) || 0;
            });

            const buttons = new $.fn.dataTable.Buttons(table, {
                buttons: [{
                    extend: 'excelHtml5',
                    title: null,
                    exportOptions: {
                        columns: [0, 1, 6, 7, 8]
                    },
                    customize: function(xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        const styles = xlsx.xl['styles.xml'];

                        let fonts = $('fonts', styles);
                        let fills = $('fills', styles);
                        let borders = $('borders', styles);
                        let cellXfs = $('cellXfs', styles);

                        // Bold font
                        fonts.append(`<font><b/><sz val="12"/><name val="Calibri"/></font>`);
                        fonts.attr('count', $('font', fonts).length);
                        const boldFontId = $('font', fonts).length - 1;

                        // Header bg
                        fills.append(`
                      <fill>
                        <patternFill patternType="solid">
                          <fgColor rgb="FFE5E7EB"/>
                          <bgColor indexed="64"/>
                        </patternFill>
                      </fill>
                    `);
                        fills.attr('count', $('fill', fills).length);
                        const headerFillId = $('fill', fills).length - 1;

                        // Border
                        borders.append(`<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>`);
                        borders.attr('count', $('border', borders).length);
                        const borderId = $('border', borders).length - 1;

                        // Number format
                        let numFmts = $('numFmts', styles);
                        if (!numFmts.length) {
                            styles.prepend('<numFmts count="0"/>');
                            numFmts = $('numFmts', styles);
                        }
                        const fmtCount = parseInt(numFmts.attr('count') || '0', 10);
                        numFmts.append(`<numFmt numFmtId="200" formatCode="#,##0"/>`);
                        numFmts.attr('count', fmtCount + 1);

                        const styleTitle = cellXfs.children().length;
                        const styleHeader = styleTitle + 1;
                        const styleCell = styleTitle + 2;
                        const styleNumber = styleTitle + 3;
                        const styleGrand = styleTitle + 4;

                        cellXfs.append(`<xf xfId="0" fontId="${boldFontId}" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>`);
                        cellXfs.append(`<xf xfId="0" fontId="${boldFontId}" fillId="${headerFillId}" borderId="${borderId}" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>`);
                        cellXfs.append(`<xf xfId="0" borderId="${borderId}" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>`);
                        cellXfs.append(`<xf xfId="0" borderId="${borderId}" numFmtId="200" applyBorder="1" applyNumberFormat="1" applyAlignment="1"><alignment vertical="center"/></xf>`);
                        cellXfs.append(`<xf xfId="0" fontId="${boldFontId}" fillId="${headerFillId}" borderId="${borderId}" numFmtId="200" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>`);
                        cellXfs.attr('count', cellXfs.children().length);

                        // SHIFT rows (buat judul)
                        $('row', sheet).each(function() {
                            const r = parseInt($(this).attr('r'), 10);
                            $(this).attr('r', r + 2);
                            $(this).find('c').each(function() {
                                const ref = $(this).attr('r');
                                const col = ref.replace(/[0-9]/g, '');
                                const row = parseInt(ref.replace(/[A-Z]/g, ''), 10);
                                $(this).attr('r', col + (row + 2));
                            });
                        });

                        const now = new Date().toLocaleString('id-ID');
                        const sheetData = $('sheetData', sheet);

                        sheetData.prepend(`<row r="2"><c r="A2" t="inlineStr" s="${styleTitle}"><is><t>Dicetak: ${now}</t></is></c></row>`);
                        sheetData.prepend(`<row r="1"><c r="A1" t="inlineStr" s="${styleTitle}"><is><t>LAPORAN BATCH PRODUK</t></is></c></row>`);

                        // Merge title A..E
                        let mergeCells = $('mergeCells', sheet);
                        if (!mergeCells.length) {
                            $('worksheet', sheet).append('<mergeCells count="0"></mergeCells>');
                            mergeCells = $('mergeCells', sheet);
                        }
                        const mergeCount = parseInt(mergeCells.attr('count') || '0', 10);
                        mergeCells.append('<mergeCell ref="A1:E1"/>');
                        mergeCells.append('<mergeCell ref="A2:E2"/>');
                        mergeCells.attr('count', mergeCount + 2);

                        // Style header + body
                        $('row[r="3"] c', sheet).attr('s', styleHeader);
                        $('row:gt(2) c', sheet).attr('s', styleCell);
                        $('row:gt(2) c[r^="E"]', sheet).attr('s', styleNumber);

                        // Column width
                        let cols = $('cols', sheet);
                        if (!cols.length) {
                            $('worksheet', sheet).prepend('<cols></cols>');
                            cols = $('cols', sheet);
                        }
                        cols.html(`
                      <col min="1" max="1" width="8" customWidth="1"/>
                      <col min="2" max="2" width="30" customWidth="1"/>
                      <col min="3" max="3" width="15" customWidth="1"/>
                      <col min="4" max="4" width="15" customWidth="1"/>
                      <col min="5" max="5" width="18" customWidth="1"/>
                    `);

                        // Grand total row
                        let lastRow = $('row', sheet).last();
                        let rowNum = parseInt(lastRow.attr('r'), 10) + 1;

                        sheetData.append(`
                      <row r="${rowNum}">
                        <c r="A${rowNum}" t="inlineStr" s="${styleGrand}"><is><t>GRAND TOTAL</t></is></c>
                        <c r="E${rowNum}" s="${styleGrand}"><v>${grandTotal}</v></c>
                      </row>
                    `);

                        const mergeCount2 = parseInt(mergeCells.attr('count') || '0', 10);
                        mergeCells.append(`<mergeCell ref="A${rowNum}:D${rowNum}"/>`);
                        mergeCells.attr('count', mergeCount2 + 1);
                    }
                }]
            });

            const $container = buttons.container().appendTo('body');
            $container.find('.buttons-excel').trigger('click');

            buttons.destroy();
            $container.remove();
        });

    });
</script>
@endpush