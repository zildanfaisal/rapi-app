@extends('layouts.app')

@section('title', __('Pelanggan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Pelanggan') }}</h2>
@endsection

@section('content')
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4">{{ __('Pelanggan') }}</h3>
                        <a href="{{ route('customers.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Pelanggan
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                   <table class="min-w-full border border-gray-300" id="dataTables">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Pelanggan</th>
                                <th class="px-4 py-2 border">No. HP</th>
                                <th class="px-4 py-2 border">E-mail</th>
                                <th class="px-4 py-2 border">Alamat</th>
                                <th class="px-4 py-2 border">Poin</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($customers as $c)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ $c->nama_customer }}</td>
                                    <td class="px-4 py-2 border">{{ $c->no_hp }}</td>
                                    <td class="px-4 py-2 border">{{ $c->email }}</td>
                                    <td class="px-4 py-2 border">{{ $c->alamat }}</td>
                                    <td class="px-4 py-2 border">{{ $c->point }}</td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('customers.show', $c->id) }}"
                                        class="text-green-600 hover:underline">
                                            Detail
                                        </a>

                                        <a href="{{ route('customers.edit', $c->id) }}"
                                        class="text-blue-600 hover:underline ms-4">
                                            Edit
                                        </a>

                                        <form action="{{ route('customers.destroy', $c->id) }}"
                                            method="POST"
                                            class="inline"
                                            data-confirm-delete>
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-red-600 hover:underline ms-4">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
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
