<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Models\Floor;
use App\Models\Room;

trait HasTenantSetup
{
    /**
     * Create floors and rooms for a tenant.
     */
    protected function setupTenantStructure(Tenant $tenant)
    {
        for ($floorNumber = 1; $floorNumber <= 3; $floorNumber++) {
            $floor = Floor::create([
                'tenant_id' => $tenant->id,
                'floor_number' => $floorNumber,
                'name' => "Piso {$floorNumber}",
            ]);
            
            // Create 10 rooms for this floor
            for ($roomNumber = 1; $roomNumber <= 10; $roomNumber++) {
                // Numbering scheme: 101-110, 201-210, 301-310
                $calculatedRoomNumber = ($floorNumber * 100) + $roomNumber;
                
                Room::create([
                    'floor_id' => $floor->id,
                    'room_number' => $calculatedRoomNumber,
                    'status' => 'available',
                ]);
            }
        }
    }
}
