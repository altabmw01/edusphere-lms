<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            LinkTypeSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            BookSeeder::class,
            BatchSeeder::class,
            CouponSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            DemoOrderSeeder::class,
        ]);
    }
}
