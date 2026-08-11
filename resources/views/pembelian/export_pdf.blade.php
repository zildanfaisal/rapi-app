<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            margin-bottom: 16px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .meta {
            font-size: 11px;
            color: #374151;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 10px;
        }

        th {
            background: #f3f4f6;
            text-transform: uppercase;
            text-align: center;
        }

        td.left {
            text-align: left;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Laporan Pembelian</div>
        <div class="meta">
            Status: {{ $statusLabel }}<br>
            Tanggal: {{ $dateFrom ?: '-' }} s/d {{ $dateTo ?: '-' }}<br>
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
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
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="left">{{ $p->invoice_number }}</td>
                    <td class="left">{{ $p->supplier->nama_supplier ?? '-' }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($p->tanggal_pembelian)->format('d/m/Y') }}</td>
                    <td class="left">{{ $p->items->pluck('product.nama_produk')->filter()->join(', ') ?: '-' }}</td>
                    <td class="center">{{ $qty }}</td>
                    <td class="right">Rp {{ number_format($p->grand_total, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format(max(0, (float) $p->grand_total - $totalBayar), 0, ',', '.') }}</td>
                    <td class="center">{{ $status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Tidak ada data pembelian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
