@extends('layouts.app')

@section('title', __('Buat Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Buat Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
			<div class="max-w-auto">
				<h3 class="mb-4">{{ __('Buat Surat Jalan') }}</h3>
				@if ($errors->any())
				<div class="mb-4 p-3 rounded bg-red-50 text-red-700">
					<ul class="list-disc list-inside text-sm">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<form method="POST" action="{{ route('surat-jalan.store') }}" enctype="multipart/form-data">
					@csrf
					<div class="mb-4">
						<label for="invoice_id" class="block text-sm font-medium text-gray-700">{{ __('Invoice') }}</label>
						<select name="invoice_id" id="invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
							<option value="" disabled selected>{{ __('Pilih Invoice') }}</option>
							@foreach($invoices as $inv)
							<option value="{{ $inv->id }}" data-grand="{{ $inv->grand_total }}" data-customer="{{ $inv->customer_id }}" data-date="{{ $inv->tanggal_invoice }}">{{ $inv->invoice_number }} — {{ $inv->customer->nama_customer ?? '-' }}</option>
							@endforeach
						</select>
						<p id="invoice-grand" class="mt-1 text-xs text-gray-600"></p>
						<input type="hidden" name="customer_id" id="customer_id">
					</div>

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="tanggal" class="block text-sm font-medium text-gray-700">{{ __('Tanggal') }}</label>
							<input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
						</div>
						<div>
							<label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700">{{ __('Nomor Surat Jalan') }}</label>
							<div class="mt-1 flex gap-2">
								<input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" value="{{ old('nomor_surat_jalan') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
								<button type="button" id="gen-no" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">{{ __('Generate') }}</button>
							</div>
						</div>
					</div>



					<input type="hidden" id="ongkos_kirim_display" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />


					<div class="mb-4">
						<label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
						<select name="status" id="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							<option value="belum dikirim">Belum Dikirim</option>
							<option value="sudah dikirim">Sudah Dikirim</option>
							<option value="cancel">Dibatalkan</option>
						</select>
					</div>
					{{-- Foto Produk --}}
					<div class="mb-4">

						{{-- Preview Foto --}}
						<div class="mt-3">
							<img id="previewImage"
								src=""
								class="hidden w-32 h-32 object-cover rounded-md border" />
						</div>

						<label for="bukti_pengiriman" class="block text-sm font-medium text-gray-700">
							{{ __('Bukti Pengiriman') }}
						</label>

						<input type="file" name="bukti_pengiriman" id="bukti_pengiriman" accept="image/*"
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
							required>

					</div>

					<div class="mb-4">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }}</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
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
		const custHidden = document.getElementById('customer_id');
		const genBtn = document.getElementById('gen-no');
		const noInput = document.getElementById('nomor_surat_jalan');
		const tanggalInput = document.getElementById('tanggal');
		const statusEl = document.getElementById('status_pembayaran');
		const alasanEl = document.getElementById('alasan_cancel');

		function formatIDR(n) {
			return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
		}

		// Invoice change handler
		sel.addEventListener('change', function() {
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const custId = opt?.getAttribute('data-customer') || '';
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			custHidden.value = custId;

			// Set min date for SJ based on invoice date
			const invDate = opt?.getAttribute('data-date') || '';
			if (tanggalInput && invDate) {
				tanggalInput.min = invDate;
				if (tanggalInput.value && tanggalInput.value < invDate) {
					tanggalInput.value = invDate;
				}
			}
		});

		// Generate nomor surat jalan
		genBtn.addEventListener('click', function() {
			const now = new Date();
			const yy = String(now.getFullYear()).slice(-2);
			const mm = String(now.getMonth() + 1).padStart(2, '0');
			const dd = String(now.getDate()).padStart(2, '0');
			const datePart = yy + mm + dd;
			const randomPart = Math.random()
				.toString(36)
				.substring(2, 7)
				.toUpperCase();

			noInput.value = `SJ-${datePart}-${randomPart}`;
		});

		// Require alasan_cancel when status is cancel
		function toggleAlasanRequired() {
			if (!statusEl || !alasanEl) return;
			const isCancelled = statusEl.value === 'cancel';
			if (isCancelled) {
				alasanEl.setAttribute('required', 'required');
			} else {
				alasanEl.removeAttribute('required');
			}
		}

		if (statusEl) {
			statusEl.addEventListener('change', toggleAlasanRequired);
		}

		// Initialize min date if invoice preselected
		if (tanggalInput && sel && sel.selectedIndex > 0) {
			const opt = sel.options[sel.selectedIndex];
			const invDate = opt?.getAttribute('data-date') || '';
			if (invDate) {
				tanggalInput.min = invDate;
				if (tanggalInput.value && tanggalInput.value < invDate) {
					tanggalInput.value = invDate;
				}
			}
		}

		// Preview Foto Upload
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

		// Init on load
		toggleAlasanRequired();
	})();
</script>
@endpush