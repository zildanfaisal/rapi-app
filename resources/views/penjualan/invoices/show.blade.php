@extends('layouts.app')

@section('title', __('Detail Penjualan'))


@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Penjualan') }}</h2>
@endsection



@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-2 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                @if ($errors->any())
                    <div class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Detail Penjualan') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('invoices.index') }}" class="inline-block px-3 py-1.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('Kembali') }}</a>
                    </div>
                </div>

                <div class="p-2 space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Nomor Invoice</div>
                            <div class="font-semibold">{{ $invoice->invoice_number }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Pelanggan</div>
                            <div class="font-semibold">{{ $invoice->customer->nama_customer ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tanggal Invoice</div>
                            <div class="font-semibold">{{ $invoice->tanggal_invoice }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Jatuh Tempo</div>
                            <div class="font-semibold">{{ $invoice->tanggal_jatuh_tempo }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Pembayaran</div>
                            @php $status = $invoice->status_pembayaran; @endphp
                            @if ($status === 'paid')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                            @elseif ($status === 'partial')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Cicilan</span>
                            @elseif ($status === 'unpaid')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                            @elseif ($status === 'overdue')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Terlambat</span>
                            @elseif ($status === 'cancelled')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                            @else
                                <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="text-gray-600">Metode Pembayaran</div>
                            <div class="font-semibold">{{ ucfirst($invoice->metode_pembayaran ?? '-') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Setor</div>
                            @php $isSetor = ($invoice->status_setor ?? 'belum') === 'sudah'; @endphp
                            <span class="inline-block px-2 py-1 rounded text-xs {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $isSetor ? 'Setor' : 'Belum' }}</span>
                        </div>
                        <div>
                            <div class="text-gray-600">Bukti Pembayaran</div>
                            {!! $invoice->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$invoice->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                            <div class="text-gray-600">Total Tagihan</div>
                            <div class="font-semibold">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-green-50 border border-green-200">
                            <div class="text-green-700">Total Dibayar</div>
                            <div class="font-semibold text-green-800">Rp {{ number_format($invoice->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50 border border-orange-200">
                            <div class="text-orange-700">Sisa Tagihan</div>
                            <div class="font-semibold text-orange-800">Rp {{ number_format($invoice->sisa_tagihan, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Produk</th>
                                    <th class="px-4 py-2 border">Kuantitas</th>
                                    <th class="px-4 py-2 border">Harga</th>
                                    <th class="px-4 py-2 border">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $item->product->nama_produk ?? $item->product->nama ?? 'Produk #'.$item->product_id }}</td>
                                        <td class="px-4 py-2 border">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 border text-right font-semibold">
                                        Ongkos Kirim <span class="text-xs text-gray-500">(+)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-green-600">
                                        + Rp {{ number_format($invoice->ongkos_kirim ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="px-4 py-2 border text-right font-semibold">
                                        Diskon <span class="text-xs text-gray-500">(−)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-red-600">
                                        − Rp {{ number_format($invoice->diskon ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-2 border text-right font-bold">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2 border font-bold">
                                        Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-6">
                        <h4 class="text-md font-semibold mb-3">Riwayat Pembayaran</h4>
                        @if($invoice->pembayarans->isEmpty())
                            <p class="text-sm text-gray-500">Belum ada pembayaran yang dicatat.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-300 text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 border">Tanggal</th>
                                            <th class="px-4 py-2 border text-right">Jumlah</th>
                                            <th class="px-4 py-2 border">Metode</th>
                                            <th class="px-4 py-2 border">Catatan</th>
                                            <th class="px-4 py-2 border">Bukti</th>
                                            <th class="px-4 py-2 border">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->pembayarans as $pembayaran)
                                            <tr>
                                                <td class="px-4 py-2 border text-center">{{ $pembayaran->tanggal_bayar }}</td>
                                                <td class="px-4 py-2 border text-right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 border text-center">{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                                                <td class="px-4 py-2 border">{{ $pembayaran->catatan ?? '-' }}</td>
                                                <td class="px-4 py-2 border text-center">
                                                    @if($pembayaran->bukti_pembayaran)
                                                        <a href="{{ asset('storage/'.$pembayaran->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 border text-center whitespace-nowrap">
                                                    @can('invoices.update')
                                                        <button type="button"
                                                            class="btn-edit-pembayaran text-blue-600 hover:underline"
                                                            data-url="{{ route('invoices.pembayaran.update', $pembayaran) }}"
                                                            data-jumlah="{{ $pembayaran->jumlah_bayar }}"
                                                            data-tanggal="{{ $pembayaran->tanggal_bayar }}"
                                                            data-metode="{{ $pembayaran->metode_pembayaran }}"
                                                            data-catatan="{{ $pembayaran->catatan }}">
                                                            Edit
                                                        </button>
                                                        <form action="{{ route('invoices.pembayaran.destroy', $pembayaran) }}" method="POST" class="inline" onsubmit="return confirm('Hapus riwayat pembayaran ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="ml-2 text-red-600 hover:underline">Hapus</button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    @if($invoice->status_pembayaran === 'cancelled')
                    <div>
                        <div class="text-gray-600">Alasan Batal</div>
                        <div class="font-semibold">{{ $invoice->alasan_cancel }}</div>
                    </div>
                    @endif
                    <div>
                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" rel="noopener" class="inline-block px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700">{{ __('Buat Invoice') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-pembayaran" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="fixed inset-0 bg-gray-900/50" data-close-edit-pembayaran></div>
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Edit Riwayat Pembayaran</h3>
                <button type="button" data-close-edit-pembayaran class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="form-edit-pembayaran" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_jumlah_bayar_display" class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                        <input type="text" id="edit_jumlah_bayar_display" class="flex-1 w-full rounded-none rounded-r-md border-gray-300" autocomplete="off" required>
                        <input type="hidden" name="jumlah_bayar" id="edit_jumlah_bayar">
                    </div>
                </div>
                <div>
                    <label for="edit_tanggal_bayar" class="block text-sm font-medium text-gray-700">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar" class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label for="edit_metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="edit_metode_pembayaran" class="mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div id="edit-bukti-pembayaran-wrapper">
                    <label for="edit_bukti_pembayaran" class="block text-sm font-medium text-gray-700">Ganti Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" id="edit_bukti_pembayaran" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300">
                    <p class="mt-1 text-xs text-gray-500">Opsional; hanya untuk transfer atau QRIS.</p>
                </div>
                <div>
                    <label for="edit_catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <input type="text" name="catatan" id="edit_catatan" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" data-close-edit-pembayaran class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modal-edit-pembayaran');
        const form = document.getElementById('form-edit-pembayaran');
        const jumlahDisplay = document.getElementById('edit_jumlah_bayar_display');
        const jumlah = document.getElementById('edit_jumlah_bayar');
        const metode = document.getElementById('edit_metode_pembayaran');
        const buktiWrapper = document.getElementById('edit-bukti-pembayaran-wrapper');

        const toggleBukti = () => {
            buktiWrapper.style.display = metode.value === 'tunai' ? 'none' : '';
        };

        document.querySelectorAll('.btn-edit-pembayaran').forEach((button) => {
            button.addEventListener('click', () => {
                form.action = button.dataset.url;
                jumlah.value = button.dataset.jumlah;
                jumlahDisplay.value = new Intl.NumberFormat('id-ID').format(button.dataset.jumlah);
                document.getElementById('edit_tanggal_bayar').value = button.dataset.tanggal;
                metode.value = button.dataset.metode;
                document.getElementById('edit_catatan').value = button.dataset.catatan || '';
                document.getElementById('edit_bukti_pembayaran').value = '';
                toggleBukti();
                modal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('[data-close-edit-pembayaran]').forEach((button) => {
            button.addEventListener('click', () => modal.classList.add('hidden'));
        });

        jumlahDisplay.addEventListener('input', function () {
            const value = this.value.replace(/\D/g, '');
            jumlah.value = value;
            this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        });
        metode.addEventListener('change', toggleBukti);
    });
</script>
@endpush
