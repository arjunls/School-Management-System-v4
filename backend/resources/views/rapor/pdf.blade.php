<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapor - {{ $student->name }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header p { margin: 2px; font-size: 12px; color: #475569; }
        .info { width: 100%; margin-bottom: 20px; }
        .info td { padding: 3px 10px; font-size: 11px; }
        .info td:first-child { width: 140px; font-weight: bold; }
        table.grades { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.grades th { background: #1e293b; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        table.grades td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        table.grades tr:nth-child(even) { background: #f8fafc; }
        .rata { text-align: right; font-size: 13px; font-weight: bold; margin-top: 10px; padding-top: 10px; border-top: 2px solid #1e293b; }
        .footer { margin-top: 40px; text-align: right; font-size: 11px; }
        .footer .ttd { margin-top: 60px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN HASIL BELAJAR</h1>
        <p>SMK Negeri 1 - Tahun Ajaran {{ $term->academicYear->name }}</p>
        <p>Semester: {{ $term->name }}</p>
    </div>

    <table class="info">
        <tr><td>Nama Siswa</td><td>: {{ $student->name }}</td></tr>
        <tr><td>NISN</td><td>: {{ $student->nisn ?? '-' }}</td></tr>
        <tr><td>Kelas</td><td>: {{ $student->kelas?->name ?? '-' }}</td></tr>
        <tr><td>Jurusan</td><td>: {{ $student->jurusan ?? '-' }}</td></tr>
    </table>

    <table class="grades">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $i => $g)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $g->subject?->name ?? '-' }}</td>
                <td>{{ number_format($g->score, 0) }}</td>
                <td>
                    @if($g->score >= 90)
                        <span style="color:#059669">Sangat Baik</span>
                    @elseif($g->score >= 75)
                        <span style="color:#0284c7">Baik</span>
                    @elseif($g->score >= 60)
                        <span style="color:#d97706">Cukup</span>
                    @else
                        <span style="color:#dc2626">Kurang</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;padding:20px;color:#94a3b8">Belum ada nilai</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($grades->isNotEmpty())
    <div class="rata">
        Rata-rata Nilai: {{ number_format($rataRata, 2) }}
        @if($rataRata >= 75)
            <span style="color:#059669"> (Tuntas)</span>
        @else
            <span style="color:#dc2626"> (Belum Tuntas)</span>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>{{ now()->format('d F Y') }}</p>
        <p>Kepala Sekolah / Wali Kelas</p>
        <div class="ttd">
            <p>____________________________</p>
        </div>
    </div>
</body>
</html>
