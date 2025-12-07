@extends('layouts.app')

@section('title', __('Input Keuangan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Input Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Row 1: Target Bulanan & Filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Target Bulanan Card -->
            @if($budgetTarget)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="text-sm text-purple-600 mb-1">Target Bulanan</div>
                <div class="text-3xl font-bold text-purple-700">Rp {{ number_format($budgetTarget->budget_bulanan, 0, ',', '.') }}</div>
                <div class="text-xs text-purple-500 mt-2">Periode: {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}</div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Target Bulanan</div>
                <div class="text-xl text-gray-500">Belum ada target untuk periode ini</div>
                <a href="{{ route('budget-targets.create') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">+ Tambah Target</a>
            </div>
            @endif

            <!-- Filter Periode Card -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('finance-records.index') }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Periode</label>
                    <select name="periode" class="w-full px-3 py-2.5 border rounded-lg" onchange="this.form.submit()">
                        <option value="{{ date('Y-m') }}" {{ $periode == date('Y-m') ? 'selected' : '' }}>Bulan Ini ({{ date('F Y') }})</option>
                        @foreach($availablePeriods as $p)
                            <option value="{{ $p }}" {{ $periode == $p ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($p . '-01')->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Row 2: Summary Cards (Pengeluaran, Saldo Sisa, Pemasukan) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;
            @endphp

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600 mb-1">Total Pengeluaran</div>
                <div class="text-2xl font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 mb-1">Saldo Sisa</div>
                <div class="text-2xl font-bold {{ $saldoSisa >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                    Rp {{ number_format($saldoSisa, 0, ',', '.') }}
                </div>
                @if($budgetTarget)
                <div class="text-xs text-gray-500 mt-1">
                    {{ $saldoSisa >= 0 ? 'Tersisa' : 'Overbudget' }}
                    {{ number_format(abs(($saldoSisa / $budgetTarget->budget_bulanan) * 100), 1) }}%
                </div>
                @endif
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600 mb-1">Total Pemasukan</div>
                <div class="text-2xl font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Input Keuangan') }} - {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}</h3>
                    <a href="{{ route('finance-records.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Tambah Data Keuangan
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTables">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Tipe</th>
                                <th class="px-4 py-2 border">Kategori</th>
                                <th class="px-4 py-2 border">Jumlah</th>
                                <th class="px-4 py-2 border">Deskripsi</th>
                                <th class="px-4 py-2 border">Dibuat Oleh</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($financeRecords as $fr)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 border">
                                        @if($fr->tipe === 'income')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Pemasukan</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">{{ $fr->kategori }}</td>
                                    <td class="px-4 py-2 border text-right">
                                        <span class="{{ $fr->tipe === 'income' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                            Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border text-left">{{ $fr->deskripsi ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $fr->user->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('finance-records.edit', $fr->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('finance-records.destroy', $fr->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td class="px-4 py-2 border" colspan="8">Belum Ada Data Keuangan untuk periode ini.</td>
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

@push('scripts')
<script>
    new DataTable('#dataTables');

    // SweetAlert2 for success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif
</script>
@endpush
