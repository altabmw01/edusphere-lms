<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'EduSphere',
            'logo' => null,
            'favicon' => null,
            'smtp_host' => null,
            'seo_title' => 'EduSphere — Online Courses & Digital Books',
            'seo_description' => 'Learn programming, design, business, marketing and more with EduSphere\'s expert-led courses and digital books.',
            'social_facebook' => 'https://facebook.com/edusphere',
            'social_twitter' => 'https://twitter.com/edusphere',
            'social_instagram' => 'https://instagram.com/edusphere',
            'currency' => config('lms.currency_code', 'BDT'),
            'timezone' => 'UTC',
            'maintenance_mode' => false,
            'shipping_cost_dhaka' => 60,
            'shipping_cost_outside_dhaka' => 120,
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
