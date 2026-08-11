<table border="1">
    <thead>
        <tr>
            <th colspan="10" style="font-size: 16px; text-align: left;">Laporan Pembelian</th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: left;">
                Status: {{ $statusLabel }} |
                Tanggal: {{ $dateFrom ?: '-' }} s/d {{ $dateTo ?: '-' }} |
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr>
            <th>No</th>
            <th>Invoice</th>
            <th>Supplier</th>
            <th>Tanggal</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Total Bayar</th>
            <th>Sisa Tagihan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pembelians as $index => $p)
            @php
                $status = match ($p->status_pembayaran) {
                    'paid' => 'Lunas',
                    'unpaid' => 'Belum Lunas',
                    'partial' => 'Cicilan',
                    'overdue' => 'Terlambat',
                    'cancelled' => 'Batal',
                    default => ucfirst((string) $p->status_pembayaran),
                };
                $totalBayar = (float) $p->pembayarans->sum('jumlah_bayar');
                $qty = (int) $p->items->sum('quantity');
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->invoice_number }}</td>
                <td>{{ $p->supplier->nama_supplier ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_pembelian)->format('d/m/Y') }}</td>
                <td>{{ $p->items->pluck('product.nama_produk')->filter()->join(', ') ?: '-' }}</td>
                <td>{{ $qty }}</td>
                <td>{{ (float) $p->grand_total }}</td>
                <td>{{ $totalBayar }}</td>
                <td>{{ max(0, (float) $p->grand_total - $totalBayar) }}</td>
                <td>{{ $status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">Tidak ada data pembelian.</td>
            </tr>
        @endforelse
    </tbody>
</table>
