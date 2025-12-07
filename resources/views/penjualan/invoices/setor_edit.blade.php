@extends('layouts.app')

@section('title', __('Setor Penjualan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Setor Penjualan') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Setor Penjualan') }}</h3>

                    @if (session('success'))
                        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 border rounded p-4 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600">{{ __('Nomor Invoice') }}</div>
                                <div class="font-semibold">{{ $invoice->invoice_number }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Pelanggan') }}</div>
                                <div class="font-semibold">{{ $invoice->customer->nama_customer ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Tanggal Invoice') }}</div>
                                <div class="font-semibold">{{ $invoice->tanggal_invoice }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Grand Total') }}</div>
                                <div class="font-semibold">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('invoices.setor.update', $invoice) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="status_setor" class="block text-sm font-medium text-gray-700">{{ __('Status Setor') }}</label>
                                <select name="status_setor" id="status_setor" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    <option value="belum" @selected(old('status_setor', $invoice->status_setor) == 'belum')>Belum</option>
                                    <option value="sudah" @selected(old('status_setor', $invoice->status_setor) == 'sudah')>Sudah</option>
                                </select>
                            </div>
                            <div>
                                <label for="bukti_setor" class="block text-sm font-medium text-gray-700">{{ __('Bukti Setor') }}</label>
                                <input type="file" name="bukti_setor" id="bukti_setor" accept="image/*" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                @if($invoice->bukti_setor)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$invoice->bukti_setor) }}" alt="Bukti Setor" class="h-24 rounded border">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Setor') }}</button>
                            <a href="{{ route('invoices.index', $invoice) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Kembali') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
