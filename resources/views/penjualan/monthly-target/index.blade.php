@extends('layouts.app')

@section('title', __('Target Bulanan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Target Bulanan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4">{{ __('Daftar Target Bulanan') }}</h3>
                    <a href="{{ route('monthly-targets.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ Buat Target</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTablesTargets">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Periode</th>
                                <th class="px-4 py-2 border">Target</th>
                                <th class="px-4 py-2 border">Sisa Menuju Target</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($targets as $t)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $t->name }}</td>
                                    <td class="px-4 py-2 border">{{ $t->start_date }} s/d {{ $t->end_date }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($t->target_amount ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($t->computed_remaining ?? ($t->achieved_total ?? 0), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $t->status; @endphp
                                        @if ($status === 'achieved')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Tercapai</span>
                                        @elseif ($status === 'ongoing')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Berjalan</span>
                                        @elseif ($status === 'missed')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Tidak Tercapai</span>
                                        @else
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('monthly-targets.show', $t->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                        <a href="{{ route('monthly-targets.edit', $t->id) }}" class="text-indigo-600 hover:underline ms-4">Edit</a>
                                        <form action="{{ route('monthly-targets.destroy', $t->id) }}" method="POST" style="display:inline;" data-confirm-delete>
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

                <div class="mt-4">
                    {{ $targets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#dataTablesTargets');
</script>
@endpush
