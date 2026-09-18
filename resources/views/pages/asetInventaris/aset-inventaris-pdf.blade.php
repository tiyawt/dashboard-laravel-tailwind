<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Aset & Inventaris</title>
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
    </style>
</head>

<body>
    <h2>Laporan Aset & Inventaris</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th>No. Inventaris</th>
                <th>Nama Barang</th>
                <th>Spesifikasi</th>
                <th>Lantai</th>
                <th>Lokasi</th>
                <th>User</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $item->no_inventaris }}</td>
                <td>{{ $item->nama_barang ?? '-' }}</td>
                <td>{{ $item->spesifikasi }}</td>
                <td>{{ $item->lantai }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->nama_user ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td>{{ $item->status ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data barang keluar pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>