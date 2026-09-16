<?php

namespace App\Http\Controllers;

use App\Models\AsetInventaris;
use App\Models\MasterLokasi;
use App\Models\MaintenanceLog;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AsetInventarisController extends Controller
{


    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $aset = AsetInventaris::with([
            'maintenanceLogs',
            'gedung',
            'lantaiMaster',
            'lokasiMaster',
        ])
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
            ->latest('updated_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.asetInventaris.index', compact('aset'));
    }

    public function scan(Request $request)
    {
        $code = trim((string) $request->input('code'));

        abort_if(
            $code === '',
            422,
            'Kode barcode wajib diisi.'
        );

        $aset = AsetInventaris::with('maintenanceLogs')
            ->whereRaw(
                'LOWER(no_inventaris) = ?',
                [Str::lower($code)]
            )
            ->first();

        return response()->json([
            'found' => (bool) $aset,
            'asset' => $aset,
        ], $aset ? 200 : 404);
    }

    public function exportXlsx()
    {
        $this->authorizeManagement();

        $assets = AsetInventaris::with([
            'gedung',
            'lantaiMaster',
            'lokasiMaster',
        ])->latest('updated_at')->latest('id')->get();

        $columns = [
            'No. Inventaris',
            'Tahun Perolehan',
            'Nama Barang',
            'Spesifikasi',
            'Gedung',
            'Lantai',
            'Divisi',
            'User',
            'Jumlah',
            'Tanggal Entry',
            'Status',
            'Tanggal Nonaktif',
            'Alasan Nonaktif',
            'Kelengkapan',
            'Keterangan',
        ];

        $rows = $assets->map(fn(AsetInventaris $asset) => [
            $asset->no_inventaris,
            $asset->tahun_perolehan ?? '-',
            $asset->nama_barang,
            $asset->spesifikasi ?? '-',
            $asset->gedung?->nama ?? '-',
            $asset->lantaiMaster?->nama ?? $asset->lantai ?? '-',
            $asset->lokasiMaster?->nama ?? $asset->lokasi ?? '-',
            $asset->user ?? '-',
            $asset->jumlah,
            $asset->tanggal_entry?->format('Y-m-d') ?? '-',
            $asset->status ?? '-',
            $asset->tanggal_nonaktif?->format('Y-m-d') ?? '-',
            $asset->alasan_nonaktif ?? '-',
            $asset->kelengkapan ?? '-',
            $asset->keterangan ?? '-',
        ])->all();

        return SimpleXlsxExporter::download(
            $columns,
            $rows,
            'aset_inventaris_' . date('Y-m-d') . '.xlsx',
            'Aset Inventaris'
        );
    }

    public function create()
    {
        $this->authorizeManagement();

        return view('pages.asetInventaris.form', [
            'aset' => new AsetInventaris(),
            'locations' => $this->locationsByType(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();

        $validated = $this->validatedAsset($request, true);

        DB::transaction(function () use ($validated) {
            $locations = $this->validatedLocations($validated);

            $nextNumber = $this->nextInventoryNumber(
                $validated['tahun_perolehan'],
                $locations
            );

            $asetData = $this->assetDataWithLocationNames(
                $validated,
                $locations
            );

            $asetData['no_inventaris'] = $this->inventoryNumber(
                $validated['tahun_perolehan'],
                $locations,
                $nextNumber
            );

            $asetData['status'] = 'Aktif';
            $asetData['tanggal_nonaktif'] = null;
            $asetData['alasan_nonaktif'] = null;

            $aset = AsetInventaris::create($asetData);

            $aset->histories()->create([
                'jenis' => 'Dibuat',
                'keterangan' => 'Aset dibuat dan diberi nomor inventaris.',
                'tanggal' => $validated['tanggal_entry'],
            ]);
        });

        return redirect()
            ->route('aset-inventaris.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show($id)
    {
        $aset = AsetInventaris::with([
            'maintenanceLogs',
            'gedung',
            'lantaiMaster',
            'lokasiMaster',
        ])->findOrFail($id);

        $histories = $aset->histories()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        return view(
            'pages.asetInventaris.show',
            compact('aset', 'histories')
        );
    }

    public function historyPdf($id)
    {
        $aset = AsetInventaris::with([
            'maintenanceLogs',
            'histories',
        ])->findOrFail($id);

        return view('pages.asetInventaris.history-pdf', compact('aset'));
    }

    public function edit($id)
    {
        $this->authorizeManagement();

        return view('pages.asetInventaris.form', [
            'aset' => AsetInventaris::with([
                'gedung',
                'lantaiMaster',
                'lokasiMaster',
            ])->findOrFail($id),

            'locations' => $this->locationsByType(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeManagement();

        $aset = AsetInventaris::with([
            'gedung',
            'lantaiMaster',
            'lokasiMaster',
        ])->findOrFail($id);

        $validated = $this->validatedAsset($request);

        $locations = $this->validatedLocations($validated);

        $oldGroup = implode('/', [
            $aset->tahun_perolehan,
            $aset->gedung_id,
            $aset->lantai_id,
            $aset->lokasi_id,
        ]);

        /*
     * Kombinasi baru.
     */
        $newGroup = implode('/', [
            $validated['tahun_perolehan'],
            $locations['gedung']->id,
            $locations['lantai']->id,
            $locations['lokasi']->id,
        ]);

        $groupChanged = $oldGroup !== $newGroup;

        $oldInventoryNumber = $aset->no_inventaris;

        $oldGedungName = $aset->gedung?->nama ?? '-';
        $oldLantaiName = $aset->lantaiMaster?->nama ?? $aset->lantai ?? '-';
        $oldLokasiName = $aset->lokasiMaster?->nama ?? $aset->lokasi ?? '-';
        $historyFields = [
            'nama_barang',
            'spesifikasi',
            'nama_user',
            'jumlah',
            'tanggal_entry',
            'kelengkapan',
            'keterangan',
            'tanggal_nonaktif',
            'alasan_nonaktif',
        ];
        $oldHistoryValues = collect($historyFields)
            ->mapWithKeys(fn(string $field) => [$field => $aset->getRawOriginal($field)])
            ->all();

        DB::transaction(function () use (
            $aset,
            $validated,
            $locations,
            $groupChanged,
            $oldInventoryNumber,
            $oldGedungName,
            $oldLantaiName,
            $oldLokasiName,
            $oldHistoryValues
        ) {
            $assetData = $this->assetDataWithLocationNames(
                $validated,
                $locations
            );

            if ($groupChanged) {
                $nextNumber = $this->nextInventoryNumber(
                    $validated['tahun_perolehan'],
                    $locations,
                    $aset->id
                );

                $assetData['no_inventaris'] = $this->inventoryNumber(
                    $validated['tahun_perolehan'],
                    $locations,
                    $nextNumber
                );
            }

            /*
         * Kalau group tidak berubah,
         * no_inventaris tidak disentuh.
         */
            $aset->update($assetData);

            $changedFields = [
                'nama_barang' => 'Nama barang',
                'spesifikasi' => 'Spesifikasi',
                'nama_user' => 'User',
                'jumlah' => 'Jumlah',
                'tanggal_entry' => 'Tanggal entry',
                'kelengkapan' => 'Kelengkapan',
                'keterangan' => 'Keterangan',
                'tanggal_nonaktif' => 'Tanggal nonaktif',
                'alasan_nonaktif' => 'Alasan nonaktif',
            ];

            $changedDescriptions = collect($changedFields)
                ->filter(fn(string $label, string $field) => $aset->wasChanged($field))
                ->map(function (string $label, string $field) use ($aset, $oldHistoryValues): string {
                    $oldValue = $oldHistoryValues[$field] ?? null;
                    $newValue = $aset->getAttributes()[$field] ?? null;

                    return sprintf(
                        '%s berubah dari "%s" menjadi "%s".',
                        $label,
                        $oldValue ?: '-',
                        $newValue ?: '-'
                    );
                })
                ->values();

            if ($changedDescriptions->isNotEmpty()) {
                $aset->histories()->create([
                    'jenis' => 'Perubahan Data Aset',
                    'keterangan' => $changedDescriptions->implode(' '),
                    'tanggal' => now()->toDateString(),
                ]);
            }

            /*
         * Catat perubahan nomor/lokasi.
         */
            if ($groupChanged) {
                $aset->histories()->create([
                    'jenis' => 'Perubahan Nomor Inventaris',

                    'keterangan' => sprintf(
                        'No. Inventaris berubah dari %s menjadi %s. Lokasi sebelumnya: %s, %s, %s. Lokasi baru: %s, %s, %s.',
                        $oldInventoryNumber,
                        $aset->no_inventaris,
                        $oldGedungName,
                        $oldLantaiName,
                        $oldLokasiName,
                        $locations['gedung']->nama,
                        $locations['lantai']->nama,
                        $locations['lokasi']->nama
                    ),

                    'tanggal' => now()->toDateString(),
                ]);
            }

            /*
         * Catat perubahan status.
         */
            if ($aset->wasChanged('status')) {
                $aset->histories()->create([
                    'jenis' => 'Perubahan Status',

                    'keterangan' => $aset->status === 'Nonaktif'
                        ? 'Status aset berubah menjadi Nonaktif. Alasan: ' . ($aset->alasan_nonaktif ?: '-')
                        : 'Status aset berubah menjadi Aktif.',

                    'tanggal' => $aset->status === 'Nonaktif'
                        ? $aset->tanggal_nonaktif
                        : now()->toDateString(),
                ]);
            }
        });

        return redirect()
            ->route('aset-inventaris.index')
            ->with(
                'success',
                'Data aset berhasil diperbarui.'
            );
    }

    private function nextInventoryNumber(
        int $year,
        array $locations,
        ?int $currentAssetId = null
    ): int {
        $sequenceLock = DB::table(
            'aset_inventory_sequence_locks'
        )
            ->where('id', 1)
            ->lockForUpdate()
            ->first();

        abort_unless(
            $sequenceLock,
            500,
            'Kunci nomor inventaris belum tersedia.'
        );

        $prefix = implode('/', [
            $year,
            $locations['gedung']->kode,
            $locations['lantai']->kode,
            $locations['lokasi']->kode,
            $locations['lokasi']->kode,
        ]) . '/';


        $usedNumbers = AsetInventaris::query()
            ->where('no_inventaris', 'like', $prefix . '%')
            ->where(function ($query) use ($currentAssetId) {

                if ($currentAssetId !== null) {
                    $query->where('id', '!=', $currentAssetId);
                }
            })
            ->pluck('no_inventaris')
            ->filter(function ($inventoryNumber) use ($prefix) {
                return is_string($inventoryNumber)
                    && str_starts_with(
                        $inventoryNumber,
                        $prefix
                    );
            })
            ->map(function ($inventoryNumber) {
                return (int) Str::afterLast(
                    $inventoryNumber,
                    '/'
                );
            })
            ->filter(fn($number) => $number > 0)
            ->unique()
            ->sort()
            ->values();


        $nextNumber = 1;

        foreach ($usedNumbers as $usedNumber) {
            if ($usedNumber === $nextNumber) {
                $nextNumber++;
                continue;
            }

            if ($usedNumber > $nextNumber) {
                break;
            }
        }

        $sequence = DB::table('aset_inventory_sequences')
            ->where('tahun', $year)
            ->where(
                'gedung_id',
                $locations['gedung']->id
            )
            ->where(
                'lantai_id',
                $locations['lantai']->id
            )
            ->where(
                'lokasi_id',
                $locations['lokasi']->id
            )
            ->first();

        $highestUsedNumber = $usedNumbers->max() ?? 0;

        $sequenceData = [
            'tahun' => $year,
            'gedung_id' => $locations['gedung']->id,
            'lantai_id' => $locations['lantai']->id,
            'lokasi_id' => $locations['lokasi']->id,
            'last_number' => $highestUsedNumber,
            'updated_at' => now(),
        ];

        if ($sequence) {
            DB::table('aset_inventory_sequences')
                ->where('id', $sequence->id)
                ->update($sequenceData);
        } else {
            DB::table('aset_inventory_sequences')
                ->insert(
                    array_merge(
                        $sequenceData,
                        [
                            'created_at' => now(),
                        ]
                    )
                );
        }

        return $nextNumber;
    }

    private function inventoryNumber(
        int $year,
        array $locations,
        int $number
    ): string {
        return implode('/', [
            $year,
            $locations['gedung']->kode,
            $locations['lantai']->kode,
            $locations['lokasi']->kode,
            $locations['lokasi']->kode,
            $number,
        ]);
    }

    /**
     * Tambah catatan maintenance.
     */
    public function storeMaintenance(
        Request $request,
        $id
    ) {
        $aset = AsetInventaris::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
            ],

            'pelapor' => [
                'required',
                'string',
                'max:255',
            ],

            'gejala_masalah' => [
                'required',
                'string',
            ],

            'penyebab' => [
                'nullable',
                'string',
            ],

            'tindakan_penanganan' => [
                'nullable',
                'string',
            ],

            'teknisi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:Open,Dalam Penanganan,Selesai',
            ],
        ]);

        $aset->maintenanceLogs()->create($validated);

        return redirect()
            ->route(
                'aset-inventaris.show',
                $aset->id
            )
            ->with(
                'success',
                'Catatan maintenance berhasil ditambahkan.'
            );
    }

    public function updateMaintenanceStatus(
        Request $request,
        $id
    ) {
        $maintenanceLog = MaintenanceLog::findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:Open,Dalam Penanganan,Selesai',
            ],
        ]);

        $maintenanceLog->update($validated);

        return redirect()
            ->route(
                'aset-inventaris.show',
                $maintenanceLog->aset_inventaris_id
            )
            ->with(
                'success',
                'Status maintenance berhasil diperbarui.'
            );
    }

    /**
     * Validasi data aset.
     */
    private function validatedAsset(
        Request $request,
    ): array {
        $validated = $request->validate([
            'tahun_perolehan' => [
                'required',
                'integer',
                'between:1900,2200',
            ],

            'gedung_id' => [
                'required',
                'integer',
            ],

            'lantai_id' => [
                'required',
                'integer',
            ],

            'lokasi_id' => [
                'required',
                'integer',
            ],

            'nama_barang' => [
                'required',
                'string',
                'max:255',
            ],

            'spesifikasi' => [
                'nullable',
                'string',
            ],

            'lantai' => [
                'nullable',
                'string',
                'max:255',
            ],

            'lokasi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'user' => [
                'required',
                'string',
                'max:255',
            ],

            'kelengkapan' => [
                'nullable',
                'string',
            ],

            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

            'tanggal_entry' => [
                'required',
                'date',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:Aktif,Nonaktif',
            ],

            'tanggal_nonaktif' => [
                'nullable',
                'date',
                'required_if:status,Nonaktif',
            ],

            'alasan_nonaktif' => [
                'nullable',
                'string',
                'required_if:status,Nonaktif',
            ],
        ]);

        $validated['nama_user'] = $validated['user'] ?? null;

        unset($validated['user']);

        return $validated;
    }

    private function locationsByType(): array
    {
        return collect([
            'Gedung',
            'Lantai',
            'Lokasi',
        ])->mapWithKeys(
            fn(string $type) => [
                Str::lower($type) => MasterLokasi::where(
                    'tipe',
                    $type
                )
                    ->orderBy('nama')
                    ->get(),
            ]
        )->all();
    }

    private function validatedLocations(
        array $validated
    ): array {
        return [
            'gedung' => MasterLokasi::where(
                'id',
                $validated['gedung_id']
            )
                ->where('tipe', 'Gedung')
                ->firstOrFail(),

            'lantai' => MasterLokasi::where(
                'id',
                $validated['lantai_id']
            )
                ->where('tipe', 'Lantai')
                ->firstOrFail(),

            'lokasi' => MasterLokasi::where(
                'id',
                $validated['lokasi_id']
            )
                ->where('tipe', 'Lokasi')
                ->firstOrFail(),
        ];
    }

    private function assetDataWithLocationNames(
        array $validated,
        array $locations
    ): array {
        $data = $validated;

        $data['lantai'] = $locations['lantai']->nama;
        $data['lokasi'] = $locations['lokasi']->nama;

        $data['nama_user'] = $data['user'] ?? (
            $data['nama_user'] ?? null
        );

        unset(
            $data['user'],
            $data['status'],
            $data['tanggal_nonaktif'],
            $data['alasan_nonaktif']
        );

        $data['gedung_id'] = $locations['gedung']->id;
        $data['lantai_id'] = $locations['lantai']->id;
        $data['lokasi_id'] = $locations['lokasi']->id;

        $data['status'] = $validated['status'] ?? 'Aktif';

        $data['tanggal_nonaktif'] =
            $data['status'] === 'Nonaktif'
            ? (
                $validated['tanggal_nonaktif']
                ?? now()->toDateString()
            )
            : null;

        $data['alasan_nonaktif'] =
            $data['status'] === 'Nonaktif'
            ? (
                $validated['alasan_nonaktif']
                ?? null
            )
            : null;

        return $data;
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
                        'superadmin',
                    ],
                    true
                ),
            403
        );
    }
}
