<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #105a44;
        }

        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-item {
            flex: 1;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .info-value {
            margin-top: 5px;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }

        .summary-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 11px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN RIWAYAT TRANSAKSI</h1>
        <h2>{{ $info_kelompok->nama_kelompok }}</h2>
        <div style="margin-top: 10px;">
            Periode:
            @if ($filters['start_date'])
                {{ date('d M Y', strtotime($filters['start_date'])) }}
            @else
                Semua Waktu
            @endif
            -
            @if ($filters['end_date'])
                {{ date('d M Y', strtotime($filters['end_date'])) }}
            @else
                {{ date('d M Y') }}
            @endif
        </div>
    </div>

    <div class="info-section">
        <div class="info-item">
            <div class="info-label">Status Filter:</div>
            <div class="info-value">
                {{ $filters['status'] == 'ALL' ? 'Semua Status' : $filters['status'] }}
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ date('d F Y H:i:s') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Dicetak Oleh:</div>
            <div class="info-value">{{ $user['nama'] }}</div>
        </div>
    </div>

    <div class="summary-section">
        <div class="summary-card">
            <div class="summary-title">TOTAL TRANSAKSI</div>
            <div class="summary-value">{{ $summary['total_transaksi'] }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">TOTAL PENDAPATAN</div>
            <div class="summary-value">Rp {{ number_format($summary['total_pendapatan'], 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">TERVERIFIKASI</div>
            <div class="summary-value">Rp {{ number_format($summary['total_verified'], 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">PENDING</div>
            <div class="summary-value">Rp {{ number_format($summary['total_pending'], 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Jamaah</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Metode Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $index => $transaksi)
                @php
                    $dataJson = json_decode($transaksi->data_json, true);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaksi->kode_transaksi }}</td>
                    <td>{{ date('d M Y', strtotime($transaksi->tgl_transaksi)) }}</td>
                    <td>{{ $transaksi->nama_jamaah }}</td>
                    <td>{{ $dataJson['nama_kontribusi'] ?? $transaksi->kategori_id }}</td>
                    <td class="text-right">Rp {{ number_format($transaksi->jumlah, 2) }}</td>
                    <td>{{ $transaksi->metode_bayar }}</td>
                    <td>
                        @if ($transaksi->status == 'VERIFIED')
                            <span class="badge badge-success">VERIFIED</span>
                        @elseif($transaksi->status == 'PENDING')
                            <span class="badge badge-warning">PENDING</span>
                        @else
                            <span class="badge badge-danger">REJECTED</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak dari Sistem Keuangan Kelompok</div>
        <div>{{ $info_kelompok->nama_kelompok }} - {{ date('d F Y H:i:s') }}</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
