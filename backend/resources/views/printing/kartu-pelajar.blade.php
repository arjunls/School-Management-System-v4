<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Kartu Pelajar</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 12px; }
        .card {
            width: 85.6mm; height: 54mm;
            border: 2px solid #1e40af;
            border-radius: 8px;
            padding: 8px;
            position: relative;
            background: #fff;
            page-break-after: always;
        }
        .header {
            background: #1e40af;
            color: #fff;
            text-align: center;
            padding: 4px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
            margin: -8px -8px 6px -8px;
        }
        .photo { width: 75px; height: 90px; border: 1px solid #ccc; float: left; margin-right: 8px; }
        .info { float: left; font-size: 10px; line-height: 1.6; }
        .info strong { display: inline-block; width: 45px; }
        .clear { clear: both; }
        .footer {
            text-align: center;
            font-size: 7px;
            color: #666;
            margin-top: 6px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">KARTU PELAJAR</div>
        <div class="photo">
            <div style="width:75px;height:90px;background:#e5e7eb;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:24px;">
                @if($student->photo)
                <img src="{{ $student->photo }}" style="width:75px;height:90px;object-fit:cover;">
                @else
                <i class="fas fa-user"></i>
                @endif
            </div>
        </div>
        <div class="info">
            <strong>NISN</strong> : {{ $student->nisn ?? '-' }}<br>
            <strong>Nama</strong> : {{ $student->name }}<br>
            <strong>Kelas</strong> : {{ $student->kelas->name ?? '-' }}<br>
            <strong>TTL</strong> : {{ $student->tempat_lahir ?? '-' }}, {{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : '-' }}<br>
            <strong>Alamat</strong> : {{ Str::limit($student->alamat ?? $student->address ?? '-', 30) }}
        </div>
        <div class="clear"></div>
        <div class="footer">
            {{ config('app.name') }} - {{ date('Y') }}
        </div>
    </div>
</body>
</html>
