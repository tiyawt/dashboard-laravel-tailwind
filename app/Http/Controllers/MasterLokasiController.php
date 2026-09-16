<?php

namespace App\Http\Controllers;

use App\Models\AsetInventaris;
use App\Models\MasterLokasi;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterLokasiController extends Controller
{
    public function index()
    {
        $this->authorizeManagement();

        return view('pages.masterLokasi.index', [
            'locations' => MasterLokasi::orderBy('tipe')->orderBy('nama')->get(),
        ]);
    }

    public function exportXlsx()
    {
        $this->authorizeManagement();

        $locations = MasterLokasi::orderBy('tipe')->orderBy('nama')->get();
        $columns = ['Nama Lokasi', 'Kode', 'Tipe'];
        $rows = $locations->map(fn(MasterLokasi $location) => [
            $location->nama,
            $location->kode,
            $location->tipe === 'Lokasi' ? 'Divisi' : $location->tipe,
        ])->all();

        return SimpleXlsxExporter::download(
            $columns,
            $rows,
            'master_lokasi_' . date('Y-m-d') . '.xlsx',
            'Master Lokasi'
        );
    }

    public function create()
    {
        $this->authorizeManagement();

        return view('pages.masterLokasi.form', [
            'location' => new MasterLokasi(),
        ]);
    }

    public function edit($id)
    {
        $this->authorizeManagement();

        return view('pages.masterLokasi.form', [
            'location' => MasterLokasi::findOrFail($id),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();

        MasterLokasi::create($this->validated($request));

        return back()->with('success', 'Master lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeManagement();

        $location = MasterLokasi::findOrFail($id);
        $validated = $this->validated($request, $id);
        $oldCode = $location->kode;

        DB::transaction(function () use ($location, $validated, $oldCode) {
            if ($oldCode !== $validated['kode']) {
                $this->updateRelatedAssetInventoryNumbers(
                    $location,
                    $oldCode,
                    $validated['kode']
                );
            }

            $location->update($validated);
        });

        return redirect()
            ->route('master-lokasi.index')
            ->with('success', 'Master lokasi berhasil diperbarui.');
    }

    private function updateRelatedAssetInventoryNumbers(
        MasterLokasi $location,
        string $oldCode,
        string $newCode
    ): void {
        [$foreignKey, $segments] = match ($location->tipe) {
            'Gedung' => ['gedung_id', [1]],
            'Lantai' => ['lantai_id', [2]],
            'Lokasi' => ['lokasi_id', [3, 4]],
        };

        AsetInventaris::where($foreignKey, $location->id)
            ->get()
            ->each(function (AsetInventaris $asset) use ($oldCode, $newCode, $segments) {
                $parts = explode('/', $asset->no_inventaris);
                $changed = false;

                foreach ($segments as $segment) {
                    if (($parts[$segment] ?? null) !== $oldCode) {
                        continue;
                    }

                    $parts[$segment] = $newCode;
                    $changed = true;
                }

                if ($changed) {
                    $asset->update([
                        'no_inventaris' => implode('/', $parts),
                    ]);
                }
            });
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $request->merge([
            'tipe' => $request->input('tipe') === 'Divisi' ? 'Lokasi' : $request->input('tipe'),
        ]);

        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:master_lokasi,kode,' . ($id ?? 'NULL') . ',id,tipe,' . $request->input('tipe')],
            'tipe' => ['required', 'in:Gedung,Lantai,Lokasi'],
        ]);
    }

    private function authorizeManagement(): void
    {
        $user = Auth::user();
        abort_unless(
            $user &&
                in_array(
                    $user->role,
                    [
                        'admin',
                        'superadmin'
                    ],
                    true
                ),
            403
        );
    }
}
