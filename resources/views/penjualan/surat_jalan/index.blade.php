@extends('layouts.app')

@section('title', __('Surat Jalan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="text-sm text-green-700 mb-1">Rasio Lunas / Transaksi</div>
                <div class="text-4xl font-extrabold text-green-800 tracking-tight">
                    {{ number_format($paidCount ?? 0, 0, ',', '.') }}
                    <span class="text-green-600">/</span>
                    {{ number_format($totalCount ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-green-700 space-y-0.5">
                    <div>Lunas: <span class="font-semibold">{{ number_format($paidCount ?? 0, 0, ',', '.') }}</span> transaksi</div>
                    <div>Total transaksi: <span class="font-semibold">{{ number_format($totalCount ?? 0, 0, ',', '.') }}</span></div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="text-[11px] text-green-600">Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}</div>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('surat-jalan.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        <a href="{{ route('surat-jalan.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Surat Jalan') }}</h3>
                    <a href="{{ route('surat-jalan.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Buat Surat Jalan
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTables">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nomor Surat Jalan</th>
                                <th class="px-4 py-2 border">Pelanggan</th>
                                <th class="px-4 py-2 border">Invoice</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Ongkir</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status Pembayaran</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratJalans as $sj)
                                <tr class="text-center hover:bg-gray-50 cursor-pointer" data-href="{{ route('surat-jalan.show', $sj) }}">
                                    <td class="px-4 py-2 border">{{ $loop->iteration + ($suratJalans->currentPage() - 1) * $suratJalans->perPage() }}</td>
                                    <td class="px-4 py-2 border">{{ $sj->nomor_surat_jalan ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $sj->customer->nama_customer ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $sj->invoice->invoice_number ?? $sj->invoice_id }}</td>
                                    <td class="px-4 py-2 border">{{ $sj->tanggal }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($sj->ongkos_kirim ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($sj->grand_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $sj->status_pembayaran; @endphp
                                        @if ($status === 'lunas')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                                        @elseif ($status === 'pending')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                                        @elseif ($status === 'cancel')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                                        @else
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('surat-jalan.edit', $sj->id) }}" class="text-blue-600 hover:underline" onclick="event.stopPropagation()">Edit</a>
                                        <form action="{{ route('surat-jalan.destroy', $sj->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat jalan ini?')" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
                                        </form>
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
                                    <td class="px-4 py-2 border"></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $suratJalans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#dataTables');
    document.querySelector('#dataTables tbody')?.addEventListener('click', function(e){
        const tr = e.target.closest('tr[data-href]');
        if (!tr) return;
        const href = tr.getAttribute('data-href');
        if (!href) return;
        window.location.href = href;
    });
</script>
@endpush
