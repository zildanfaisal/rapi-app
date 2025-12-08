<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
        @media print {
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <div class="title">Invoice</div>
            <div class="meta">No: {{ $invoice->invoice_number }}</div>
            <div class="meta">Tanggal: {{ $invoice->tanggal_invoice }}</div>
            <div class="meta">Jatuh Tempo: {{ $invoice->tanggal_jatuh_tempo }}</div>
        </div>
        <div>
            <div class="title">Pelanggan</div>
            <div class="meta">{{ $invoice->customer->nama_customer ?? '-' }}</div>
            <div class="meta">{{ $invoice->customer->no_hp ?? '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Produk</th>
            <th class="right">Qty</th>
            <th class="right">Harga</th>
            <th class="right">Sub Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->nama_product ?? $item->product->nama ?? 'Produk #'.$item->product_id }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="right"><strong>Grand Total</strong></td>
            <td class="right"><strong>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong></td>
        </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
