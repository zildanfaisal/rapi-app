@extends('layouts.app')

@section('title', __('Setor Penjualan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Setor Penjualan') }}</h2>
@endsection
@push('scripts')
<script>
    new DataTable('#dataTablesSetor');
</script>
@endpush

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Daftar Invoice') }}</h3>
                </div>

                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
                @endif

                <div class="max-w-auto">
                    <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTablesSetor">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nomor Invoice</th>
                                <th class="px-4 py-2 border">Pelanggan</th>
                                <th class="px-4 py-2 border">Tanggal Invoice</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status Setor</th>
                                <th class="px-4 py-2 border">Bukti Setor</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->invoice_number }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->customer->nama_customer ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->tanggal_invoice }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $isSetor = ($inv->status_setor ?? 'belum') === 'sudah'; @endphp
                                        <span class="inline-block px-2 py-1 rounded text-xs {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $isSetor ? 'Sudah' : 'Belum' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border">
                                        @if ($inv->bukti_setor)
                                            <a href="{{ asset('storage/' . $inv->bukti_setor) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('invoices.setor.edit', $inv) }}" class="text-blue-600 hover:underline">
                                            {{ ($inv->status_setor ?? 'belum') === 'sudah' ? 'Edit Setor' : 'Setor' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border">Belum Ada Data.</td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
