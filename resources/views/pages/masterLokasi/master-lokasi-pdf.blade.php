<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Master Lokasi</title>
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
    <h2>Laporan Master Lokasi</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th width="13%">Nama Lokasi</th>
                <th width="8%">Kode</th>
                <th width="10%">Tipe Bangunan</th>

            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $item->nama ?? '-' }}</td>
                <td>{{ $item->kode ?? '-' }}</td>
                <td>{{ $item->tipe === 'Lokasi' ? 'Divisi' : $item->tipe ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">Tidak ada data master lokasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>