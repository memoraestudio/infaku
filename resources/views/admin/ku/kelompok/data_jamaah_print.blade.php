<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Jamaah – {{ $info_kelompok->nama_kelompok ?? 'Kelompok' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ========== KOP SURAT ========== */
        .kop-surat {
            width: 210mm;
            margin: 0 auto;
            padding: 15px 0 10px 0;
            border-bottom: 4px solid #0d6048;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10mm;
        }

        .kop-logo img {
            height: 150px;
            width: auto;
        }

        .kop-teks {
            text-align: center;
            flex: 1;
        }

        .kop-teks .title-kop-surat {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: -10px;
            margin-bottom: -5px;
        }

        .kop-teks .nama-kelompok {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0px;
        }

        .kop-teks .alamat {
            font-size: 11pt;
            margin: 0;
        }

        .kop-teks .kontak {
            font-size: 10pt;
            margin: 2px 0 0 0;
        }

        /* ========== HALAMAN ========== */
        @page {
            size: A4;
            margin: 20mm 25mm 20mm 25mm;
        }

        body {
            margin: 0;
            padding: 0;
            font: 12pt/1.4 'Times New Roman', Times, serif;
            color: #222;
            background: #fff;
        }

        .sheet {
            width: 200mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin: 25px 0 18px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 17pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 4px 0;
            font-size: 13pt;
            font-weight: normal;
        }

        .info-section {
            display: table;
            width: 100%;
            margin: 15px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-label,
        .info-value {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            width: 180px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10pt;
        }

        table.data th {
            background: #0d6048;
            color: #fff;
            padding: 7px 5px;
            border: 1px solid #aaa;
        }

        table.data td {
            padding: 6px 5px;
            border: 1px solid #aaa;
            vertical-align: top;
        }

        table.data tr:nth-child(even) {
            background: #f7f7f7;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            padding: 25px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .footer-left {
            font-size: 10pt;
            color: #555;
        }

        .footer-right {
            text-align: center;
            width: 200px;
            font-size: 11pt;
        }

        .footer-right .blank {
            height: 60px;
        }

        .footer-right .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        @media print {
            .no-print {
                display: none;
            }

            table.data {
                page-break-inside: auto;
            }

            table.data tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>

    <!-- ========== KOP SURAT ========== -->
    <div class="kop-surat">
        <div class="kop-inner">
            <div class="kop-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <div class="kop-teks">
                <p class="nama-kelompok">
                    DATA JAMA'AH KELOMPOK {{ strtoupper($info_kelompok->nama_kelompok ?? '-') }}
                </p>

                <p class="title-kop-surat">LEMBAGA DAKWAH ISLAM INDONESIA</p>
                <p class="title-kop-surat" style="color: #06aa66">( L D I I )</p>
                <p class="alamat"> {{ $info_kelompok->alamat_masjid ?? '-' }}</p>
                <p class="kontak">Telp. (....) ....... – Email: ...........@....</p>
            </div>
        </div>
    </div>

    <!-- ========== ISI LAPORAN ========== -->
    <div class="sheet">
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Nama Kelompok</div>
                <div class="info-value">: {{ $info_kelompok->nama_kelompok ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Jama'ah</div>
                <div class="info-value">: {{ $jamaahs->count() }} Jama'ah</div>
            </div>
        </div>

        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    {{-- <th>ID Jamaah</th> --}}
                    <th>Nama Lengkap</th>
                    <th>L/P</th>
                    <th>TTL</th>
                    {{-- <th>Telepon</th> --}}
                    <th>Pekerjaan</th>
                    <th>Status</th>
                    <th>Dapuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jamaahs as $index => $jamaah)
                    <tr style="white-space: nowrap">
                        <td align="center">{{ $index + 1 }}</td>
                        {{-- <td>{{ $jamaah->jamaah_id }}</td> --}}
                        <td>{{ $jamaah->nama_lengkap }}</td>
                        <td align="center" class="text-uppercase">{{ $jamaah->jenis_kelamin }}</td>
                        <td style="white-space: nowrap">
                            @if ($jamaah->tempat_lahir || $jamaah->tanggal_lahir)
                                {{ $jamaah->tempat_lahir ?? '' }}{{ $jamaah->tempat_lahir && $jamaah->tanggal_lahir ? ', ' : '' }}
                                @if ($jamaah->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($jamaah->tanggal_lahir)->isoFormat('D MMM Y') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        {{-- <td>{{ $jamaah->telepon ?? '-' }}</td> --}}
                        <td>{{ $jamaah->pekerjaan ?? '-' }}</td>
                        <td class="text-center">
                            @switch($jamaah->status_menikah)
                                @case('BELUM_MENIKAH')
                                    Belum
                                @break

                                @case('MENIKAH')
                                    Menikah
                                @break

                                @case('JANDA')
                                    Janda
                                @break

                                @case('DUDA')
                                    Duda
                                @break

                                @default
                                    {{ $jamaah->status_menikah }}
                            @endswitch
                        </td>
                        <td>{{ $jamaah->nama_dapuan ?? '-' }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="no-data">Tidak ada data jama'ah</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="footer">
                <div class="footer-left">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="padding-right:6px;">Dicetak pada</td>
                            <td style="padding-right:6px;">:</td>
                            <td>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm') }}</td>
                        </tr>
                        <tr>
                            <td>Dicetak oleh</td>
                            <td>:</td>
                            <td>{{ $user['nama_lengkap'] ?? 'Admin' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="footer-right">
                    <p style="margin: 0px">Mengetahui</p>
                    <div class="blank"></div>
                    <p class="name">( ............................. )</p>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('load', () => setTimeout(() => window.print(), 250));
        </script>
    </body>

    </html>
