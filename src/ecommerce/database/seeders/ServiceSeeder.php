<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'installation', 'description' => 'installation of the device'],
            ['name' => 'delivery', 'description' => 'delivery of the device'],
            ['name' => 'configuration', 'description' => 'configuration of the device'],
            ['name' => 'warranty', 'description' => 'warrantry of the device'],
        ];
    
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
