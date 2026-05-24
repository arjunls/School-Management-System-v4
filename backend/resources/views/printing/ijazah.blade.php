<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Ijazah - {{ $student->name }}</title>
    <style>
        @page { margin: 30px 40px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
        }
        .border-frame {
            border: 2px solid #1e40af;
            padding: 8px;
            position: relative;
        }
        .inner-border {
            border: 1px solid #1e40af;
            padding: 20px 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 8px;
        }
        .header .logo img {
            max-width: 80px;
            max-height: 80px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 14px;
            margin: 0 0 2px;
            font-weight: bold;
        }
        .header p {
            font-size: 10px;
            margin: 0;
            color: #555;
        }
        .title {
            text-align: center;
            margin: 24px 0 20px;
        }
        .title h3 {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 4px;
        }
        .title p {
            font-size: 10px;
            margin: 0;
            color: #555;
        }
        .student-data {
            margin-bottom: 16px;
        }
        .student-data table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-data td {
            padding: 3px 6px;
            font-size: 11px;
        }
        .student-data .label {
            width: 120px;
            font-weight: bold;
        }
        .student-data .separator {
            width: 16px;
            text-align: center;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        .grades-table th {
            background: #1e40af;
            color: #fff;
            padding: 6px 4px;
            font-size: 9px;
            border: 1px solid #1e40af;
            text-align: center;
        }
        .grades-table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }
        .grades-table .text-left { text-align: left; }
        .rata {
            text-align: right;
            font-weight: bold;
            font-size: 11px;
            margin: 8px 0;
        }
        .footer {
            margin-top: 30px;
        }
        .footer table {
            width: 100%;
        }
        .footer td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 20px;
        }
        .footer .city-date {
            font-size: 10px;
            margin-bottom: 60px;
        }
        .footer .sign-line {
            margin-top: 4px;
            font-size: 10px;
        }
        .cert-number {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin: 10px 0;
        }
        .keterangan {
            text-align: justify;
            font-size: 10px;
            margin: 12px 0;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="border-frame">
        <div class="inner-border">
            <div class="header">
                @if($school && $school->logo)
                    <div class="logo"><img src="{{ public_path('storage/' . $school->logo) }}" alt="Logo"></div>
                @endif
                <h1>{{ $school->name ?? config('app.name') }}</h1>
                <p>{{ $school->address ?? '' }} | NPSN: {{ $school->npsn ?? '-' }}</p>
                <p>Terakreditasi {{ $school->akreditasi ?? 'A' }}</p>
            </div>

            <div class="title">
                <h3>IJAZAH</h3>
                <p>Sekolah Menengah Kejuruan (SMK)</p>
                <p>Program {{ $student->jurusan ?? 'Keahlian' }}</p>
            </div>

            <div class="cert-number">
                Nomor Ijazah: <strong>{{ $cert->certificate_number }}</strong>
            </div>

            <div class="student-data">
                <table>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td><strong>{{ $student->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Tempat/Tgl Lahir</td>
                        <td class="separator">:</td>
                        <td>{{ $student->tempat_lahir ?? '-' }} / {{ $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NISN</td>
                        <td class="separator">:</td>
                        <td>{{ $student->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="separator">:</td>
                        <td>{{ $student->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jurusan</td>
                        <td class="separator">:</td>
                        <td>{{ $student->jurusan ?? $student->kelas->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="keterangan">
                <strong>Keterangan:</strong>
                <p style="margin: 4px 0 0;">
                    Berdasarkan hasil ujian dan evaluasi, peserta didik yang tersebut di atas dinyatakan
                    <strong>LULUS</strong> dari satuan pendidikan dengan nilai sebagai berikut:
                </p>
            </div>

            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="width:30px;">No</th>
                        <th class="text-left">Mata Pelajaran</th>
                        <th style="width:80px;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $index => $grade)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $grade->subject->name ?? '-' }}</td>
                        <td>{{ number_format($grade->score, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:8px;">Belum ada nilai</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="rata">
                Nilai Rata-rata: <strong>{{ number_format($rataNilai, 2) }}</strong>
            </div>

            <div class="footer">
                <table>
                    <tr>
                        <td>
                            <div class="city-date">
                                {{ $school->city ?? '' }}, {{ now()->format('d F Y') }}
                            </div>
                            <div>Kepala Sekolah,</div>
                            <div style="margin-top:50px;">
                                <u><strong>{{ $school->kepala_sekolah ?? '________________' }}</strong></u>
                            </div>
                            <div class="sign-line">NIP. {{ $school->kepala_sekolah_nip ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="city-date">&nbsp;</div>
                            <div>Wali Kelas,</div>
                            <div style="margin-top:50px;">
                                <u><strong>{{ $student->kelas->homeroomTeacher->name ?? '________________' }}</strong></u>
                            </div>
                            <div class="sign-line">NIP. {{ $student->kelas->homeroomTeacher->nip ?? '-' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
