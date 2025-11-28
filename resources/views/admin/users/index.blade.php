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
                    <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Create User
                    </a>
                </div>

                @if(session('status'))
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                @endif

                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border text-left">Name</th>
                            <th class="px-4 py-2 border text-left">Email</th>
                            <th class="px-4 py-2 border text-left">Status</th>
                            <th class="px-4 py-2 border text-left">Roles</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    @if($users->isEmpty())
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-6">Belum Ada User.</td>
                            </tr>
                        </tbody>
                    @endif
                    <tbody>
                        @foreach ($users as $user)
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
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ms-4" onclick="return confirm('Delete user?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection