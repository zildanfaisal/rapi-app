@extends('layouts.app')

@section('title', __('Buat Surat Jalan'))

@section('header')
	<h2 class="text-xl font-semibold text-gray-800">{{ __('Buat Surat Jalan') }}</h2>
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

				<form method="POST" action="{{ route('surat-jalan.store') }}">
					@csrf
					<div class="mb-4">
						<label for="invoice_id" class="block text-sm font-medium text-gray-700">{{ __('Invoice') }}</label>
						<select name="invoice_id" id="invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
							<option value="" disabled selected>{{ __('Pilih Invoice') }}</option>
							@foreach($invoices as $inv)
								<option value="{{ $inv->id }}" data-grand="{{ $inv->grand_total }}" data-customer="{{ $inv->customer_id }}">{{ $inv->invoice_number }} — {{ $inv->customer->nama_customer ?? '-' }}</option>
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

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
						<div>
							<label for="ongkos_kirim" class="block text-sm font-medium text-gray-700">{{ __('Ongkos Kirim') }}</label>
							<div class="mt-1 flex items-center">
								<span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
								<input type="number" step="0.01" name="ongkos_kirim" id="ongkos_kirim" value="{{ old('ongkos_kirim', 0) }}" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							</div>
						</div>
						<div class="sm:col-span-2">
							<label class="block text-sm font-medium text-gray-700">{{ __('Grand Total') }}</label>
							<div class="mt-1 text-lg font-semibold" id="grand_total_display">Rp 0</div>
							<input type="hidden" name="grand_total" id="grand_total_hidden" value="0">
						</div>
					</div>

					<div class="mb-4">
						<label for="status_pembayaran" class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
						<select name="status_pembayaran" id="status_pembayaran" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							<option value="pending">Belum Lunas</option>
							<option value="lunas">Lunas</option>
							<option value="cancel">Dibatalkan</option>
						</select>
					</div>

					<div class="mb-4">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }}</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
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
	(function(){
		const sel = document.getElementById('invoice_id');
		const grandText = document.getElementById('invoice-grand');
		const ongkirEl = document.getElementById('ongkos_kirim');
		const grandDisp = document.getElementById('grand_total_display');
		const grandHidden = document.getElementById('grand_total_hidden');
		const custHidden = document.getElementById('customer_id');
		const genBtn = document.getElementById('gen-no');
		const noInput = document.getElementById('nomor_surat_jalan');

		function formatIDR(n){ return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0)); }

		function recalc(){
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const ongkir = parseFloat(ongkirEl.value || 0);
			const total = invGrand + ongkir;
			grandDisp.textContent = formatIDR(total);
			grandHidden.value = total;
		}

		sel.addEventListener('change', function(){
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const custId = opt?.getAttribute('data-customer') || '';
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			custHidden.value = custId;
			recalc();
		});

		ongkirEl.addEventListener('input', recalc);

		genBtn.addEventListener('click', function(){
			const rand = Math.random().toString(36).substring(2, 10).toUpperCase();
			noInput.value = rand;
		});

		// Require alasan_cancel when status is cancel
		const statusEl = document.getElementById('status_pembayaran');
		const alasanEl = document.getElementById('alasan_cancel');
		function toggleAlasanRequiredCreate(){
			if (!statusEl || !alasanEl) return;
			const isCancelled = statusEl.value === 'cancel';
			if (isCancelled) alasanEl.setAttribute('required','required');
			else alasanEl.removeAttribute('required');
		}
		statusEl.addEventListener('change', toggleAlasanRequiredCreate);

		// init on load
		recalc();
		toggleAlasanRequiredCreate();
	})();
</script>
@endpush
