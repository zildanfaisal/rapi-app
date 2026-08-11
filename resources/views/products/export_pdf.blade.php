<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Master Produk</title>
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
        <div class="title">Laporan Master Produk</div>
        <div class="meta">
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
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
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $p->barcode }}</td>
                    <td class="left">{{ $p->nama_produk }}</td>
                    <td class="center">{{ $p->kategori }}</td>
                    <td class="right">Rp {{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td class="left">{{ $p->supplier ?? '-' }}</td>
                    <td class="center">{{ $stok }}</td>
                    <td class="center">{{ $p->satuan }}</td>
                    <td class="center">{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
