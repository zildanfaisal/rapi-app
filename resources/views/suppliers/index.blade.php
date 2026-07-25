@extends('layouts.app')

@section('title', __('Supplier'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Supplier') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Data Supplier</h3>

                    @can('suppliers.create')
                    <a href="{{ route('suppliers.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Tambah Supplier
                    </a>
                    @endcan
                </div>

                <div class="hidden lg:block w-full overflow-x-auto" id="desktopWrapper">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Nama Supplier</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">No. HP</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Email</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Alamat</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $supplier)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2">{{ $supplier->nama_supplier }}</td>
                                <td class="border px-3 py-2">{{ $supplier->no_hp ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $supplier->email ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $supplier->alamat ?? '-' }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('suppliers.show', $supplier->id) }}"
                                            class="text-green-600 hover:underline">Detail</a>

                                        @can('suppliers.update')
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                            class="text-blue-600 hover:underline">Edit</a>
                                        @endcan

                                        @can('suppliers.delete')
                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="block lg:hidden mt-4" id="mobileWrapper">
                    <div class="flex justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="border rounded text-sm mx-1">
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                            entries
                        </div>
                    </div>

                    <div id="mobileCards" class="space-y-3">
                        @foreach ($suppliers as $supplier)
                        <div class="mobile-card border rounded-lg bg-white shadow">
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="font-semibold text-gray-900">{{ $supplier->nama_supplier }}</div>
                                <div class="text-xs text-gray-500">Supplier #{{ $loop->iteration }}</div>
                            </div>

                            <div class="px-4 py-3 text-sm space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">No. HP:</span>
                                    <span class="text-gray-900">{{ $supplier->no_hp ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="text-gray-900">{{ $supplier->email ?? '-' }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-gray-600">Alamat:</span>
                                    <span class="text-gray-900 text-sm">{{ $supplier->alamat ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                <a href="{{ route('suppliers.show', $supplier->id) }}"
                                    class="flex-1 border border-green-600 text-green-600 rounded text-center py-2 hover:bg-green-50">Detail</a>

                                @can('suppliers.update')
                                <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                    class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2 hover:bg-blue-50">Edit</a>
                                @endcan

                                @can('suppliers.delete')
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="flex-1" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full border border-red-600 text-red-600 rounded text-center py-2 hover:bg-red-50">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center w-full"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let dataTable = null;
        const cards = [...document.querySelectorAll('.mobile-card')];
        const info = document.getElementById('mobileInfo');
        const pagination = document.getElementById('mobilePagination');
        const perPageSelect = document.getElementById('mobilePerPage');

        let perPage = parseInt(perPageSelect.value);
        let currentPage = 1;

        function renderMobile() {
            const total = cards.length;
            const pages = Math.ceil(total / perPage);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            cards.forEach((c, i) => {
                c.style.display = i >= start && i < end ? 'block' : 'none';
            });

            if (total === 0) {
                info.textContent = 'Showing 0 to 0 of 0 entries';
            } else {
                info.textContent = `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;
            }

            renderPagination(pages);
        }

        function renderPagination(totalPages) {
            pagination.innerHTML = '';
            const hasData = totalPages > 0;
            const maxVisible = 5;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);

            const createBtn = (label, disabled, active, cb) => {
                const btn = document.createElement('button');
                btn.textContent = label;
                btn.disabled = disabled;
                btn.className = `px-3 py-1 text-sm rounded-md border ${active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'} ${disabled ? 'opacity-50 cursor-not-allowed' : ''}`;
                btn.onclick = cb;
                return btn;
            };

            pagination.appendChild(createBtn('Prev', !hasData || currentPage === 1, false, () => {
                if (!hasData || currentPage === 1) return;
                currentPage--;
                renderMobile();
            }));

            for (let i = startPage; i <= endPage; i++) {
                pagination.appendChild(createBtn(i, false, i === currentPage, () => {
                    currentPage = i;
                    renderMobile();
                }));
            }

            pagination.appendChild(createBtn('Next', !hasData || currentPage === totalPages, false, () => {
                if (!hasData || currentPage === totalPages) return;
                currentPage++;
                renderMobile();
            }));
        }

        perPageSelect.onchange = () => {
            perPage = parseInt(perPageSelect.value);
            currentPage = 1;
            renderMobile();
        };

        function handleResponsive() {
            const isDesktop = window.innerWidth >= 1024;
            const mobileWrapper = document.getElementById('mobileWrapper');
            const desktopWrapper = document.getElementById('desktopWrapper');

            if (isDesktop) {
                desktopWrapper.style.display = 'block';
                mobileWrapper.style.display = 'none';

                if (!dataTable) {
                    dataTable = new DataTable('#dataTables');
                }
            } else {
                if (dataTable) {
                    dataTable.destroy();
                    dataTable = null;
                }

                desktopWrapper.style.display = 'none';
                mobileWrapper.style.display = 'block';
                renderMobile();
            }
        }

        handleResponsive();
        window.addEventListener('resize', handleResponsive);
    });
</script>
@endpush
