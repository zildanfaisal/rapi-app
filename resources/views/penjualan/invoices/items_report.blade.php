@extends('layouts.app')

@section('title', __('Laporan Penjualan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Laporan Penjualan') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- Filter Tanggal --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('invoices.report.items') }}" class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Dari (Tanggal Kirim)</label>
                        <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                               class="w-full px-3 py-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Sampai (Tanggal Kirim)</label>
                        <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                               class="w-full px-3 py-2.5 border rounded-lg">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('invoices.report.items') }}"
                       class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold">Laporan Item Terjual</h3>
                        <div class="text-xs text-gray-500 mt-1">Sumber: invoice lunas + surat jalan sudah dikirim</div>
                    </div>

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

                        <a href="{{ route('invoices.index', array_filter(['date_from' => $dateFrom ?? null, 'date_to' => $dateTo ?? null])) }}"
                           class="inline-flex items-center justify-center gap-2
                                  px-4 py-2.5
                                  bg-gray-200 text-gray-800 text-sm font-medium
                                  rounded-lg hover:bg-gray-300
                                  w-full sm:w-auto">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Produk</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Terjual</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Harga Beli</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Harga Jual</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Selisih</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Total</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Total Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($rows as $r)
                                @php
                                    $produk = trim((string) ($r->nama_produk ?? '-'));
                                    $label = $produk;
                                    $hargaBeli = (float) ($r->harga_beli ?? 0);
                                    $harga = (float) ($r->harga_jual ?? 0);
                                    $terjual = (int) ($r->terjual ?? 0);
                                    $selisih = (float) ($harga - $hargaBeli);
                                    $total = (float) ($r->total ?? 0);
                                    $totalSelisih = (float) ($selisih * $terjual);
                                @endphp
                                <tr class="hover:bg-gray-50 text-center">
                                    <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-3 py-2 text-left">{{ $label }}</td>
                                    <td class="border px-3 py-2">{{ $terjual }}</td>
                                    <td class="border px-3 py-2 text-right" data-export="{{ $hargaBeli }}">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                    <td class="border px-3 py-2 text-right" data-export="{{ $harga }}">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                    <td class="border px-3 py-2 text-right" data-export="{{ $selisih }}">Rp {{ number_format($selisih, 0, ',', '.') }}</td>
                                    <td class="border px-3 py-2 text-right" data-export="{{ $total }}">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td class="border px-3 py-2 text-right" data-export="{{ $totalSelisih }}">Rp {{ number_format($totalSelisih, 0, ',', '.') }}</td>
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
    document.addEventListener('DOMContentLoaded', function() {

        const filterFrom = @json($dateFrom ?? null);
        const filterTo = @json($dateTo ?? null);

        function initDesktopTable() {
            if (!$.fn.DataTable.isDataTable('#dataTables')) {
                $('#dataTables').DataTable({
                    responsive: true,
                });
            }
            return $('#dataTables').DataTable();
        }

        // init table immediately (page ini hanya desktop-style table)
        initDesktopTable();

        $('#excelBtn').off('click').on('click', function() {
            const table = initDesktopTable();

            // GRAND TOTAL sederhana:
            // Grand Total Jual (sum kolom Total) - Grand Total Beli (qty * harga_beli) = Selisih
            let grandTotalJual = 0;
            let grandTotalBeli = 0;

            table.rows({ search: 'applied' }).every(function() {
                const rowNode = this.node();
                const $cells = $(rowNode).find('td');

                const qty = parseInt(($cells.eq(2).text() || '0').replace(/[^0-9-]/g, ''), 10) || 0;
                const hargaBeli = parseFloat($cells.eq(3).data('export')) || 0;
                const totalJual = parseFloat($cells.eq(6).data('export')) || 0;

                grandTotalJual += totalJual;
                grandTotalBeli += (qty * hargaBeli);
            });

            const grandSelisih = grandTotalJual - grandTotalBeli;

            const buttons = new $.fn.dataTable.Buttons(table, {
                buttons: [{
                    extend: 'excelHtml5',
                    title: null,
                    exportOptions: {
                        // No, Produk, Terjual, Harga Beli, Harga Jual, Selisih, Total, Total Selisih
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        format: {
                            body: function(data, row, column, node) {
                                // Kolom angka: ambil dari data-export supaya Excel dapat numeric
                                if ([3, 4, 5, 6, 7].includes(column)) {
                                    const v = $(node).data('export');
                                    return (v === undefined || v === null || v === '') ? 0 : v;
                                }
                                return data;
                            }
                        }
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
                        const rangeLabel = (filterFrom || filterTo)
                            ? ` | Dari: ${filterFrom || '-'} Sampai: ${filterTo || '-'}`
                            : '';
                        const sheetData = $('sheetData', sheet);

                        sheetData.prepend(`<row r="2"><c r="A2" t="inlineStr" s="${styleTitle}"><is><t>Dicetak: ${now}${rangeLabel}</t></is></c></row>`);
                        sheetData.prepend(`<row r="1"><c r="A1" t="inlineStr" s="${styleTitle}"><is><t>LAPORAN ITEM TERJUAL</t></is></c></row>`);

                        // Merge title A..H
                        let mergeCells = $('mergeCells', sheet);
                        if (!mergeCells.length) {
                            $('worksheet', sheet).append('<mergeCells count="0"></mergeCells>');
                            mergeCells = $('mergeCells', sheet);
                        }
                        const mergeCount = parseInt(mergeCells.attr('count') || '0', 10);
                        mergeCells.append('<mergeCell ref="A1:H1"/>');
                        mergeCells.append('<mergeCell ref="A2:H2"/>');
                        mergeCells.attr('count', mergeCount + 2);

                        // Style header + body
                        $('row[r="3"] c', sheet).attr('s', styleHeader);
                        $('row:gt(2) c', sheet).attr('s', styleCell);
                        // D..H as numbers
                        $('row:gt(2) c[r^="D"]', sheet).attr('s', styleNumber);
                        $('row:gt(2) c[r^="E"]', sheet).attr('s', styleNumber);
                        $('row:gt(2) c[r^="F"]', sheet).attr('s', styleNumber);
                        $('row:gt(2) c[r^="G"]', sheet).attr('s', styleNumber);
                        $('row:gt(2) c[r^="H"]', sheet).attr('s', styleNumber);

                        // Column width
                        let cols = $('cols', sheet);
                        if (!cols.length) {
                            $('worksheet', sheet).prepend('<cols></cols>');
                            cols = $('cols', sheet);
                        }
                        cols.html(`
                          <col min="1" max="1" width="8" customWidth="1"/>
                          <col min="2" max="2" width="35" customWidth="1"/>
                          <col min="3" max="3" width="12" customWidth="1"/>
                                                    <col min="4" max="4" width="16" customWidth="1"/>
                                                    <col min="5" max="5" width="16" customWidth="1"/>
                                                    <col min="6" max="6" width="16" customWidth="1"/>
                                                    <col min="7" max="7" width="18" customWidth="1"/>
                                                    <col min="8" max="8" width="18" customWidth="1"/>
                        `);

                        // Grand total row
                        let lastRow = $('row', sheet).last();
                        let rowNum = parseInt(lastRow.attr('r'), 10) + 1;

                        sheetData.append(`
                          <row r="${rowNum}">
                                                        <c r="A${rowNum}" t="inlineStr" s="${styleGrand}"><is><t>GRAND TOTAL PENJUALAN</t></is></c>
                                                        <c r="G${rowNum}" s="${styleGrand}"><v>${grandTotalJual}</v></c>
                          </row>
                        `);

                                                const rowNum2 = rowNum + 1;
                                                sheetData.append(`
                                                    <row r="${rowNum2}">
                                                        <c r="A${rowNum2}" t="inlineStr" s="${styleGrand}"><is><t>GRAND TOTAL BELI</t></is></c>
                                                        <c r="G${rowNum2}" s="${styleGrand}"><v>${grandTotalBeli}</v></c>
                                                    </row>
                                                `);

                                                const rowNum3 = rowNum + 2;
                                                sheetData.append(`
                                                    <row r="${rowNum3}">
                                                        <c r="A${rowNum3}" t="inlineStr" s="${styleGrand}"><is><t>SELISIH (JUAL - BELI)</t></is></c>
                                                        <c r="G${rowNum3}" s="${styleGrand}"><v>${grandSelisih}</v></c>
                                                    </row>
                                                `);

                        const mergeCount2 = parseInt(mergeCells.attr('count') || '0', 10);
                                                mergeCells.append(`<mergeCell ref="A${rowNum}:F${rowNum}"/>`);
                                                mergeCells.append(`<mergeCell ref="A${rowNum2}:F${rowNum2}"/>`);
                                                mergeCells.append(`<mergeCell ref="A${rowNum3}:F${rowNum3}"/>`);
                                                mergeCells.attr('count', mergeCount2 + 3);
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
