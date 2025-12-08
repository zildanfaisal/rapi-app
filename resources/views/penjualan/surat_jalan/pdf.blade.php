<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan {{ $suratJalan->nomor_surat_jalan ?? $suratJalan->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; }
        .meta { font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; font-size: 12px; }
        thead { background: #f5f5f5; }
        .right { text-align: right; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .footer { margin-top: 24px; font-size: 12px; color: #555; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <div class="title">Surat Jalan</div>
            <div class="meta">No: {{ $suratJalan->nomor_surat_jalan ?? '-' }}</div>
            <div class="meta">Tanggal: {{ $suratJalan->tanggal }}</div>
        </div>
        <div>
            <div class="title">Pelanggan</div>
            <div class="meta">{{ $suratJalan->customer->nama_customer ?? '-' }}</div>
            <div class="meta">{{ $suratJalan->customer->no_hp ?? '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Nomor Invoice</th>
            <th class="right">Grand Total Invoice</th>
            <th class="right">Ongkir</th>
            <th class="right">Grand Total SJ</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $suratJalan->invoice->invoice_number ?? $suratJalan->invoice_id }}</td>
                <td class="right">Rp {{ number_format($suratJalan->invoice->grand_total ?? 0, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($suratJalan->ongkos_kirim ?? 0, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($suratJalan->grand_total ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer mt-4">
        Status Pembayaran: {{ ucfirst($suratJalan->status_pembayaran) }}
        @if($suratJalan->status_pembayaran === 'cancel')
            <div class="mt-2">Alasan Batal: {{ $suratJalan->alasan_cancel }}</div>
        @endif
    </div>
</div>
</body>
</html>
