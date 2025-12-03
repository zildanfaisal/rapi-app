@extends('layouts.app')

@section('title', __('Permissions'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Permissions') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Permissions') }}</h3>
                    @can('permissions.create')
                    <a href="{{ route('permissions.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Create Permission
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
                            <th class="px-4 py-2 border text-left">Name</th>
                            @canany(['permissions.update','permissions.delete'])
                            <th class="px-4 py-2 border">Aksi</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border text-left">{{ $permission->name }}</td>
                                @canany(['permissions.update','permissions.delete'])
                                <td class="px-4 py-2 border">
                                    @can('permissions.update')
                                    <a href="{{ route('permissions.edit', $permission) }}" class="text-blue-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('permissions.delete')
                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ms-4"  >Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                                @endcanany
                            </tr>
                         @empty
                                <tr class="text-center">
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border">Belum Ada Customer.</td>
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
