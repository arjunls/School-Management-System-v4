<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Legger Nilai</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #666; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #1e40af;
            color: #fff;
            padding: 6px 4px;
            text-align: center;
            font-size: 9px;
            border: 1px solid #1e40af;
        }
        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
        }
        tr:nth-child(even) { background: #f9fafb; }
        .no { width: 30px; }
        .text-left { text-align: left; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
        .footer .signature { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>LEGGER NILAI</p>
        <p>Kelas: <strong>{{ $kelas->name }}</strong> | Mata Pelajaran: <strong>{{ $subject->name }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="text-left">NISN</th>
                <th class="text-left">Nama Siswa</th>
                <th>Nilai</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
            @php $grade = $grades->get($student->id); @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $student->nisn ?? '-' }}</td>
                <td class="text-left">{{ $student->name }}</td>
                <td>{{ $grade ? number_format($grade->score, 0) : '-' }}</td>
                <td>{{ $grade->grade ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:12px;">Tidak ada siswa</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <div class="signature">
            <p>Mengetahui,</p>
            <br><br>
            <p>_________________________</p>
            <p>Guru Mata Pelajaran</p>
        </div>
    </div>
</body>
</html>
