<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pengajuan Barang</title>
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
    <h2>Laporan Pengajuan Barang</h2>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>
    <table>
        <thead>
            <tr>
                <th width="8%">Tanggal</th>
                <th width="13%">Nama Barang</th>
                <th width="5%">Volume</th>
                <th width="7%">Satuan</th>
                <th width="9%">Harga/Unit</th>
                <th width="13%">Link SPB & Invoice</th>
                <th width="8%">Permintaan</th>
                <th width="10%">Status Barang</th>
                <th width="10%">Status Disposisi</th>
                <th width="17%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                <td>{{ $item->barang?->nama_barang ?? '-' }}</td>
                <td>{{ $item->volume }}</td>
                <td>{{ $item->barang?->satuan ?? '-' }}</td>
                <td>Rp {{ number_format($item->harga_per_unit, 0, ',', '.') }}</td>
                <td>
                    @if ($item->link_spb_invoice)
                    <a href="{{ $item->link_spb_invoice }}">Lihat Dokumen</a>
                    @else
                    -
                    @endif
                </td>
                <td>{{ strtoupper($item->permintaan) }}</td>
                <td>{{ $item->status_barang ?? '-' }}</td>
                <td>{{ $item->status_disposisi ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center;">Tidak ada data pengajuan barang pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>