<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Barang Keluar</title>
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
    <h2>Laporan Barang Keluar</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Pelapor</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') }}</td>
                <td>{{ $item->barang?->nama_barang ?? '-' }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->barang?->satuan ?? '-' }}</td>
                <td>{{ $item->pelapor }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ strtoupper($item->status) }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
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