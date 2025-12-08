@extends('layouts.app')

@section('title', __('Edit Data Keuangan'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Edit Data Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Edit Data Keuangan') }}</h3>
                <form method="POST" action="{{ route('finance-records.update', $financeRecord->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $financeRecord->tanggal) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('tanggal')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Tipe Transaksi</label>
                        <select name="tipe" class="w-full px-3 py-2 border rounded" required>
                            <option value="">Pilih Tipe</option>
                            <option value="income" {{ old('tipe', $financeRecord->tipe) === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ old('tipe', $financeRecord->tipe) === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                        @error('tipe')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori', $financeRecord->kategori) }}" class="w-full px-3 py-2 border rounded" placeholder="Contoh: Gaji, Belanja, Transport, dll" required />
                        @error('kategori')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Jumlah</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="text" id="jumlah_display" class="w-full px-3 py-2 pl-10 border rounded" placeholder="0" />
                            <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah', $financeRecord->jumlah) }}" required />
                        </div>
                        @error('jumlah')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded" placeholder="Catatan tambahan...">{{ old('deskripsi', $financeRecord->deskripsi) }}</textarea>
                        @error('deskripsi')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Update</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('finance-records.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const jumlahDisplay = document.getElementById('jumlah_display');
    const jumlahHidden = document.getElementById('jumlah');

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(angka) {
        return angka.replace(/[^0-9]/g, '');
    }

    if (jumlahHidden.value) {
        jumlahDisplay.value = formatRupiah(jumlahHidden.value);
    }

    jumlahDisplay.addEventListener('input', function(e) {
        let value = unformatRupiah(e.target.value);
        jumlahHidden.value = value;
        if (value) {
            e.target.value = formatRupiah(value);
        } else {
            e.target.value = '';
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!jumlahHidden.value) {
            e.preventDefault();
            alert('Jumlah harus diisi');
        }
    });
</script>
@endpush
