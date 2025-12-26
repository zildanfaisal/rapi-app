@extends('layouts.app')

@section('title', __('Edit Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
			<div class="max-w-auto">
				<h3 class="mb-4">{{ __('Edit Surat Jalan') }}</h3>
				@if ($errors->any())
				<div class="mb-4 p-3 rounded bg-red-50 text-red-700">
					<ul class="list-disc list-inside text-sm">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<form method="POST" action="{{ route('surat-jalan.update', $suratJalan->id) }}" enctype="multipart/form-data">
					@csrf
					@method('PUT')
					<div class=" mb-4">
						<label for="invoice_id" class="block text-sm font-medium text-gray-700">{{ __('Invoice') }}</label>
						<select name="invoice_id" id="invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
							@foreach($invoices as $inv)
							<option value="{{ $inv->id }}" data-grand="{{ $inv->grand_total }}" data-customer="{{ $inv->customer_id }}" @selected(old('invoice_id', $suratJalan->invoice_id) == $inv->id)>{{ $inv->invoice_number }} — {{ $inv->customer->nama_customer ?? '-' }}</option>
							@endforeach
						</select>
						<p id="invoice-grand" class="mt-1 text-xs text-gray-600"></p>
						<input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $suratJalan->customer_id) }}">
					</div>

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="tanggal" class="block text-sm font-medium text-gray-700">{{ __('Tanggal') }}</label>
							<input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $suratJalan->tanggal) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
						</div>
						<div>
							<label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700">{{ __('Nomor Surat Jalan') }}</label>
							<input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" value="{{ old('nomor_surat_jalan', $suratJalan->nomor_surat_jalan) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" readonly>
						</div>
					</div>


					<div class="mb-4">
						<label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
						<select name="status" id="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							<option value="belum dikirim" @selected(old('status', $suratJalan->status)=='belum dikirim')>Belum Dikirim</option>
							<option value="sudah dikirim" @selected(old('status', $suratJalan->status)=='sudah dikirim')>Sudah Dikirim</option>
							<option value="cancel" @selected(old('status', $suratJalan->status)=='cancel')>Dibatalkan</option>
						</select>
					</div>

					<div class="mb-4">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }}</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel', $suratJalan->alasan_cancel) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
					</div>

					{{-- bukti --}}
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700">Bukti Pengiriman</label>

						{{-- Bukti Lama --}}
						<div class="mb-3">
							<img id="previewImage" src="{{ asset('storage/' . $suratJalan->bukti_pengiriman) }}"
								class="w-32 h-32 object-cover rounded-md border" alt="Bukti Pengiriman">
						</div>

						{{-- Input File --}}
						<input type="file" name="bukti_pengiriman" id="bukti_pengiriman" accept="image/*"
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm">

						<small class="text-gray-600">Pilih foto baru untuk mengganti foto saat ini.</small>
					</div>


					<div class="flex items-center gap-4">
						<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
						<a href="{{ route('surat-jalan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
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
		const sel = document.getElementById('invoice_id');
		const grandText = document.getElementById('invoice-grand');
		const ongkirEl = document.getElementById('ongkos_kirim');
		const ongkirDisp = document.getElementById('ongkos_kirim_display');
		const grandDisp = document.getElementById('grand_total_display');
		const grandHidden = document.getElementById('grand_total_hidden');
		const custHidden = document.getElementById('customer_id');
		const statusEl = document.getElementById('status_pembayaran');
		const alasanEl = document.getElementById('alasan_cancel');

		function formatIDR(n) {
			return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
		}

		function formatRupiahRaw(n) {
			return new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
		}

		function unformatRupiah(str) {
			return (str || '').toString().replace(/[^0-9]/g, '');
		}

		function recalc() {
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const ongkir = parseFloat(ongkirEl.value || 0);
			const total = invGrand + ongkir;
			grandDisp.textContent = formatIDR(total);
			grandHidden.value = total;
		}

		sel.addEventListener('change', function() {
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const custId = opt?.getAttribute('data-customer') || '';
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			custHidden.value = custId;
			recalc();
		});

		ongkirDisp.addEventListener('input', function(e) {
			const raw = unformatRupiah(e.target.value);
			ongkirEl.value = raw || 0;
			e.target.value = raw ? formatRupiahRaw(raw) : '';
			recalc();
		});

		function toggleAlasanRequiredEdit() {
			if (!statusEl || !alasanEl) return;
			const isCancelled = statusEl.value === 'cancel';
			if (isCancelled) alasanEl.setAttribute('required', 'required');
			else alasanEl.removeAttribute('required');
		}
		statusEl.addEventListener('change', toggleAlasanRequiredEdit);

		// init display using current selected invoice
		(function init() {
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			if (ongkirDisp && ongkirEl) {
				ongkirDisp.value = formatRupiahRaw(ongkirEl.value || '0');
			}
			recalc();
			toggleAlasanRequiredEdit();
		})();
		const fotoInput = document.getElementById('bukti_pengiriman');
		const previewImage = document.getElementById('previewImage');

		if (fotoInput && previewImage) {
			fotoInput.addEventListener('change', function(event) {
				const file = event.target.files[0];

				if (file) {
					previewImage.src = URL.createObjectURL(file);
					previewImage.classList.remove('hidden');
				} else {
					previewImage.classList.add('hidden');
					previewImage.src = "";
				}
			});
		}

	})();

	// Preview Foto Upload
</script>
@endpush