<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan UKT {{ $periode }}</title>
    <style>
        @page {
            margin: 60px 50px 80px 50px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            color: #191c1c;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* ── KOP SURAT ── */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #006b3f;
            padding-bottom: 10px;
            margin-bottom: 2px;
        }
        .kop-logo {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
            text-align: center;
        }
        .kop-logo img {
            width: 75px;
            height: auto;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
            line-height: 1.5;
        }
        .kop-text .univ {
            font-size: 15px;
            font-weight: bold;
            color: #006b3f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text .fakultas {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text .alamat {
            font-size: 9.5px;
            margin-top: 3px;
            color: #444;
        }
        .garis-merah {
            height: 2px;
            background: #c0392b;
            margin-top: 3px;
        }

        /* ── JUDUL DOKUMEN ── */
        .doc-title {
            text-align: center;
            margin: 16px 0 12px;
        }
        .doc-title h2 {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            color: #006b3f;
            margin: 0;
        }
        .doc-title p {
            font-size: 10px;
            margin-top: 4px;
            color: #444;
        }

        /* ── INFO DOKUMEN ── */
        .doc-info {
            width: 100%;
            margin: 10px 0 15px;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }
        .doc-info td {
            padding: 5px 8px;
            font-size: 10px;
            vertical-align: top;
        }
        .doc-info .label { width: 110px; color: #3f4849; }
        .doc-info .value { font-weight: bold; color: #000; }
        .doc-info tr:nth-child(even) td { background: #f8faf9; }

        /* ── SECTION TITLE ── */
        .section-title {
            background: #006b3f;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 8px;
            margin: 18px 0 0;
            text-transform: uppercase;
        }

        /* ── STAT BOXES ── */
        .stat-row {
            display: table;
            width: 100%;
            margin: 12px 0;
            border-collapse: collapse;
        }
        .stat-box {
            display: table-cell;
            width: 20%;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .stat-box .num { font-size: 18px; font-weight: bold; color: #006b3f; }
        .stat-box .lbl { font-size: 8.5px; margin-top: 3px; color: #3f4849; font-weight: bold; }

        /* ── TABEL DATA ── */
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 10px;
        }
        table.data th {
            background: #006b3f;
            color: white;
            border: 1px solid #004d2c;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
        }
        table.data td {
            border: 1px solid #d1d5db;
            padding: 5px 4px;
            text-align: center;
        }
        table.data tr:nth-child(even) td { background: #f0fdf4; }
        table.data td.left { text-align: left; }
        table.data tr { page-break-inside: avoid; }
        table.data .total-row td {
            background: #e8f5ee;
            font-weight: bold;
            color: #006b3f;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 8px;
            font-size: 8.5px;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }
        .badge-1 { background: #d1fae5; color: #065f46; }
        .badge-2 { background: #dbeafe; color: #1e40af; }
        .badge-3 { background: #fef3c7; color: #92400e; }
        .badge-4 { background: #ffedd5; color: #9a3412; }
        .badge-5 { background: #fee2e2; color: #991b1b; }

        /* ── TANDA TANGAN ── */
        .ttd-section {
            margin-top: 40px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            font-size: 10px;
            padding: 0 10px;
        }
        .ttd-space { height: 60px; }
        .ttd-nama  { font-weight: bold; text-decoration: underline; color: #006b3f; margin-top: 4px; }
        .ttd-nip   { font-size: 9.5px; margin-top: 2px; }

        /* ── FOOTER FIXED ── */
        .page-footer {
            position: fixed;
            bottom: -50px;
            left: 0; right: 0;
            padding: 5px 0;
            font-size: 8px;
            display: table;
            width: 100%;
            border-top: 1px solid #006b3f;
            color: #3f4849;
        }
        .page-footer .left-f  { display: table-cell; text-align: left; font-style: italic; }
        .page-footer .right-f { display: table-cell; text-align: right; font-style: italic; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

{{-- FOOTER FIXED --}}
<div class="page-footer">
    <div class="left-f">Laporan Penentuan Golongan UKT — Metode AHP | Periode {{ $periode }}</div>
    <div class="right-f">Dicetak: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</div>
</div>

<main>

{{-- ══ KOP SURAT ══ --}}
<div class="kop">
    <div class="kop-logo">
        {{-- Ganti path logo sesuai lokasi file Anda di public/images/ --}}
        <img src="{{ public_path('images/logo-uinbkt.png') }}" alt="Logo" onerror="this.style.display='none'">
    </div>
    <div class="kop-text">
        <div class="univ">Kementerian Agama Republik Indonesia</div>
        <div class="fakultas">Universitas Islam Negeri Sjech M. Djamil Djambek Bukittinggi</div>
        <div class="fakultas">Fakultas Tarbiyah dan Ilmu Keguruan</div>
        <div class="alamat">
            Kampus II: Jl. Gurun Aua, Kubang Putiah, Kec. Banuhampu, Kab. Agam, Sumatera Barat 26181<br>
            Telp. (0752) 123456 | Website: www.uinbukittinggi.ac.id
        </div>
    </div>
</div>
<div class="garis-merah"></div>

{{-- ══ JUDUL DOKUMEN ══ --}}
<div class="doc-title">
    <h2>Laporan Hasil Penentuan Golongan Uang Kuliah Tunggal (UKT)</h2>
    <p>Menggunakan Metode AHP (Analytical Hierarchy Process)</p>
</div>

{{-- ══ INFO DOKUMEN ══ --}}
<table class="doc-info">
    <tr>
        <td class="label">Periode Akademik</td>
        <td class="value">: {{ $periode }}</td>
        <td class="label">Total Mahasiswa</td>
        <td class="value">: {{ $hasil->count() }} Orang</td>
    </tr>
    <tr>
        <td class="label">Metode Perhitungan</td>
        <td class="value">: AHP (Analytical Hierarchy Process)</td>
        <td class="label">Metode Golongan</td>
        <td class="value">: {{ ucfirst(str_replace('_', ' ', $metodePembagian)) }}</td>
    </tr>
    <tr>
        <td class="label">Rasio Konsistensi</td>
        <td class="value">: {{ number_format($ahpHasil['cr'], 4) }} ({{ $ahpHasil['konsisten'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' }})</td>
        <td class="label">Jumlah Kriteria</td>
        <td class="value">: {{ $kriterias->count() }} Kriteria</td>
    </tr>
    <tr>
        <td class="label">Tanggal Cetak</td>
        <td class="value">: {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
        <td class="label">λmax</td>
        <td class="value">: {{ number_format($ahpHasil['lambda_max'], 4) }}</td>
    </tr>
</table>

{{-- ══ STATISTIK DISTRIBUSI ══ --}}
<div class="section-title">A. Distribusi Golongan UKT</div>
<div class="stat-row">
    @foreach([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $labelG)
    @php
        $count = $distribusi->get($g, 0);
        $nom   = \App\Models\Pengaturan::nominalUkt($g);
    @endphp
    <div class="stat-box">
        <div class="num">{{ $count }}</div>
        <div class="lbl">{{ $nom['label'] }}</div>
        <div class="lbl" style="color:#006b3f; margin-top:2px;">{{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}</div>
    </div>
    @endforeach
</div>

<table class="data">
    <thead>
        <tr>
            <th>Golongan</th>
            <th>Keterangan</th>
            <th>Nominal UKT</th>
            <th>Jumlah Mahasiswa</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @php $totalMhs = $hasil->count(); @endphp
        @foreach([1=>'Sangat Tidak Mampu',2=>'Tidak Mampu',3=>'Menengah ke Bawah',4=>'Menengah',5=>'Mampu'] as $g => $labelG)
        @php $count = $distribusi->get($g, 0); $nom = \App\Models\Pengaturan::nominalUkt($g); @endphp
        <tr>
            <td><strong>{{ $nom['label'] }}</strong></td>
            <td>{{ $labelG }}</td>
            <td>{{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}</td>
            <td>{{ $count }}</td>
            <td>{{ $totalMhs > 0 ? number_format($count/$totalMhs*100, 1) : 0 }}%</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>{{ $totalMhs }}</strong></td>
            <td><strong>100%</strong></td>
        </tr>
    </tbody>
</table>

{{-- ══ BOBOT AHP ══ --}}
<div class="section-title">B. Bobot Prioritas Kriteria (Priority Vector)</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th style="text-align:left">Nama Kriteria</th>
            <th style="width:80px">Bobot</th>
            <th style="width:80px">Persentase</th>
            <th style="width:60px">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php $maxBobot = max($ahpHasil['bobot']); @endphp
        @foreach($kriterias as $i => $k)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="left">
                {{ $k->nama }}
                @if($ahpHasil['bobot'][$i] == $maxBobot) <strong>(★ Tertinggi)</strong> @endif
            </td>
            <td>{{ number_format($ahpHasil['bobot'][$i], 4) }}</td>
            <td>{{ number_format($ahpHasil['bobot'][$i] * 100, 2) }}%</td>
            <td>{{ $ahpHasil['bobot'][$i] == $maxBobot ? 'Prioritas Utama' : '' }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2"><strong>Total</strong></td>
            <td><strong>{{ number_format(array_sum($ahpHasil['bobot']), 4) }}</strong></td>
            <td><strong>100.00%</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- Uji Konsistensi --}}
<table class="doc-info" style="margin-top:8px;">
    <tr>
        <td class="label">λmax</td>
        <td class="value">: {{ number_format($ahpHasil['lambda_max'], 4) }}</td>
        <td class="label">RI (n={{ $ahpHasil['n'] }})</td>
        <td class="value">: {{ number_format($ahpHasil['ri'], 2) }}</td>
    </tr>
    <tr>
        <td class="label">CI</td>
        <td class="value">: {{ number_format($ahpHasil['ci'], 4) }}</td>
        <td class="label">CR = CI/RI</td>
        <td class="value" style="color:{{ $ahpHasil['konsisten'] ? '#006b3f' : '#dc2626' }}">
            : {{ number_format($ahpHasil['cr'], 4) }}
            — {{ $ahpHasil['konsisten'] ? '✓ KONSISTEN (CR ≤ 0.1)' : '✗ TIDAK KONSISTEN' }}
        </td>
    </tr>
</table>

<div class="page-break"></div>

{{-- ══ DAFTAR MAHASISWA ══ --}}
<div class="section-title">C. Daftar Mahasiswa & Hasil Golongan UKT</div>
<table class="data">
    <thead>
        <tr>
            <th style="width:25px">No</th>
            <th style="width:60px">NIM</th>
            <th style="text-align:left">Nama Mahasiswa</th>
            <th style="width:80px">Program Studi</th>
            <th style="width:50px">Skor AHP</th>
            <th style="width:40px">Rank</th>
            <th style="width:55px">Golongan</th>
            <th style="width:70px">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hasil as $h)
        @php $g = $h->golongan_ukt; $nom = \App\Models\Pengaturan::nominalUkt($g); @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td style="font-family:monospace">{{ $h->mahasiswa->nim }}</td>
            <td class="left">{{ $h->mahasiswa->nama }}</td>
            <td class="left" style="font-size:8.5px">{{ $h->mahasiswa->program_studi ?? '-' }}</td>
            <td style="font-family:monospace">{{ number_format($h->skor_total, 4) }}</td>
            <td>#{{ $h->peringkat }}</td>
            <td><span class="badge badge-{{ $g }}">{{ $nom['label'] }}</span></td>
            <td style="font-size:8.5px">{{ \App\Models\Pengaturan::formatRupiah($nom['nominal']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ══ TANDA TANGAN ══ --}}
<div class="ttd-section">
    <div class="ttd-box">
        <div>Mengetahui,</div>
        <div>Ketua Program Studi</div>
        <div class="ttd-space"></div>
        <div class="ttd-nama">( _________________________ )</div>
        <div class="ttd-nip">NIP.</div>
    </div>
    <div class="ttd-box">
        <div>Dosen Pembimbing</div>
        <div>&nbsp;</div>
        <div class="ttd-space"></div>
        <div class="ttd-nama">( _________________________ )</div>
        <div class="ttd-nip">NIP.</div>
    </div>
    <div class="ttd-box">
        <div>Bukittinggi, {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
        <div>Peneliti</div>
        <div class="ttd-space"></div>
        <div class="ttd-nama">( _________________________ )</div>
        <div class="ttd-nip">NIM.</div>
    </div>
</div>

</main>
</body>
</html>