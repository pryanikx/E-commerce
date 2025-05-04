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
            ['name' => 'installation', 'description' => 'installation of the device', 'duration' => '1 day'],
            ['name' => 'delivery', 'description' => 'delivery of the device', 'duration' => '3 days'],
            ['name' => 'configuration', 'description' => 'configuration of the device', 'duration' => '2 hours'],
            ['name' => 'warranty', 'description' => 'warranty of the device', 'duration' => '2 years'],
        ];

        foreach ($maintenances as $maintenance) {
            Maintenance::create($maintenance);
        }
    }
}
