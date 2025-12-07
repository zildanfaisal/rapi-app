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
                    <h3 class="mb-4">{{ __('Product Batches') }}</h3>
                    <a href="{{ route('product-batches.create') }}" 
                       class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Tambah Batch
                    </a>
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
                            <th class="px-4 py-2 border">Qty Awal</th>
                            <th class="px-4 py-2 border">Qty Sekarang</th>
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
                            <td colspan="11" class="px-4 py-2 border">Belum Ada Batch.</td>
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
