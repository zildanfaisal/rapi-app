@extends('layouts.app')

@section('title', __('Tambah Target Anggaran'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Tambah Target Anggaran') }}</h2>
@endsection

@push('scripts')
<script>
    const budgetDisplay = document.getElementById('budget_display');
    const budgetHidden = document.getElementById('budget_bulanan');

    // Format number with thousand separator
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Remove non-numeric characters
    function unformatRupiah(angka) {
        return angka.replace(/[^0-9]/g, '');
    }

    // Set initial value if old value exists
    if (budgetHidden.value) {
        budgetDisplay.value = formatRupiah(budgetHidden.value);
    }

    budgetDisplay.addEventListener('input', function(e) {
        let value = unformatRupiah(e.target.value);

        // Update hidden input with raw number
        budgetHidden.value = value;

        // Update display with formatted number
        if (value) {
            e.target.value = formatRupiah(value);
        } else {
            e.target.value = '';
        }
    });

    // Prevent form submission if empty
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!budgetHidden.value) {
            e.preventDefault();
            alert('Budget bulanan harus diisi');
        }
    });
</script>
@endpush

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Tambah Target Anggaran') }}</h3>
                <form method="POST" action="{{ route('budget-target.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="w-full px-3 py-2 border rounded" required />
                        @error('tanggal')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Budget Bulanan</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="text" id="budget_display" class="w-full px-3 py-2 pl-10 border rounded" placeholder="0" />
                            <input type="hidden" name="budget_bulanan" id="budget_bulanan" value="{{ old('budget_bulanan') }}" required />
                        </div>
                        @error('budget_bulanan')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Simpan</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('budget-target.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
