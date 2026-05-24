<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Induk Siswa{{ $kelas ? ' - ' . $kelas->name : '' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 12px; font-weight: normal; margin-top: 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #e5e7eb; font-size: 9px; text-align: center; }
        td { font-size: 9px; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>BUKU INDUK SISWA</h1>
    <h2>{{ config('app.name') }} {{ $kelas ? ' - Kelas ' . $kelas->name : '' }}</h2>
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>NISN</th>
                <th>Nama Lengkap</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Kelas</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $s)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="nowrap">{{ $s->nisn ?? '-' }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->tempat_lahir ?? '-' }}</td>
                <td class="nowrap">{{ $s->date_of_birth ? $s->date_of_birth->format('d M Y') : '-' }}</td>
                <td class="text-center">{{ $s->gender ?? '-' }}</td>
                <td>{{ $s->alamat ?? '-' }}</td>
                <td>{{ $s->kelas->name ?? '-' }}</td>
                <td class="text-center">{{ $s->status ?? 'active' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Tidak ada data siswa</td></tr>
            @endforelse
        </tbody>
    </table>
    <p style="margin-top: 20px; font-size: 9px;">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
