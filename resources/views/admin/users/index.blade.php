@extends('layouts.app')

@section('title', __('Users'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Users') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Users') }}</h3>
                    @can('users.create')
                    <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Create User
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
                            <th class="px-4 py-2 border text-left">Email</th>
                            <th class="px-4 py-2 border text-left">Status</th>
                            <th class="px-4 py-2 border text-left">Roles</th>
                            @canany(['users.update','users.delete'])
                            <th class="px-4 py-2 border">Aksi</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ ($users->currentPage()-1) * $users->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-2 border text-left">{{ $user->name }}</td>
                                <td class="px-4 py-2 border text-left">{{ $user->email }}</td>
                                <td class="px-4 py-2 border text-left">
                                    <span class="inline-block px-2 py-1 rounded text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($user->status ?? 'inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border text-left">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                                @canany(['users.update','users.delete'])
                                <td class="px-4 py-2 border">
                                    @can('users.update')
                                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                                    @endcan
                                    @can('users.delete')
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ms-4" >Hapus</button>
                                    </form>
                                    @endcan
                                </td>
                                @endcanany
                            </tr>
                        @empty
                                <tr class="text-center">
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border">Belum Ada User.</td>
                                    <td class="px-4 py-2 border"></td>
                                    <td class="px-4 py-2 border"></td>
                                </tr>
                            @endforelse
                    </tbody>
                </table>
            </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    new DataTable('#dataTables', {
        responsive: true
    });
</script>
@endpush
