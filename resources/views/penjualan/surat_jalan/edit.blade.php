@extends('layouts.app')

@section('title', __('Edit Surat Jalan'))

@section('header')
	<h2 class="text-xl font-semibold text-gray-800">{{ __('Edit Surat Jalan') }}</h2>
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

				<form method="POST" action="{{ route('surat-jalan.update', $suratJalan->id) }}">
					@csrf
					@method('PUT')
					<div class="mb-4">
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

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
						<div>
							<label for="ongkos_kirim" class="block text-sm font-medium text-gray-700">{{ __('Ongkos Kirim') }}</label>
							<div class="mt-1 flex items-center">
								<span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
								<input type="number" step="0.01" name="ongkos_kirim" id="ongkos_kirim" value="{{ old('ongkos_kirim', $suratJalan->ongkos_kirim) }}" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							</div>
						</div>
						<div class="sm:col-span-2">
							<label class="block text-sm font-medium text-gray-700">{{ __('Grand Total') }}</label>
							<div class="mt-1 text-lg font-semibold" id="grand_total_display">Rp {{ number_format(old('grand_total', $suratJalan->grand_total), 0, ',', '.') }}</div>
							<input type="hidden" name="grand_total" id="grand_total_hidden" value="{{ old('grand_total', $suratJalan->grand_total) }}">
						</div>
					</div>

					<div class="mb-4">
						<label for="status_pembayaran" class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
						<select name="status_pembayaran" id="status_pembayaran" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							<option value="pending" @selected(old('status_pembayaran', $suratJalan->status_pembayaran)=='pending')>Belum Lunas</option>
							<option value="lunas" @selected(old('status_pembayaran', $suratJalan->status_pembayaran)=='lunas')>Lunas</option>
							<option value="cancel" @selected(old('status_pembayaran', $suratJalan->status_pembayaran)=='cancel')>Dibatalkan</option>
						</select>
					</div>

					<div class="mb-4">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }}</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel', $suratJalan->alasan_cancel) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
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
	(function(){
		const sel = document.getElementById('invoice_id');
		const grandText = document.getElementById('invoice-grand');
		const ongkirEl = document.getElementById('ongkos_kirim');
		const grandDisp = document.getElementById('grand_total_display');
		const grandHidden = document.getElementById('grand_total_hidden');
		const custHidden = document.getElementById('customer_id');
		const statusEl = document.getElementById('status_pembayaran');
		const alasanEl = document.getElementById('alasan_cancel');

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

		function toggleAlasanRequiredEdit(){
			if (!statusEl || !alasanEl) return;
			const isCancelled = statusEl.value === 'cancel';
			if (isCancelled) alasanEl.setAttribute('required','required');
			else alasanEl.removeAttribute('required');
		}
		statusEl.addEventListener('change', toggleAlasanRequiredEdit);

		// init display using current selected invoice
		(function init(){
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			recalc();
			toggleAlasanRequiredEdit();
		})();
	})();
</script>
@endpush
