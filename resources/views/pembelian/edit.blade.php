@extends('layouts.app')

@section('title', __('Edit Pembelian'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Pembelian') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Edit Pembelian') }}</h3>
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('pembelian.update', $pembelian->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Supplier --}}
                        <div class="mb-4">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select name="supplier_id" id="supplier_id"
                                class="tom-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                <option value="" disabled>Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-nama="{{ $supplier->nama_supplier }}"
                                        @selected(old('supplier_id', $pembelian->supplier_id) == $supplier->id)>
                                        {{ $supplier->nama_supplier }} - {{ $supplier->alamat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                                <input type="date" name="tanggal_pembelian" id="tanggal_pembelian"
                                    value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="invoice_number" class="block text-sm font-medium text-gray-700">Nomor Invoice</label>
                                <div class="mt-1 flex gap-2">
                                    <input type="text" name="invoice_number" id="invoice_number"
                                        value="{{ old('invoice_number', $pembelian->invoice_number) }}"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                        readonly placeholder="Klik Generate">
                                    <button type="button" id="generate-invoice"
                                        class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">Generate</button>
                                </div>
                            </div>
                        </div>

                        {{-- Produk --}}
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold mb-2">Produk</h4>
                            <div id="items-wrapper" class="space-y-3">
                                @foreach ($pembelian->items as $idx => $existingItem)
                                <div class="item-row grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">Produk</label>
                                        <select name="items[{{ $idx }}][product_id]"
                                            class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                            <option value="" disabled>Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->harga_beli ?? ($product->harga ?? 0) }}"
                                                    @selected($existingItem->product_id == $product->id)>
                                                    {{ $product->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Qty</label>
                                        <input type="number" name="items[{{ $idx }}][quantity]"
                                            value="{{ $existingItem->quantity }}"
                                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Tgl Expired</label>
                                        <input type="date" name="items[{{ $idx }}][tanggal_expired]"
                                            value="{{ $existingItem->batch->tanggal_expired ?? '' }}"
                                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Harga Beli</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text"
                                                class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                                value="{{ number_format($existingItem->harga, 0, ',', '.') }}"
                                                placeholder="0" autocomplete="off" />
                                            <input type="hidden" name="items[{{ $idx }}][harga]" class="item-price" value="{{ $existingItem->harga }}" required />
                                            <button type="button"
                                                class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                                aria-label="Hapus item">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-item"
                                class="mt-3 px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">Tambah Produk</button>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="tunai"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio"
                                    @checked(old('metode_pembayaran', $pembelian->metode_pembayaran) == 'tunai')>
                                <span class="text-sm text-gray-700">Tunai (Cash)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="transfer"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio"
                                    @checked(old('metode_pembayaran', $pembelian->metode_pembayaran) == 'transfer')>
                                <span class="text-sm text-gray-700">Transfer (TF)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="qris"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio"
                                    @checked(old('metode_pembayaran', $pembelian->metode_pembayaran) == 'qris')>
                                <span class="text-sm text-gray-700">QRIS</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label for="status_pembayaran" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran"
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="paid" @selected(old('status_pembayaran', $pembelian->status_pembayaran) == 'paid')>Lunas</option>
                                <option value="unpaid" @selected(in_array(old('status_pembayaran', $pembelian->status_pembayaran), ['unpaid', 'partial', 'overdue']))>Belum Lunas</option>
                            </select>
                        </div>

                        {{-- Form Riwayat Pembelian (Cicilan) hanya tampil jika Belum Lunas --}}
                        <div id="riwayat-cicilan-wrapper" style="display: none;" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <h4 class="text-md font-semibold mb-3">Input Pembayaran / Cicilan (Opsional)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm">
                                <div>
                                    <label for="cicilan_jumlah_bayar_display" class="block font-medium text-gray-700">Jumlah Bayar</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-500">Rp</span>
                                        <input type="text" id="cicilan_jumlah_bayar_display" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" autocomplete="off">
                                        <input type="hidden" name="cicilan_jumlah_bayar" id="cicilan_jumlah_bayar">
                                    </div>
                                </div>
                                <div>
                                    <label for="cicilan_tanggal_bayar" class="block font-medium text-gray-700">Tanggal Bayar</label>
                                    <input type="date" name="cicilan_tanggal_bayar" id="cicilan_tanggal_bayar" value="{{ date('Y-m-d') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="cicilan_catatan" class="block font-medium text-gray-700">Catatan</label>
                                    <input type="text" name="cicilan_catatan" id="cicilan_catatan" placeholder="Catatan (Opsional)"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Unified Bukti Pembayaran Form (moved to the bottom) --}}
                        <div class="mb-4" id="bukti-pembayaran-wrapper" style="display:none;">
                            <label for="bukti_setor" class="block text-sm font-medium text-gray-700">
                                Bukti Pembayaran
                                <span class="text-xs text-gray-500">(Wajib untuk Transfer/QRIS)</span>
                            </label>
                            @if ($pembelian->bukti_setor)
                                <div class="mt-2 mb-2">
                                    <p class="text-xs text-gray-600 mb-1">Bukti saat ini:</p>
                                    <img src="{{ asset('storage/' . $pembelian->bukti_setor) }}" alt="Bukti Setor" class="h-32 rounded border">
                                </div>
                            @endif
                            <input type="file" name="bukti_setor" id="bukti_setor" accept="image/*"
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB). Kosongkan jika tidak ingin mengganti.</p>
                            <div class="mt-2 hidden" id="preview-bukti-pembayaran">
                                <p class="text-xs text-gray-600 mb-1">Preview:</p>
                                <div class="relative inline-block">
                                    <img id="preview-img-pembayaran" src="" alt="Preview" class="h-32 rounded border">
                                    <button type="button" onclick="cancelPreviewPembayaran()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm">×</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-right">
                                <span class="text-sm text-gray-600">Grand Total</span>
                                <span id="grand-total" class="text-lg font-semibold">Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                            <a href="{{ route('pembelian.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    let index = {{ $pembelian->items->count() }};
    const wrapper = document.getElementById('items-wrapper');
    const addBtn = document.getElementById('add-item');
    const grandEl = document.getElementById('grand-total');

    const PRODUCT_CATALOG = {!! json_encode(
        $products->map(fn($p) => [
            'id' => $p->id,
            'price' => $p->harga_beli ?? ($p->harga ?? 0),
            'name' => $p->nama_produk ?? 'Produk #' . $p->id,
        ])->values()->toArray()
    ) !!};

    function formatRupiah(num) {
        num = num.toString();
        if (num.indexOf('.') !== -1) num = num.split('.')[0];
        if (num.indexOf(',') !== -1) num = num.split(',')[0];
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(str) {
        return (str || '').toString().replace(/[^0-9]/g, '');
    }

    function recalc() {
        let total = 0;
        wrapper.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value || 0);
            const hargaRawEl = row.querySelector('.item-price');
            const harga = parseFloat(hargaRawEl?.value || 0);
            total += (qty * harga);
        });
        if (total < 0) total = 0;
        grandEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    // Generate invoice number
    document.getElementById('generate-invoice').addEventListener('click', function() {
        const now = new Date();
        const yy = String(now.getFullYear()).slice(-2);
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        let rand = '';
        for (let i = 0; i < 4; i++) rand += letters[Math.floor(Math.random() * letters.length)];
        rand += numbers[Math.floor(Math.random() * numbers.length)];
        document.getElementById('invoice_number').value = `PB-${yy}${mm}${dd}-${rand}`;
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Nomor invoice berhasil digenerate', showConfirmButton: false, timer: 2000, timerProgressBar: true });
    });

    // Add item row
    addBtn.addEventListener('click', function() {
        const tpl = document.createElement('div');
        tpl.className = 'item-row grid grid-cols-1 sm:grid-cols-4 gap-3';
        tpl.innerHTML = `
            <div>
                <label class="block text-xs text-gray-600">Produk</label>
                <select name="items[${index}][product_id]" class="tom-select item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                    <option value="" disabled selected>Pilih Produk</option>
                    ${PRODUCT_CATALOG.map(p => `<option value="${p.id}" data-price="${p.price || 0}">${p.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Qty</label>
                <input type="number" name="items[${index}][quantity]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Tgl Expired</label>
                <input type="date" name="items[${index}][tanggal_expired]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Harga Beli</label>
                <div class="mt-1 flex items-center">
                    <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                    <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" autocomplete="off" />
                    <input type="hidden" name="items[${index}][harga]" class="item-price" required />
                    <button type="button" class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700" aria-label="Hapus item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        wrapper.appendChild(tpl);
        tpl.querySelectorAll('select.tom-select').forEach(function(el) {
            if (el.tomselect) return;
            new TomSelect(el, {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                maxOptions: 100,
                onItemAdd: function() { this.control_input.readOnly = true; },
                onItemRemove: function() { this.control_input.readOnly = false; },
            });
        });
        index++;
    });

    // Auto-fill harga_beli when product selected
    wrapper.addEventListener('change', function(e) {
        if (e.target.matches('select[name^="items"][name$="[product_id]"]')) {
            const productId = e.target.value;
            const opt = e.target.options[e.target.selectedIndex];
            let price = opt?.getAttribute('data-price');
            if (!price && productId) {
                const cat = PRODUCT_CATALOG.find(p => String(p.id) === String(productId));
                if (cat) price = cat.price;
            }
            const row = e.target.closest('.item-row');
            const priceDisplay = row.querySelector('.item-price-display');
            const priceHidden = row.querySelector('.item-price');
            if (priceDisplay && priceHidden && price) {
                priceHidden.value = String(price);
                priceDisplay.value = formatRupiah(String(price));
                recalc();
            }
        }
    });

    // Rupiah formatting for price display
    wrapper.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-price-display')) {
            const row = e.target.closest('.item-row');
            const priceHidden = row.querySelector('.item-price');
            let val = unformatRupiah(e.target.value);
            if (val === '') { e.target.value = ''; priceHidden.value = ''; recalc(); return; }
            e.target.value = formatRupiah(val);
            priceHidden.value = val;
            recalc();
        }
        if (e.target.matches('input[name$="[quantity]"]')) {
            recalc();
        }
    });

    // Remove item
    wrapper.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-item');
        if (!btn) return;
        const row = btn.closest('.item-row');
        if (row) { row.remove(); recalc(); }
    });

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            html: `<div class="text-left"><ul class="list-disc list-inside space-y-1">@foreach ($errors->all() as $error)<li class="text-sm">{{ $error }}</li>@endforeach</ul></div>`,
            confirmButtonColor: '#2563eb'
        });
    @endif

    // Status Pembayaran & Riwayat Cicilan Logic
    const statusSelect = document.getElementById('status_pembayaran');
    const riwayatCicilanWrapper = document.getElementById('riwayat-cicilan-wrapper');
    const cicilanJumlahDisplay = document.getElementById('cicilan_jumlah_bayar_display');
    const cicilanJumlahHidden = document.getElementById('cicilan_jumlah_bayar');

    function toggleRiwayatCicilan() {
        if (statusSelect.value === 'unpaid') {
            riwayatCicilanWrapper.style.display = 'block';
        } else {
            riwayatCicilanWrapper.style.display = 'none';
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleRiwayatCicilan);
        toggleRiwayatCicilan();
    }

    if (cicilanJumlahDisplay) {
        cicilanJumlahDisplay.addEventListener('input', function(e) {
            let val = unformatRupiah(e.target.value);
            if (val === '') { 
                e.target.value = ''; 
                cicilanJumlahHidden.value = '';
                return; 
            }
            e.target.value = formatRupiah(val);
            cicilanJumlahHidden.value = val;
        });
    }

    // Bukti pembayaran toggle & preview
    const buktiFileInput = document.getElementById('bukti_setor');
    if (buktiFileInput) {
        buktiFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                Swal.fire({ icon: 'error', title: 'File Tidak Valid', text: 'Harap pilih file gambar (JPG, PNG, JPEG).', confirmButtonColor: '#2563eb' });
                this.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: 'Ukuran file maksimal 2MB.', confirmButtonColor: '#2563eb' });
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img-pembayaran').src = e.target.result;
                document.getElementById('preview-bukti-pembayaran').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    function toggleBuktiPembayaran() {
        let selected = null;
        document.querySelectorAll('.metode-pembayaran-radio').forEach(r => { if (r.checked) selected = r.value; });
        const bpWrapper = document.getElementById('bukti-pembayaran-wrapper');
        if (selected && selected !== 'tunai') {
            bpWrapper.style.display = '';
        } else {
            bpWrapper.style.display = 'none';
            const fi = document.getElementById('bukti_setor');
            if (fi) fi.value = '';
            const pv = document.getElementById('preview-bukti-pembayaran');
            if (pv) pv.classList.add('hidden');
        }
    }
    document.querySelectorAll('.metode-pembayaran-radio').forEach(radio => { radio.addEventListener('change', toggleBuktiPembayaran); });
    toggleBuktiPembayaran();

    // Form Submit Validation for Cicilan
    document.querySelector('form').addEventListener('submit', function(e) {
        if (statusSelect.value === 'unpaid') {
            const cicilanVal = parseFloat(cicilanJumlahHidden.value || 0);
            let total = 0;
            wrapper.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value || 0);
                const hargaRawEl = row.querySelector('.item-price');
                const harga = parseFloat(hargaRawEl?.value || 0);
                total += (qty * harga);
            });
            if (total < 0) total = 0;
            
            if (cicilanVal > total) {
                e.preventDefault();
                const kelebihan = cicilanVal - total;
                Swal.fire({
                    icon: 'error',
                    title: 'Kelebihan Pembayaran',
                    text: `Jumlah bayar (cicilan) tidak boleh melebihi total tagihan (Rp ${formatRupiah(total)}). Kelebihan Rp ${formatRupiah(kelebihan)}.`,
                    confirmButtonColor: '#2563eb'
                });
            }
        }
    });

    // Initialize TomSelect on existing product rows
    document.addEventListener('DOMContentLoaded', function() {
        wrapper.querySelectorAll('.item-product').forEach(function(el) {
            if (el.tomselect) return;
            new TomSelect(el, {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                maxOptions: 100,
                onItemAdd: function() { this.control_input.readOnly = true; },
                onItemRemove: function() { this.control_input.readOnly = false; },
            });
        });
    });

    recalc();
})();

function cancelPreviewPembayaran() {
    document.getElementById('bukti_setor').value = '';
    document.getElementById('preview-bukti-pembayaran').classList.add('hidden');
}
</script>
@endpush