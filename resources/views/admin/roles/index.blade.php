@extends('layouts.app')

@section('title', __('Peran'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Peran') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Peran') }}</h3>
                    @can('roles.create')
                    <a href="{{ route('roles.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Tambah Peran
                    </a>
                    @endcan
                </div>

                @if(session('status'))
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                @endif
                <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300" id="dataTables">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border text-left">Nama</th>
                            <th class="px-4 py-2 border text-left">Izin</th>
                            @canany(['roles.update','roles.delete'])
                            <th class="px-4 py-2 border">Aksi</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border text-left">{{ $role->name }}</td>
                                <td class="px-4 py-2 border text-left">{{ $role->permissions->pluck('name')->join(', ') }}</td>
                                @canany(['roles.update','roles.delete'])
                                <td class="px-4 py-2 border">
                                    @can('roles.update')
                                    <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('roles.delete')
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                                @endcanany
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
