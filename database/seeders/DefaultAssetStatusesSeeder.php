<?php

namespace Database\Seeders;

use App\Models\WorkUnitAssetStatus;
use Illuminate\Database\Seeder;

class DefaultAssetStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the default individual asset statuses into the work_unit_asset_statuses table.
     * These statuses are shared between individual and work unit assets.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Aktif Digunakan', 'slug' => 'active',      'is_active' => true, 'order' => 1],
            ['name' => 'Di Gudang',       'slug' => 'in_storage',  'is_active' => true, 'order' => 2],
            ['name' => 'Dalam Perbaikan', 'slug' => 'maintenance', 'is_active' => true, 'order' => 3],
            ['name' => 'Rusak',           'slug' => 'damaged',     'is_active' => true, 'order' => 4],
            ['name' => 'Dihapuskan',      'slug' => 'disposed',    'is_active' => true, 'order' => 5],
        ];

        foreach ($statuses as $status) {
            WorkUnitAssetStatus::firstOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
