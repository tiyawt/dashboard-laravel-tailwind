<?php

namespace App\Http\Controllers;

use App\Models\AsetInventaris;
use App\Models\MasterLokasi;
use App\Services\SimpleXlsxExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterLokasiController extends Controller
{
    public function index()
    {
        $this->authorizeManagement();

        return view('pages.masterLokasi.index', [
            'locations' => MasterLokasi::orderByRaw("
            CASE tipe
                WHEN 'Gedung' THEN 1
                WHEN 'Lantai' THEN 2
                WHEN 'Lokasi' THEN 3
            END
        ")
                ->orderBy('nama')
                ->paginate(10),
        ]);
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

        return redirect()
            ->route('master-lokasi.index')
            ->with('success', 'Master lokasi berhasil ditambahkan.');
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

    public function export(Request $request)
    {
        $this->authorizeManagement();

        $format = $request->input('format', 'pdf');
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $data = MasterLokasi::orderByRaw("
        CASE tipe
            WHEN 'Gedung' THEN 1
            WHEN 'Lantai' THEN 2
            WHEN 'Lokasi' THEN 3
        END
    ")
            ->orderBy('nama')
            ->get();

        // =========================
        // PDF
        // =========================
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pages.masterLokasi.master-lokasi-pdf', [
                'data' => $data,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ])->setPaper('a4', 'portrait');

            return $pdf->download(
                'master_lokasi_' . date('Y-m-d') . '.pdf'
            );
        }

        // =========================
        // EXCEL
        // =========================
        if (in_array($format, ['excel', 'xlsx'], true)) {

            $columns = [
                'Nama Lokasi',
                'Kode',
                'Tipe',
            ];

            $rows = $data->map(fn(MasterLokasi $item) => [
                $item->nama,
                $item->kode,
                $item->tipe === 'Lokasi'
                    ? 'Divisi'
                    : $item->tipe,
            ])->all();

            return SimpleXlsxExporter::download(
                $columns,
                $rows,
                'master_lokasi_' . date('Y-m-d') . '.xlsx',
                'Master Lokasi'
            );
        }
    }
}
