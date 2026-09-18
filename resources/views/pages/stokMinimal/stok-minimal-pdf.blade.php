<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Standar Minimal Stock IT</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        p {
            text-align: center;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 6px;
        }

        th {
            background: #e5e7eb;
        }

        a {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h2>Laporan Standar Minimal Stock IT</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th width="13%">Nama Barang</th>
                <th width="5%">Satuan</th>
                <th width="5%">Jumlah Stock</th>
                <th width="5%">Batas Minimal</th>
                <th width="5%">Selisih</th>
                <th width="13%">Status Alert</th>
                <th width="7%">Rentang Waktu</th>
                <th width="17%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $item->barang?->nama_barang ?? '-' }}</td>
                <td>{{ $item->barang?->satuan ?? '-' }}</td>
                <td>{{ $item->jumlah_stock ?? 0 }}</td>
                <td>{{ $item->minimal ?? 0 }}</td>
                <td>{{ $item->selisih ?? 0 }}</td>
                <td>{{ $item->statusAlert }}</td>
                <td>{{ $item->rentang_waktu ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data stock minimal IT pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>