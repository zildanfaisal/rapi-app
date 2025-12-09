@extends('layouts.app')

@section('title', __('Product Batches'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Product Batches') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                
                <div class="flex items-center justify-between mb-4">
                 <h3 class="text-xl font-semibold">{{ __('Product Batches') }}</h3>
                    <div class="flex gap-3">
                        <!-- Tombol Laporan Batch -->
                        <button 
                            @click="window.dispatchEvent(new CustomEvent('open-batch-report'))"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Export Laporan Batch
                        </button>

                        <!-- Tombol Tambah Batch -->
                        <a href="{{ route('product-batches.create') }}" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            + Tambah Batch
                        </a>
                    </div>
                </div>


                <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300" id="dataTables">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Produk</th>
                            <th class="px-4 py-2 border">Kode Batch</th>
                            <th class="px-4 py-2 border">Tanggal Masuk</th>
                            <th class="px-4 py-2 border">Tanggal Expired</th>
                            <th class="px-4 py-2 border">Kuantitas Awal</th>
                            <th class="px-4 py-2 border">Kuantitas Sekarang</th>
                            <th class="px-4 py-2 border">Supplier</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($batches as $b)
                        <tr class="text-center hover:bg-gray-50">

                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>

                            <td class="px-4 py-2 border">{{ $b->product->nama_produk }}</td>

                            <td class="px-4 py-2 border font-semibold">
                                {{ $b->batch_number }}
                            </td>

                           <td class="px-4 py-2 border">
                                {{ \Carbon\Carbon::parse($b->tanggal_masuk)->translatedFormat('F') }}
                            </td>

                            <td class="px-4 py-2 border">
                                {{ \Carbon\Carbon::parse($b->tanggal_expired)->translatedFormat('F') }}
                            </td>
                            <td class="px-4 py-2 border">{{ $b->quantity_masuk }}</td>

                            <td class="px-4 py-2 border">{{ $b->quantity_sekarang }}</td>

                            <td class="px-4 py-2 border">{{ $b->supplier }}</td>

                            @php
                                $expired = \Carbon\Carbon::parse($b->tanggal_expired);
                                $now = \Carbon\Carbon::now();
                                $diffMonths = $now->diffInMonths($expired, false);
                            @endphp


                            <td class="px-4 py-2 border">
                                @if ($expired->isSameMonth($now)) 
                                    {{-- Merah: expired bulan ini --}}
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">{{ $b->status }}</span>

                                @elseif ($diffMonths >= 1 && $diffMonths <= 2)
                                    {{-- Kuning: expired H-1 bulan --}}
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">{{ $b->status }}</span>

                                @elseif ($diffMonths > 2)
                                    {{-- Hijau: expired > bulan sekarang --}}
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">{{ $b->status }}</span>

                                @else
                                    {{-- Sudah lewat (expired) → merah --}}
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">{{ $b->status }}</span>
                                @endif
                            </td>


                            <td class="px-4 py-2 border">
                                <a href="{{ route('product-batches.edit', $b->id) }}" 
                                   class="text-blue-600 hover:underline">Edit</a>

                                <form action="{{ route('product-batches.destroy', $b->id) }}" 
                                      method="POST" style="display:inline;" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ms-4">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="text-center">
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border">Belum Ada Product Batch.</td>
                                    <td class="px-4 py-2 border"></td>
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

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#dataTables');
</script>
@endpush

{{-- MODAL LAPORAN --}}
<div 
    x-data="{ showBatchModal: false }"
    x-on:open-batch-report.window="showBatchModal = true"
    x-show="showBatchModal"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    style="display: none;">

    <div 
        x-show="showBatchModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showBatchModal = false"
        class="absolute inset-0 bg-black/50 backdrop-blur-sm">
    </div>

    <div 
        x-show="showBatchModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-90 translate-y-4"
        class="relative bg-white rounded-xl w-full max-w-md p-6 shadow-2xl z-10 mx-4">

        <h2 class="text-lg font-bold text-slate-800 mb-4">Filter Laporan Batch Produk</h2>

        <form action="{{ route('product-batches.report') }}" method="GET" target="_blank">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Filter</label>
                <select name="filter" class="w-full border-slate-300 rounded-lg p-2.5" required>
                    <option value="all">Semua</option>
                    <option value="tanggal_masuk">Tanggal Masuk</option>
                    <option value="tanggal_expired">Tanggal Expired</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="month" name="start_date" class="w-full border-slate-300 rounded-lg p-2.5">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="month" name="end_date" class="w-full border-slate-300 rounded-lg p-2.5">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="showBatchModal = false"
                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">
                    Batal
                </button>

                <button type="reset"
                    class="px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg hover:bg-yellow-100">
                    Reset
                </button>

                <button type="submit" @click="showBatchModal = false"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Generate PDF
                </button>
            </div>
        </form>

    </div>
</div>
