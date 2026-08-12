<?php

namespace Database\Seeders;

use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed demo accounts — predictable credentials for local development.
        User::factory()->admin()->create([
            'name' => 'Michael Grant',
            'email' => 'admin@edusphere.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->manager()->create([
            'name' => 'Sofia Novak',
            'email' => 'manager@edusphere.test',
            'password' => Hash::make('password'),
        ]);

        $teacherNames = ['Sarah Mitchell', 'Daniel Osei', 'Ravi Chandran', 'Elena Petrova', 'Marcus Lee'];
        foreach ($teacherNames as $name) {
            $teacher = User::factory()->teacher()->create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@edusphere.test',
                'password' => Hash::make('password'),
            ]);

            TeacherProfile::create([
                'user_id' => $teacher->id,
                'headline' => fake()->jobTitle(),
                'biography' => fake()->paragraph(4),
                'social_links' => [
                    'website' => 'https://example.com/' . str($name)->slug(),
                    'twitter' => 'https://twitter.com/' . str($name)->slug(),
                    'linkedin' => 'https://linkedin.com/in/' . str($name)->slug(),
                ],
                'skills' => fake()->randomElements(
                    ['PHP', 'Laravel', 'JavaScript', 'React', 'UI/UX', 'SEO', 'Copywriting', 'Data Analysis', 'IELTS Coaching'],
                    3
                ),
                'experience_years' => fake()->numberBetween(3, 15),
                'rating_avg' => fake()->randomFloat(2, 4.0, 5.0),
                'rating_count' => fake()->numberBetween(50, 3000),
                'total_revenue' => fake()->randomFloat(2, 500, 50000),
            ]);
        }

        User::factory()->student()->create([
            'name' => 'Emily Carter',
            'email' => 'student@edusphere.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->student()->count(30)->create();
    }
}
