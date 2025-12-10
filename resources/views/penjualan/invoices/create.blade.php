@extends('layouts.app')

@section('title', __('Tambah Penjualan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Tambah Penjualan') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Tambah Penjualan') }}</h3>
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="mb-4">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">{{ __('Pelanggan') }}</label>
                            <select name="customer_id" id="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                <option value="" disabled selected>{{ __('Pilih Pelanggan') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->nama_customer }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_invoice" class="block text-sm font-medium text-gray-700">{{ __('Tanggal Invoice') }}</label>
                                <input type="date" name="tanggal_invoice" id="tanggal_invoice" value="{{ old('tanggal_invoice') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700">{{ __('Jatuh Tempo') }}</label>
                                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700">{{ __('Nomor Invoice') }}</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" readonly placeholder="Klik Generate">
                                <button type="button" id="generate-invoice" class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Generate') }}</button>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold mb-2">{{ __('Produk') }}</h4>
                            <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label for="scan-barcode" class="block text-xs text-gray-600">Barcode Produk</label>
                                    <input type="text" id="scan-barcode" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Masukkan Barcode" autocomplete="off">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="scan-clear" class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Bersihkan</button>
                                </div>
                            </div>
                            <div id="items-wrapper" class="space-y-3">
                                <div class="item-row grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Produk') }}</label>
                                        <select name="items[0][product_id]" class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                            <option value="" disabled selected>{{ __('Pilih Produk') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}" data-price="{{ $product->harga ?? $product->price ?? 0 }}">{{ $product->nama_produk ?? $product->nama ?? 'Produk #'.$product->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                                        <select name="items[0][batch_id]" class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                            <option value="" disabled selected>{{ __('Pilih Batch') }}</option>
                                            @foreach($batches as $batch)
                                                <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}">{{ $batch->batch_number }} — {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->translatedFormat('F') }} — Stok: {{ $batch->quantity_sekarang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Qty') }}</label>
                                        <input type="number" name="items[0][quantity]" min="1" value="1" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Harga') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />
                                            <input type="hidden" name="items[0][harga]" class="item-price" required />
                                            <button type="button" class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700" aria-label="Hapus item">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-item" class="mt-3 px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">{{ __('Tambah Produk') }}</button>
                        </div>
                        <div class="mb-4">
                            <label for="status_pembayaran" class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="unpaid" @selected(old('status_pembayaran','unpaid')=='unpaid')>Belum Lunas</option>
                                <option value="paid" @selected(old('status_pembayaran')=='paid')>Lunas</option>
                                <option value="overdue" @selected(old('status_pembayaran')=='overdue')>Terlambat</option>
                                <option value="cancelled" @selected(old('status_pembayaran')=='cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <div class="text-right">
                                <span class="text-sm text-gray-600">{{ __('Grand Total') }}</span>
                                <span id="grand-total" class="text-lg font-semibold">0</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
                            <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function() {
        let index = 1;
        const wrapper = document.getElementById('items-wrapper');
        const addBtn = document.getElementById('add-item');
        const grandEl = document.getElementById('grand-total');
        const scanInput = document.getElementById('scan-barcode');
        const scanClear = document.getElementById('scan-clear');

        const PRODUCT_CATALOG = {!! json_encode(
            $products->map(function($p){
                return [
                    'id' => $p->id,
                    'barcode' => $p->barcode,
                    'price' => $p->harga ?? $p->price ?? 0,
                ];
            })->values()->toArray()
        ) !!};


        function formatRupiah(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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
            grandEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        function selectProductInRow(row, productId) {
            const sel = row.querySelector('.item-product');
            if (!sel) return;
            sel.value = String(productId);
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            if (qtyInput && (!qtyInput.value || qtyInput.value === '0')) qtyInput.value = 1;
        }

        function getEmptyRowOrAdd() {
            let row = Array.from(wrapper.querySelectorAll('.item-row')).find(r => {
                const sel = r.querySelector('.item-product');
                return sel && !sel.value;
            });
            if (!row) {
                addBtn.click();
                const rows = wrapper.querySelectorAll('.item-row');
                row = rows[rows.length - 1];
            }
            return row;
        }

        function handleScan(code) {
            const bc = String(code || '').trim();
            if (!bc) return;
            const found = PRODUCT_CATALOG.find(p => (p.barcode || '') === bc);
            if (!found) {
                alert('Produk dengan barcode tersebut tidak ditemukan');
                return;
            }
            const row = getEmptyRowOrAdd();
            selectProductInRow(row, found.id);
            recalc();
        }

        addBtn.addEventListener('click', function() {
            const tpl = document.createElement('div');
            tpl.className = 'item-row grid grid-cols-1 sm:grid-cols-4 gap-3';
            tpl.innerHTML = `
                <div>
                    <label class="block text-xs text-gray-600">{{ __('Produk') }}</label>
                    <select name="items[${index}][product_id]" class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>{{ __('Pilih Produk') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}" data-price="{{ $product->harga ?? $product->price ?? 0 }}">{{ $product->nama_produk ?? $product->nama ?? 'Produk #'.$product->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                    <select name="items[${index}][batch_id]" class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>{{ __('Pilih Batch') }}</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}">{{ $batch->batch_number }} — Stok: {{ $batch->quantity_sekarang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Qty</label>
                    <input type="number" name="items[${index}][quantity]" min="1" value="1" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Harga</label>
                    <div class="mt-1 flex items-center">
                        <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                        <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />
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
            index++;
        });

        // Generate invoice number on button click
        document.getElementById('generate-invoice').addEventListener('click', function() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let out = '';
            for (let i = 0; i < 8; i++) out += chars[Math.floor(Math.random()*chars.length)];
            document.getElementById('invoice_number').value = out;
        });

        // Auto-fill harga when product selected
        wrapper.addEventListener('change', function(e){
            if (e.target.matches('select[name^="items"][name$="[product_id]"]')) {
                const priceAttr = e.target.options[e.target.selectedIndex]?.getAttribute('data-price');
                const row = e.target.closest('.item-row');
                const priceDisplay = row.querySelector('.item-price-display');
                const priceHidden = row.querySelector('.item-price');
                if (priceDisplay && priceHidden && priceAttr) {
                    priceHidden.value = String(priceAttr);
                    priceDisplay.value = formatRupiah(String(priceAttr));
                    recalc();
                }
                // Filter batch options matching selected product
                const productId = e.target.value;
                const batchSelect = row.querySelector('.item-batch');
                if (batchSelect) {
                    Array.from(batchSelect.options).forEach(opt => {
                        if (!opt.value) return; // skip placeholder
                        const p = opt.getAttribute('data-product');
                        opt.hidden = (p !== productId);
                    });
                    // Reset selection
                    batchSelect.value = '';
                }
            }
        });

        // Recalculate on qty or harga change
        wrapper.addEventListener('input', function(e){
            if (e.target.matches('input[name$="[quantity]"]')) {
                recalc();
                return;
            }
            if (e.target.classList.contains('item-price-display')) {
                const row = e.target.closest('.item-row');
                const hidden = row.querySelector('.item-price');
                const raw = unformatRupiah(e.target.value);
                hidden.value = raw;
                e.target.value = raw ? formatRupiah(raw) : '';
                recalc();
            }
        });

        // Remove item row on click "x / Hapus"
        wrapper.addEventListener('click', function(e){
            const btn = e.target.closest('.remove-item');
            if (!btn) return;
            const row = btn.closest('.item-row');
            if (row) {
                row.remove();
                recalc();
            }
        });

        // Scan handler: auto-handle without pressing Enter
        if (scanInput) {
            let scanTimer = null;
            const triggerScan = () => {
                const val = (scanInput.value || '').trim();
                if (!val) return;
                handleScan(val);
                scanInput.value = '';
            };
            scanInput.addEventListener('input', function(){
                if (scanTimer) clearTimeout(scanTimer);
                // small debounce to let scanner finish
                scanTimer = setTimeout(triggerScan, 150);
            });
            // also trigger on change for scanners that fire change
            scanInput.addEventListener('change', triggerScan);
        }
        if (scanClear) {
            scanClear.addEventListener('click', function(){ scanInput && (scanInput.value = ''); scanInput && scanInput.focus(); });
        }

        // Also recalc on initial load (in case defaults present)
        recalc();
    })();
</script>
@endpush