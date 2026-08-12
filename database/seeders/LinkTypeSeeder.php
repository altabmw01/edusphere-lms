<?php

namespace Database\Seeders;

use App\Models\LinkType;
use Illuminate\Database\Seeder;

class LinkTypeSeeder extends Seeder
{
    public function run(): void
    {
        LinkType::firstOrCreate(['slug' => 'zoom'], ['name' => 'Zoom']);
        LinkType::firstOrCreate(['slug' => 'google-meet'], ['name' => 'Google Meet']);
    }
}
