@extends('layouts.app')

@section('title', __('Detail Supplier'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Supplier') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h3 class="mb-4 font-semibold text-lg">Informasi Supplier</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Supplier</p>
                    <p class="font-medium">{{ $supplier->nama_supplier }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">No. HP</p>
                    <p class="font-medium">{{ $supplier->no_hp ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $supplier->email ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Alamat</p>
                    <p class="font-medium">{{ $supplier->alamat ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                @can('suppliers.update')
                <a href="{{ route('suppliers.edit', $supplier->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
                @endcan

                <a href="{{ route('suppliers.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
