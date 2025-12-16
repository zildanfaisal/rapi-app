@extends('layouts.app')

@section('title', __('Product Batches'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Product Batches') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Product Batches</h3>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">

    <button
        @click="window.dispatchEvent(new CustomEvent('open-batch-report'))"
        class="inline-flex items-center justify-center gap-2
               px-4 py-2.5
               bg-green-600 text-white text-sm font-medium
               rounded-lg hover:bg-green-700
               w-full sm:w-auto">
        Export Laporan
    </button>

    <a href="{{ route('product-batches.create') }}"
       class="inline-flex items-center justify-center gap-2
              px-4 py-2.5
              bg-blue-600 text-white text-sm font-medium
              rounded-lg hover:bg-blue-700
              w-full sm:w-auto">
        + Tambah Batch
    </a>

</div>

                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Produk</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Kode Batch</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Harga Beli</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Masuk</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Expired</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Qty Awal</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Qty Sisa</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Supplier</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($batches as $b)
                            @php
                                $expired = \Carbon\Carbon::parse($b->tanggal_expired);
                                $now = \Carbon\Carbon::now();
                                $diffMonths = $now->diffInMonths($expired, false);
                            @endphp
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-left">{{ $b->product->nama_produk }}</td>
                                <td class="border px-3 py-2 font-semibold text-left">{{ $b->batch_number }}</td>
                                <td class="border px-3 py-2 text-right">
                                    Rp {{ number_format($b->harga_beli,0,',','.') }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($b->tanggal_masuk)->translatedFormat('F') }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($b->tanggal_expired)->translatedFormat('F') }}
                                </td>
                                <td class="border px-3 py-2">{{ $b->quantity_masuk }}</td>
                                <td class="border px-3 py-2">{{ $b->quantity_sekarang }}</td>
                                <td class="border px-3 py-2">{{ $b->supplier }}</td>
                                <td class="border px-3 py-2">
                                    @if ($expired->isSameMonth($now) || $diffMonths < 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">{{ $b->status }}</span>
                                    @elseif ($diffMonths <= 2)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">{{ $b->status }}</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">{{ $b->status }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('product-batches.edit', $b->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('product-batches.destroy', $b->id) }}" method="POST" class="inline" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline ms-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden w-full" id="mobileCardWrapper">

                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="mx-1 border-gray-300 rounded text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                            entries
                        </div>
                    </div>

                    <div id="mobileCards" class="space-y-3">
                        @foreach ($batches as $b)
                        <div class="mobile-card border rounded-lg shadow-sm bg-white">

                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="text-xs text-gray-500">Batch</div>
                                <h4 class="font-semibold">{{ $b->batch_number }}</h4>
                                <div class="text-sm text-gray-600">{{ $b->product->nama_produk }}</div>
                            </div>

                            <div class="px-4 py-3 text-sm space-y-1">
                                <div>Harga Beli: <b>Rp {{ number_format($b->harga_beli,0,',','.') }}</b></div>
                                <div>Masuk: {{ \Carbon\Carbon::parse($b->tanggal_masuk)->translatedFormat('F') }}</div>
                                <div>Expired: {{ \Carbon\Carbon::parse($b->tanggal_expired)->translatedFormat('F') }}</div>
                                <div>Qty Awal: {{ $b->quantity_masuk }}</div>
                                <div>Qty Sisa: {{ $b->quantity_sekarang }}</div>
                                <div>Supplier: {{ $b->supplier }}</div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">

                                <a href="{{ route('product-batches.edit', $b) }}"
                                   class="flex-1 text-center
                                          px-3 py-2
                                          border border-blue-600
                                          text-blue-600
                                          rounded-md text-sm
                                          hover:bg-blue-50">
                                    Edit
                                </a>

                                <form action="{{ route('product-batches.destroy', $b) }}"
                                      method="POST"
                                      class="flex-1"
                                      data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-3 py-2
                                                   border border-red-600
                                                   text-red-600
                                                   rounded-md text-sm
                                                   hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let dataTableInstance = null;
    const cards = [...document.querySelectorAll('.mobile-card')];
    const pagination = document.getElementById('mobilePagination');
    const info = document.getElementById('mobileInfo');
    const perPageSelect = document.getElementById('mobilePerPage');

    let perPage = parseInt(perPageSelect.value);
    let currentPage = 1;

    function renderMobile() {
        const total = cards.length;
        const totalPages = Math.ceil(total / perPage);
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        cards.forEach((c, i) => c.style.display = i >= start && i < end ? 'block' : 'none');
        info.textContent = `Showing ${start+1} to ${Math.min(end,total)} of ${total} entries`;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        pagination.innerHTML = '';
        const max = 5;
        let s = Math.max(1, currentPage - 2);
        let e = Math.min(totalPages, s + max - 1);

        const btn = (t,d,a,cb) => {
            const b = document.createElement('button');
            b.textContent = t;
            b.disabled = d;
            b.className = `px-3 py-1 text-sm border rounded
                ${a?'bg-blue-600 text-white':'bg-white'}
                ${d?'opacity-50':''}`;
            b.onclick = cb;
            return b;
        };

        pagination.appendChild(btn('Prev', currentPage===1,false,()=>{currentPage--;renderMobile();}));
        for(let i=s;i<=e;i++){
            pagination.appendChild(btn(i,false,i===currentPage,()=>{currentPage=i;renderMobile();}));
        }
        pagination.appendChild(btn('Next', currentPage===totalPages,false,()=>{currentPage++;renderMobile();}));
    }

    perPageSelect.onchange = () => { perPage=parseInt(perPageSelect.value); currentPage=1; renderMobile(); };

    function handleResponsive() {
        if (window.innerWidth >= 1024) {
            if (!dataTableInstance) {
                dataTableInstance = new DataTable('#dataTables', { responsive:true });
            }
        } else {
            if (dataTableInstance) {
                dataTableInstance.destroy();
                dataTableInstance = null;
            }
            renderMobile();
        }
    }

    handleResponsive();
    window.addEventListener('resize', handleResponsive);
});
</script>
@endpush
