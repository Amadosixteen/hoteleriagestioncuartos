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
                // Room numbers like 101, 102... or 1, 2, 3...
                // The prompt says "10 cuartos en cada una, enumeralas en orden"
                // I'll keep them as 1, 2, 3... per floor for now
                Room::create([
                    'floor_id' => $floor->id,
                    'room_number' => $roomNumber,
                    'status' => 'available',
                ]);
            }
        }
    }
}
