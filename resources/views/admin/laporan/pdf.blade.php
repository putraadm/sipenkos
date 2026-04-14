<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Penghuni</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .stats {
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .stats-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            text-align: center;
        }
        .stats-item .label {
            font-size: 10px;
            color: #666;
            display: block;
        }
        .stats-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th {
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #2563eb;
        }
        table.data td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        table.data tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-masuk {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-keluar {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Mutasi Penghuni</h1>
        <p>Wilayah Pendataan Sipenkos</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 50%">
                    <strong>Tanggal Cetak:</strong> {{ date('d F Y') }}<br>
                    <strong>Periode:</strong> {{ $filters['month'] ?? 'Semua' }} / {{ $filters['year'] ?? 'Semua' }}
                </td>
                <td style="width: 50%; text-align: right">
                     <strong>Status:</strong> Resmi
                </td>
            </tr>
        </table>
    </div>

    <div class="stats">
        <div class="stats-item">
            <span class="label">Total Penghuni Aktif</span>
            <span class="value">{{ $stats['total_penghuni_aktif'] }}</span>
        </div>
        <div class="stats-item">
            <span class="label">Masuk ({{ $stats['period_label'] }})</span>
            <span class="value">{{ $stats['masuk_count'] }}</span>
        </div>
        <div class="stats-item">
            <span class="label">Keluar ({{ $stats['period_label'] }})</span>
            <span class="value">{{ $stats['keluar_count'] }}</span>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Tanggal</th>
                <th>Penghuni</th>
                <th>NIK</th>
                <th>No. WA</th>
                <th>Kos</th>
                <th>Pemilik</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tanggal_mutasi }}</td>
                <td><strong>{{ $item->penghuni?->nama ?? '-' }}</strong></td>
                <td><span style="font-family: monospace">{{ $item->penghuni?->nik ?? '-' }}</span></td>
                <td>{{ $item->penghuni?->no_wa ?? '-' }}</td>
                <td><strong>{{ $item->kos?->nama_kos ?? '-' }}</strong></td>
                <td>{{ $item->kos?->nama_pemilik ?? '-' }}</td>
                <td style="text-align: center">
                    <span class="badge badge-{{ $item->jenis_mutasi }}">
                        {{ $item->jenis_mutasi }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Pendataan Kos (Sipenkos) pada {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
