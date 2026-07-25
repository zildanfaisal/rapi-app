@extends('layouts.app')

@section('title', __('Detail Pembelian'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Pembelian') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-2 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Detail Pembelian') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('pembelian.index') }}" class="inline-block px-3 py-1.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('Kembali') }}</a>
                    </div>
                </div>

                <div class="p-2 space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Nomor Invoice</div>
                            <div class="font-semibold">{{ $pembelian->invoice_number }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Supplier</div>
                            <div class="font-semibold">{{ $pembelian->supplier->nama_supplier ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tanggal Pembelian</div>
                            <div class="font-semibold">{{ $pembelian->tanggal_pembelian }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">User</div>
                            <div class="font-semibold">{{ $pembelian->user->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Pembayaran</div>
                            @php $status = $pembelian->status_pembayaran; @endphp
                            @if ($status === 'paid')
                                <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                            @elseif ($status === 'partial' || $status === 'unpaid')
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
                            <div class="font-semibold">{{ ucfirst($pembelian->metode_pembayaran ?? '-') }}</div>
                        </div>
                        {{-- <div>
                            <div class="text-gray-600">Status Setor</div>
                            @php $isSetor = ($pembelian->status_setor ?? 'belum') === 'sudah'; @endphp
                            <span class="inline-block px-2 py-1 rounded text-xs {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $isSetor ? 'Setor' : 'Belum' }}</span>
                        </div> --}}
                        <div>
                            <div class="text-gray-600">Bukti Pembayaran</div>
                            {!! $pembelian->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$pembelian->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Produk</th>
                                    <th class="px-4 py-2 border">Batch Number</th>
                                    <th class="px-4 py-2 border">Kuantitas</th>
                                    <th class="px-4 py-2 border">Harga Beli</th>
                                    <th class="px-4 py-2 border">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->items as $item)
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $item->product->nama_produk ?? 'Produk #'.$item->product_id }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $item->batch->batch_number ?? '-' }}
                                            @if($item->batch && $item->batch->tanggal_expired)
                                                <div class="text-xs text-gray-500 mt-1">Exp: {{ \Carbon\Carbon::parse($item->batch->tanggal_expired)->format('d/m/Y') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="4" class="px-4 py-2 border text-right font-bold">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2 border font-bold text-center">
                                        Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($pembelian->status_pembayaran === 'cancelled' && $pembelian->alasan_cancel)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="text-gray-600 mb-1">Alasan Batal</div>
                        <div class="font-semibold">{{ $pembelian->alasan_cancel }}</div>
                    </div>
                    @endif

                    {{-- Riwayat Pembayaran --}}
                    @if($pembelian->status_pembayaran !== 'paid' || $pembelian->pembayarans->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-md font-semibold mb-3">Riwayat Pembayaran (Cicilan)</h4>
                        @if($pembelian->pembayarans->isEmpty())
                            <p class="text-sm text-gray-500">Belum ada riwayat pembayaran.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr class="text-center">
                                            <th class="px-4 py-2 border">Tanggal</th>
                                            <th class="px-4 py-2 border">Jumlah Bayar</th>
                                            <th class="px-4 py-2 border">Metode</th>
                                            <th class="px-4 py-2 border">Catatan</th>
                                            <th class="px-4 py-2 border">Bukti</th>
                                            <th class="px-4 py-2 border">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalBayarSebelumnya = 0;
                                        @endphp
                                        @foreach($pembelian->pembayarans as $pb)
                                            <tr class="text-center">
                                                <td class="px-4 py-2 border">{{ $pb->tanggal_bayar }}</td>
                                                <td class="px-4 py-2 border text-right">Rp {{ number_format($pb->jumlah_bayar, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 border">{{ ucfirst($pb->metode_pembayaran) }}</td>
                                                <td class="px-4 py-2 border text-left">{{ $pb->catatan ?? '-' }}</td>
                                                <td class="px-4 py-2 border">
                                                    @if($pb->bukti_setor)
                                                        <a href="{{ asset('storage/' . $pb->bukti_setor) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 border whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button type="button"
                                                                class="text-blue-600 hover:text-blue-900 font-semibold hover:underline btn-edit-pembayaran"
                                                                data-id="{{ $pb->id }}"
                                                                data-jumlah="{{ $pb->jumlah_bayar }}"
                                                                @php
                                                                    // Sisa tagihan untuk cicilan ini adalah: grand_total - total cicilan lain
                                                                    $sisaMaks = $pembelian->grand_total - ($pembelian->pembayarans->sum('jumlah_bayar') - $pb->jumlah_bayar);
                                                                @endphp
                                                                data-max-jumlah="{{ $sisaMaks }}"
                                                                data-tanggal="{{ $pb->tanggal_bayar }}"
                                                                data-metode="{{ $pb->metode_pembayaran }}"
                                                                data-catatan="{{ $pb->catatan }}"
                                                                data-url="{{ route('pembelian.bayar.update', $pb->id) }}">
                                                            Edit
                                                        </button>

                                                        <form action="{{ route('pembelian.bayar.destroy', $pb->id) }}" method="POST" data-confirm-delete class="inline m-0">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold hover:underline">
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
                        @endif
                    </div>
                    @endif


                    <div class="mt-6 flex flex-wrap gap-3">
                        @can('pembelian.update')
                        <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Edit Pembelian') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal Edit Pembayaran --}}
<div id="modalEditPembayaran" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeModalEditPembayaran()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formEditPembayaran" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start w-full">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                Edit Pembayaran
                            </h3>
                            
                            <div class="space-y-4 text-sm">
                                <div>
                                    <label for="edit_jumlah_bayar_display" class="block font-medium text-gray-700">Jumlah Bayar</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            Rp
                                        </span>
                                        <input type="text" id="edit_jumlah_bayar_display" required
                                            class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                            autocomplete="off">
                                        <input type="hidden" name="jumlah_bayar" id="edit_jumlah_bayar" required min="1">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_tanggal_bayar" class="block font-medium text-gray-700">Tanggal Bayar</label>
                                    <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="edit_metode_pembayaran" class="block font-medium text-gray-700">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" id="edit_metode_pembayaran" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                        <option value="tunai">Tunai (Cash)</option>
                                        <option value="transfer">Transfer (TF)</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>

                                <div id="edit_bukti_setor_wrapper">
                                    <label for="edit_bukti_setor" class="block font-medium text-gray-700">Bukti Pembayaran</label>
                                    <input type="file" name="bukti_setor" id="edit_bukti_setor" accept="image/*"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>

                                <div>
                                    <label for="edit_catatan" class="block font-medium text-gray-700">Catatan</label>
                                    <input type="text" name="catatan" id="edit_catatan" placeholder="Catatan pembayaran..."
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeModalEditPembayaran()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
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

    // Toggle edit modal bukti setor wrapper
    function toggleEditBuktiSetor() {
        const metode = document.getElementById('edit_metode_pembayaran').value;
        const wrapper = document.getElementById('edit_bukti_setor_wrapper');
        if (metode === 'tunai') {
            wrapper.style.display = 'none';
            document.getElementById('edit_bukti_setor').value = '';
        } else {
            wrapper.style.display = '';
        }
    }
    document.getElementById('edit_metode_pembayaran').addEventListener('change', toggleEditBuktiSetor);

    // Modal Edit Pembayaran Logic
    window.openModalEditPembayaran = function(url, jumlah, maxJumlah, tanggal, metode, catatan) {
        const modal = document.getElementById('modalEditPembayaran');
        const form = document.getElementById('formEditPembayaran');
        const inputJumlah = document.getElementById('edit_jumlah_bayar');
        const inputJumlahDisplay = document.getElementById('edit_jumlah_bayar_display');
        const inputTanggal = document.getElementById('edit_tanggal_bayar');
        const inputMetode = document.getElementById('edit_metode_pembayaran');
        const inputCatatan = document.getElementById('edit_catatan');

        form.action = url;
        inputJumlah.value = jumlah;
        inputJumlah.max = maxJumlah;
        inputJumlahDisplay.value = formatRupiahHelper(jumlah);
        inputTanggal.value = tanggal;
        inputMetode.value = metode;
        inputCatatan.value = catatan || '';

        toggleEditBuktiSetor();

        modal.classList.remove('hidden');
    }

    document.getElementById('edit_jumlah_bayar_display').addEventListener('input', function(e) {
        let val = unformatRupiahHelper(e.target.value);
        if (val === '') { 
            e.target.value = ''; 
            document.getElementById('edit_jumlah_bayar').value = '';
            return; 
        }
        e.target.value = formatRupiahHelper(val);
        document.getElementById('edit_jumlah_bayar').value = val;
    });

    // Form Edit Pembayaran submit validation
    document.getElementById('formEditPembayaran').addEventListener('submit', function(e) {
        const valInput = parseFloat(document.getElementById('edit_jumlah_bayar').value || 0);
        const maxInput = parseFloat(document.getElementById('edit_jumlah_bayar').max || 0);
        if (valInput > maxInput) {
            e.preventDefault();
            const kelebihan = valInput - maxInput;
            Swal.fire({
                icon: 'error',
                title: 'Kelebihan Pembayaran',
                text: `Jumlah bayar tidak boleh melebihi batas maksimal (Rp ${formatRupiahHelper(maxInput)}). Kelebihan Rp ${formatRupiahHelper(kelebihan)}.`,
                confirmButtonColor: '#2563eb'
            });
        }
    });

    window.closeModalEditPembayaran = function() {
        const modal = document.getElementById('modalEditPembayaran');
        modal.classList.add('hidden');
    }

    // Attach click events for edit buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-pembayaran');
        if (btn) {
            const url = btn.getAttribute('data-url');
            const jumlah = btn.getAttribute('data-jumlah');
            const maxJumlah = btn.getAttribute('data-max-jumlah');
            const tanggal = btn.getAttribute('data-tanggal');
            const metode = btn.getAttribute('data-metode');
            const catatan = btn.getAttribute('data-catatan');
            openModalEditPembayaran(url, jumlah, maxJumlah, tanggal, metode, catatan);
        }
    });
});
</script>
@endpush
