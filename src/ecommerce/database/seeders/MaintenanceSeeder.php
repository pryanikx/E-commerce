<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maintenance;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maintenances = [
            ['name' => 'installation', 'description' => 'installation of the device'],
            ['name' => 'delivery', 'description' => 'delivery of the device'],
            ['name' => 'configuration', 'description' => 'configuration of the device'],
            ['name' => 'warranty', 'description' => 'warranty of the device'],
        ];

        foreach ($maintenances as $maintenance) {
            Maintenance::create($maintenance);
        }
    }
}
