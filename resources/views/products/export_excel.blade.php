<table border="1">
    <thead>
        <tr>
            <th colspan="10" style="font-size: 16px; text-align: left;">Laporan Master Produk</th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: left;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr>
            <th>No</th>
            <th>Barcode</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Supplier</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $index => $p)
            @php
                $stok = $p->batches->sum('quantity_sekarang');
                $statusLabel = $p->status === 'available' ? 'Tersedia' : 'Tidak Tersedia';
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->barcode }}</td>
                <td>{{ $p->nama_produk }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ (float) $p->harga_beli }}</td>
                <td>{{ (float) $p->harga }}</td>
                <td>{{ $p->supplier ?? '-' }}</td>
                <td>{{ $stok }}</td>
                <td>{{ $p->satuan }}</td>
                <td>{{ $statusLabel }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">Tidak ada data produk.</td>
            </tr>
        @endforelse
    </tbody>
</table>
