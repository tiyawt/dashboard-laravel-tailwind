<?php

namespace App\Http\Controllers;

use App\Models\AsetInventaris;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AsetInventarisController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $aset = AsetInventaris::with('maintenanceLogs')
            ->when($search, function ($query) use ($search) {
                $needle = '%' . Str::lower($search) . '%';
                $query->where(function ($query) use ($needle) {
                    $query->whereRaw('LOWER(no_inventaris) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(nama_barang) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(spesifikasi) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(lantai) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(lokasi) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(nama_user) LIKE ?', [$needle]);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.asetInventaris.index', compact('aset'));
    }

    public function scan(Request $request)
    {
        $code = trim((string) $request->input('code'));

        abort_if($code === '', 422, 'Kode barcode wajib diisi.');

        $aset = AsetInventaris::with('maintenanceLogs')
            ->whereRaw('LOWER(no_inventaris) = ?', [Str::lower($code)])
            ->first();

        return response()->json([
            'found' => (bool) $aset,
            'asset' => $aset,
        ], $aset ? 200 : 404);
    }

    public function create()
    {
        return view('pages.asetInventaris.form', ['aset' => new AsetInventaris()]);
    }

    public function store(Request $request)
    {
        AsetInventaris::create($this->validatedAsset($request));

        return redirect()->route('aset-inventaris.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show($id)
    {
        $aset = AsetInventaris::with('maintenanceLogs')->findOrFail($id);

        return view('pages.asetInventaris.show', compact('aset'));
    }

    public function edit($id)
    {
        return view('pages.asetInventaris.form', ['aset' => AsetInventaris::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        AsetInventaris::findOrFail($id)->update($this->validatedAsset($request));

        return redirect()->route('aset-inventaris.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function storeMaintenance(Request $request, $id)
    {
        $aset = AsetInventaris::findOrFail($id);
        $aset->maintenanceLogs()->create($request->validate([
            'tanggal' => ['required', 'date'],
            'pelapor' => ['required', 'string', 'max:255'],
            'gejala_masalah' => ['required', 'string'],
            'penyebab' => ['nullable', 'string'],
            'tindakan_penanganan' => ['nullable', 'string'],
            'teknisi' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ]));

        return redirect()->route('aset-inventaris.show', $aset->id)->with('success', 'Catatan maintenance berhasil ditambahkan.');
    }

    public function updateMaintenanceStatus(Request $request, $id)
    {
        $maintenanceLog = MaintenanceLog::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', 'in:Open,Dalam Penanganan,Selesai'],
        ]);

        $maintenanceLog->update($validated);

        return redirect()->route('aset-inventaris.show', $maintenanceLog->aset_inventaris_id)
            ->with('success', 'Status maintenance berhasil diperbarui.');
    }

    private function validatedAsset(Request $request): array
    {
        $validated = $request->validate([
            'no_inventaris' => ['required', 'string', 'max:255'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'spesifikasi' => ['nullable', 'string'],
            'lantai' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'user' => ['nullable', 'string', 'max:255'],
            'kelengkapan' => ['nullable', 'string'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal_entry' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['nama_user'] = $validated['user'] ?? null;
        unset($validated['user']);

        return $validated;
    }
}
