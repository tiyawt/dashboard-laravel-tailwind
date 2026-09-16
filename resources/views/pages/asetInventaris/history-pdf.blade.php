<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Aset - {{ $aset->no_inventaris }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
        }

        h1 {
            font-size: 17pt;
            margin: 0;
        }

        h2 {
            border-bottom: 1px solid #9ca3af;
            font-size: 12pt;
            margin: 22px 0 8px;
            padding-bottom: 4px;
        }

        p {
            margin: 2px 0;
        }

        .header {
            border-bottom: 2px solid #111827;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        .muted {
            color: #4b5563;
        }

        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 24px;
            margin-bottom: 16px;
        }

        .summary strong {
            display: inline-block;
            min-width: 125px;
        }

        table {
            border-collapse: collapse;
            margin-top: 6px;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #9ca3af;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #e5e7eb;
            font-weight: 700;
        }

        tr {
            break-inside: avoid;
        }

        .empty {
            color: #6b7280;
            font-style: italic;
        }

        .footer {
            color: #6b7280;
            font-size: 8pt;
            margin-top: 18px;
        }

        .actions {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            margin-bottom: 16px;
            padding: 10px;
        }

        button {
            background: #2563eb;
            border: 0;
            color: #fff;
            cursor: pointer;
            padding: 8px 12px;
        }

        @media print {
            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </div>

    <header class="header">
        <h1>Laporan Histori Aset & Inventaris</h1>
        <p class="muted">Dicetak pada {{ now()->translatedFormat('j F Y H:i') }}</p>
    </header>

    <section class="summary">
        <p><strong>No. Inventaris</strong> {{ $aset->no_inventaris }}</p>
        <p><strong>Nama Barang</strong> {{ $aset->nama_barang }}</p>
        <p><strong>Spesifikasi</strong> {{ $aset->spesifikasi ?: '-' }}</p>
        <p><strong>User</strong> {{ $aset->user ?: '-' }}</p>
        <p><strong>Lantai</strong> {{ $aset->lantai ?: '-' }}</p>
        <p><strong>Lokasi</strong> {{ $aset->lokasi ?: '-' }}</p>
        <p><strong>Status</strong> {{ $aset->status }}</p>
    </section>

    <h2>Histori Aset ({{ $aset->histories->count() }})</h2>

    @if($aset->histories->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 25%;">Jenis</th>
                <th>Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @foreach($aset->histories as $history)
            <tr>
                <td>{{ $history->tanggal?->translatedFormat('j F Y') ?: '-' }}</td>
                <td>{{ $history->jenis }}</td>
                <td>{{ $history->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Belum ada histori aset.</p>
    @endif
    


    <h2>Histori Maintenance ({{ $aset->maintenanceLogs->count() }})</h2>
    @if($aset->maintenanceLogs->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 15%;">Pelapor</th>
                <th>Gejala / Masalah</th>
                <th>Penyebab</th>
                <th>Tindakan</th>
                <th style="width: 15%;">Teknisi</th>
                <th style="width: 13%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aset->maintenanceLogs as $log)
            <tr>
                <td>{{ $log->tanggal?->translatedFormat('j F Y') ?: '-' }}</td>
                <td>{{ $log->pelapor ?: '-' }}</td>
                <td>{{ $log->gejala_masalah ?: '-' }}</td>
                <td>{{ $log->penyebab ?: '-' }}</td>
                <td>{{ $log->tindakan_penanganan ?: '-' }}</td>
                <td>{{ $log->teknisi ?: '-' }}</td>
                <td>{{ $log->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">Belum ada catatan maintenance.</p>
    @endif

    <p class="footer">Dokumen ini berisi seluruh histori yang tersimpan untuk aset {{ $aset->no_inventaris }}.</p>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>

</html>