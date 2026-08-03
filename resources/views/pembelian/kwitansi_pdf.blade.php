<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $pembelian->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 15px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .logo-wrapper {
            display: table;
            width: 100%;
        }

        .logo-cell {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
        }

        .company-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .company-info {
            font-size: 9px;
            color: #666;
            line-height: 1.4;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .invoice-meta {
            font-size: 10px;
            color: #666;
        }

        .invoice-meta strong {
            color: #333;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-box {
            display: table-cell;
            width: 48%;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .info-box:first-child {
            margin-right: 4%;
        }

        .info-title {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 4px;
        }

        .info-content {
            font-size: 10px;
            line-height: 1.6;
        }

        .info-content strong {
            display: inline-block;
            width: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        thead {
            background: #2c3e50;
            color: white;
        }

        th {
            padding: 8px 6px;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
        }

        th.center {
            text-align: center;
        }

        th.right {
            text-align: right;
        }

        td {
            padding: 7px 6px;
            font-size: 10px;
            border-bottom: 1px solid #ddd;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        .summary {
            float: right;
            width: 250px;
            margin-top: 15px;
        }

        .summary-row {
            display: table;
            width: 100%;
            font-size: 10px;
        }

        .summary-label {
            display: table-cell;
            text-align: left;
            padding-right: 15px;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }

        .summary-total {
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
            margin-top: 8px;
            font-size: 11px;
            color: #2c3e50;
        }

        .summary-total .summary-value {
            font-size: 13px;
        }

        .payment-history {
            clear: both;
            margin-top: 20px;
        }

        .payment-history-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 4px;
            text-transform: uppercase;
        }

        .payment-history table th,
        .payment-history table td {
            font-size: 9px;
            padding: 6px 5px;
        }

        .signature-section {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 0 auto;
            width: 150px;
            padding-top: 4px;
        }

        .signature-name {
            font-size: 10px;
            font-weight: bold;
        }

        .signature-position {
            font-size: 9px;
            color: #666;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-lunas {
            background: #d4edda;
            color: #155724;
        }

        .status-belum {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancel {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <div class="logo-wrapper">
                    <div class="logo-cell">
                        <img src="{{ public_path('images/logo-rapi.png') }}" alt="Logo" class="logo">
                    </div>
                    <div class="company-cell">
                        <div class="company-name">RAPI PVC</div>
                    </div>
                </div>
                <div class="company-info">
                    Jl. Jend. Soeharto No.124, Naikolan, Kec. Maulafa<br>
                    Kota Kupang, Provinsi Nusa Tenggara Timur 85142<br>
                    Telp: 08881930769<br>
                    Email: <a href="#">rapipvcinterior@gmail.com</a> |
                    <img src="{{ public_path('images/facebook.svg') }}" alt="Facebook" style="height:10px;vertical-align:middle;"> Facebook: Rapipvc interiors<br>
                    <img src="{{ public_path('images/instagram.svg') }}" alt="Instagram" style="height:10px;vertical-align:middle;"> Instagram: Rapipvcinterior |
                    <img src="{{ public_path('images/tiktok.svg') }}" alt="TikTok" style="height:10px;vertical-align:middle;"> TikTok: Rapipvcinterior
                </div>
            </div>
            <div class="header-right">
                <div class="document-title">KWITANSI</div>
                <div class="invoice-meta">
                    <strong>No:</strong> {{ $pembelian->invoice_number }}<br>
                    <strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}<br>
                    @php
                        $statusLabel = match ($pembelian->status_pembayaran) {
                            'paid' => 'Lunas',
                            'partial' => 'Cicilan',
                            'cancelled' => 'Dibatalkan',
                            'unpaid' => 'Belum Dibayar',
                            'overdue' => 'Terlambat',
                            default => '-',
                        };
                    @endphp

                    @if ($pembelian->status_pembayaran == 'paid')
                        <span class="status-badge status-lunas">{{ $statusLabel }}</span>
                    @elseif($pembelian->status_pembayaran == 'cancelled')
                        <span class="status-badge status-cancel">{{ $statusLabel }}</span>
                    @else
                        <span class="status-badge status-belum">{{ $statusLabel }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <div class="info-title">Dari</div>
                <div class="info-content">
                    <strong>Supplier:</strong> {{ $pembelian->supplier->nama_supplier ?? '-' }}<br>
                    <strong>Telepon:</strong> {{ $pembelian->supplier->no_hp ?? '-' }}<br>
                    <strong>Alamat:</strong> {{ $pembelian->supplier->alamat ?? '-' }}
                </div>
            </div>
            <div class="info-box">
                <div class="info-title">Informasi Pembayaran</div>
                <div class="info-content">
                    <strong>Metode:</strong> {{ ucfirst($pembelian->metode_pembayaran ?? '-') }}<br>
                    <strong>Pencatat:</strong> {{ $pembelian->user->name ?? '-' }}<br>
                    <strong>Status:</strong> {{ $statusLabel }}<br>
                    <strong>Sisa:</strong> Rp {{ number_format($pembelian->sisa_tagihan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Nama Produk</th>
                    <th class="center" style="width: 10%;">Kuantitas</th>
                    <th class="right" style="width: 20%;">Harga Satuan</th>
                    <th class="right" style="width: 20%;">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembelian->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->nama_produk ?? ($item->product->nama ?? 'Produk #' . $item->product_id) }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Total Dibayar:</span>
                <span class="summary-value">Rp {{ number_format($pembelian->pembayarans->sum('jumlah_bayar'), 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Sisa Tagihan:</span>
                <span class="summary-value">Rp {{ number_format($pembelian->sisa_tagihan, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row summary-total">
                <span class="summary-label">GRAND TOTAL:</span>
                <span class="summary-value">Rp {{ number_format($pembelian->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="payment-history">
            <div class="payment-history-title">Riwayat Pembayaran</div>
            @if ($pembelian->pembayarans->isEmpty())
                <div style="font-size:10px; color:#666;">Belum ada riwayat pembayaran.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">Tanggal</th>
                            <th style="width: 20%;">Metode</th>
                            <th class="right" style="width: 20%;">Jumlah Bayar</th>
                            <th class="right" style="width: 20%;">Total Dibayar</th>
                            <th class="right" style="width: 15%;">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $akumulasiBayar = 0; @endphp
                        @foreach ($pembelian->pembayarans->sortBy('tanggal_bayar')->values() as $index => $pembayaran)
                            @php
                                $akumulasiBayar += (float) $pembayaran->jumlah_bayar;
                                $sisaTagihan = max(0, (float) $pembelian->grand_total - $akumulasiBayar);
                            @endphp
                            <tr>
                                <td class="center">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($pembayaran->metode_pembayaran ?? '-') }}</td>
                                <td class="right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="right">Rp {{ number_format($akumulasiBayar, 0, ',', '.') }}</td>
                                <td class="right">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div style="clear:both; margin-top: 20px;">
            <div style="
                background: #fffbeb;
                border-left: 4px solid #f59e0b;
                border-radius: 4px;
                padding: 8px 12px;
                display: inline-block;
                width: 100%;
                box-sizing: border-box;
            ">
                <span style="font-size: 12px; font-weight: bold; color: #92400e;">Catatan:</span>
                <span style="font-size: 12px; color: #78350f; font-style: italic;">
                    Barang yang sudah dibeli, tidak dapat ditukar atau dikembalikan.
                </span>
            </div>
        </div>

        <div class="signature-section">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                        <div class="signature-title">Penerima</div>
                        <div class="signature-line">
                            <div class="signature-name">{{ $pembelian->supplier->nama_supplier ?? '_______________' }}</div>
                            <div class="signature-position">Supplier</div>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                        <div class="signature-title">Hormat Kami</div>
                        <div class="signature-line">
                            <div class="signature-name">{{ $pembelian->user->name ?? 'Admin' }}</div>
                            <div class="signature-position">{{ $pembelian->user->jabatan ?? 'Staff Pembelian' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda. Dokumen ini dicetak secara otomatis</p>
        </div>
    </div>
</body>

</html>
