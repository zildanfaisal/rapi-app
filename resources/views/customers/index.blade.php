@extends('layouts.app')

@section('title', __('Customers'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Customers') }}</h2>
@endsection

@section('content')
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4">{{ __('Customers') }}</h3>
                        <a href="{{ route('customers.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Customers
                        </a>
                    </div>
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Customer</th>
                                <th class="px-4 py-2 border">No. HP</th>
                                <th class="px-4 py-2 border">E-mail</th>
                                <th class="px-4 py-2 border">Alamat</th>
                                <th class="px-4 py-2 border">Point</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        @if($customers->isEmpty())
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-6">Belum Ada Customer.</td>
                                </tr>
                            </tbody>
                        @endif
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
                                        <a href="{{ route('customers.edit', $c->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('customers.destroy', $c->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
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
@endsection