<?php
namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        Organization::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
        Organization::create(['name' => 'Globex Inc', 'slug' => 'globex-inc']);
        Organization::create(['name' => 'Initech', 'slug' => 'initech']);
    }
}