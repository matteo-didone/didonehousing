<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class DeleteCorruptedProperty extends Command
{
    protected $signature = 'property:delete-corrupted {id}';
    protected $description = 'Force delete a corrupted property by ID';

    public function handle()
    {
        $id = $this->argument('id');

        $property = Property::find($id);

        if (!$property) {
            $this->error("Property with ID {$id} not found.");
            return 1;
        }

        $this->info("Found property:");
        $this->info("ID: {$property->id}");
        $this->info("Status: " . ($property->status ?? 'NULL'));
        $this->info("Street: " . ($property->street_name ?? 'NULL'));
        $this->info("House Number: " . ($property->house_number ?? 'NULL'));
        $this->info("City: " . ($property->city ?? 'NULL'));
        $this->info("Landlord ID: {$property->landlord_id}");

        if ($this->confirm('Do you want to delete this property?')) {
            $property->delete();
            $this->info("Property {$id} deleted successfully!");
            return 0;
        }

        $this->info('Deletion cancelled.');
        return 0;
    }
}
