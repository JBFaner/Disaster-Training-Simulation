<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            [
                'name' => 'Fire Extinguisher',
                'category' => 'Safety Equipment',
                'quantity' => 5,
                'available' => 5,
                'condition' => 'Good',
                'status' => 'Available',
                'location' => 'Storage Room A',
                'serial_number' => 'FE-001-2025',
                'assigned_to_event_id' => null,
                'assigned_handler_id' => null,
                'maintenance_status' => 'No',
            ],
            [
                'name' => 'First Aid Kit',
                'category' => 'Medical',
                'quantity' => 10,
                'available' => 10,
                'condition' => 'Good',
                'status' => 'Available',
                'location' => 'Storage Room B',
                'serial_number' => 'FAK-002-2025',
                'assigned_to_event_id' => null,
                'assigned_handler_id' => null,
                'maintenance_status' => 'No',
            ],
            [
                'name' => 'Emergency Light',
                'category' => 'Lighting',
                'quantity' => 3,
                'available' => 3,
                'condition' => 'Good',
                'status' => 'Available',
                'location' => 'Storage Room A',
                'serial_number' => 'EL-003-2025',
                'assigned_to_event_id' => null,
                'assigned_handler_id' => null,
                'maintenance_status' => 'No',
            ],
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }
    }
}
