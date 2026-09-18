<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Daftar Belanja</title>
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
    <h2>Laporan Daftar Belanja</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th width="13%">Nama Barang</th>
                <th width="13%">Tanggal Pengajuan</th>
                <th width="8%">Divisi Permintaan</th>
                <th width="5%">Volume Dibutuhkan</th>
                <th width="5%">Total Diterima</th>
                <th width="5%">Selisih Kekurangan</th>
                <th width="9%">Harga/Unit</th>
                <th width="13%">Perkiraan Biaya</th>
                <th width="17%">Status Pemenuhan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $item->barang?->nama_barang ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                <td>{{ $item->permintaan }}</td>
                <td>{{ $item->volume }}</td>
                <td>{{ $item->total_diterima }}</td>
                <td>{{ $item->selisih }}</td>
                <td>Rp {{ number_format($item->harga_per_unit, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->biayaKurang ?? '0', 0, ',', '.') }}</td>
                <td>{{ $item->statusPemenuhan ?? '-' }}</td>
            </tr> 
            @empty
            <tr>
                <td colspan="11" style="text-align: center;">Tidak ada data daftar belanja pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>