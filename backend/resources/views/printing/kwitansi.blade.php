<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Kwitansi</title>
    <style>
        @page { margin: 30px; }
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; text-transform: uppercase; }
        .header p { font-size: 10px; color: #666; margin: 0; }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 6px 0;
            margin-bottom: 16px;
        }
        table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 8px; font-size: 11px; }
        .info-table td:first-child { width: 120px; font-weight: bold; }
        .detail-table { margin-top: 12px; }
        .detail-table th {
            background: #1e40af;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        .detail-table td { padding: 6px 8px; border: 1px solid #ddd; font-size: 11px; }
        .detail-table tr:nth-child(even) { background: #f9fafb; }
        .total-row td { font-weight: bold; font-size: 12px; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
        .footer .signature { margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Jl. Pendidikan No.1, Kota</p>
    </div>

    <div class="title">KWITANSI PEMBAYARAN</div>

    <table class="info-table">
        <tr><td>No. Invoice</td><td>: {{ $invoice->id }}</td></tr>
        <tr><td>Nama Siswa</td><td>: {{ $invoice->student->name ?? '-' }}</td></tr>
        <tr><td>Jenis Pembayaran</td><td>: {{ $invoice->feeType->name ?? '-' }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ $invoice->created_at->format('d/m/Y') }}</td></tr>
        <tr><td>Jatuh Tempo</td><td>: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td></tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th style="text-align:right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->feeType->name ?? 'Pembayaran' }} - {{ $invoice->notes ?? '-' }}</td>
                <td style="text-align:right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
            @php $paid = $invoice->getPaidAmount(); @endphp
            @if($paid > 0)
            <tr>
                <td>Total Dibayar</td>
                <td style="text-align:right">Rp {{ number_format($paid, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Sisa Tagihan</td>
                <td style="text-align:right">Rp {{ number_format($invoice->getRemainingAmount(), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Status: <strong>{{ ucfirst($invoice->status) }}</strong></p>
        <div class="signature">
            <p>Hormat Kami,</p>
            <br><br>
            <p>_________________________</p>
            <p>Bendahara</p>
        </div>
    </div>
</body>
</html>
