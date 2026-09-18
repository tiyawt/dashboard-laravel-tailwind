<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Daftar Penerimaan Barang</title>
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
    <h2>Laporan Daftar Penerimaan Barang</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th width="13%">Nama Barang</th>
                <th width="13%">Tgl Pengajuan</th>
                <th width="8%">Status Barang</th>
                <th width="5%">Divisi Permintaan</th>
                <th width="5%">Total Pengajuan</th>
                <th width="13%">Tgl Pengambilan</th>
                <th width="5%">Jumlah Diterima</th>
                <th width="13%">Penerima</th>
                <th width="17%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $item->pengajuan?->barang?->nama_barang ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->pengajuan?->tanggal_pengajuan)->format('d/m/Y') }}</td>
                <td>{{ $item->pengajuan?->status_barang ?? '-' }}</td>
                <td>{{ $item->pengajuan?->permintaan ?? '-' }}</td>
                <td>{{ $item->pengajuan?->volume ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d/m/Y') }}</td>
                <td>{{ $item->jumlah_diterima ?? 0 }}</td>
                <td>{{ $item->penerima ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center;">Tidak ada data penerimaan barang pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>