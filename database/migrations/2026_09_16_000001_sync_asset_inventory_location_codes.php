<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $locationMappings = [
            'Gedung' => ['gedung_id', [1]],
            'Lantai' => ['lantai_id', [2]],
            'Lokasi' => ['lokasi_id', [3, 4]],
        ];

        foreach ($locationMappings as $type => [$foreignKey, $segments]) {
            DB::table('master_lokasi')
                ->where('tipe', $type)
                ->get(['id', 'kode'])
                ->each(function (object $location) use ($foreignKey, $segments): void {
                    $assets = DB::table('aset_inventaris')
                        ->where($foreignKey, $location->id)
                        ->get(['id', 'no_inventaris']);
                    $updates = [];
                    $reservedNumbers = [];

                    foreach ($assets as $asset) {
                        $parts = explode('/', $asset->no_inventaris);
                        $changed = false;

                        foreach ($segments as $segment) {
                            if (($parts[$segment] ?? null) !== $location->kode) {
                                $parts[$segment] = $location->kode;
                                $changed = true;
                            }
                        }

                        if ($changed) {
                            $updates[$asset->id] = implode('/', $parts);
                            $reservedNumbers[$updates[$asset->id]] = true;
                        }
                    }

                    foreach (array_keys($updates) as $assetId) {
                        DB::table('aset_inventaris')
                            ->where('id', $assetId)
                            ->update([
                                'no_inventaris' => '__location_sync__' . $assetId,
                                'updated_at' => now(),
                            ]);
                    }

                    foreach ($updates as $assetId => $inventoryNumber) {
                        unset($reservedNumbers[$inventoryNumber]);
                        $parts = explode('/', $inventoryNumber);
                        $number = (int) array_pop($parts);
                        $prefix = implode('/', $parts);
                        $candidate = $inventoryNumber;

                        while (
                            isset($reservedNumbers[$candidate])
                            || DB::table('aset_inventaris')
                            ->where('no_inventaris', $candidate)
                            ->exists()
                        ) {
                            $candidate = $prefix . '/' . (++$number);
                        }

                        DB::table('aset_inventaris')
                            ->where('id', $assetId)
                            ->update([
                                'no_inventaris' => $candidate,
                                'updated_at' => now(),
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Data corrections cannot be safely reversed without the previous codes.
    }
};
