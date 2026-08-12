<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $courseCategories = [
            ['name' => 'English Speaking', 'icon' => 'bi-chat-dots', 'color' => '#2563EB'],
            ['name' => 'IELTS', 'icon' => 'bi-award', 'color' => '#0EA5E9'],
            ['name' => 'Programming', 'icon' => 'bi-code-slash', 'color' => '#F59E0B'],
            ['name' => 'Web Development', 'icon' => 'bi-globe2', 'color' => '#22C55E'],
            ['name' => 'Graphic Design', 'icon' => 'bi-palette', 'color' => '#EF4444'],
            ['name' => 'Digital Marketing', 'icon' => 'bi-megaphone', 'color' => '#8B5CF6'],
            ['name' => 'Freelancing', 'icon' => 'bi-briefcase', 'color' => '#0EA5E9'],
            ['name' => 'Business', 'icon' => 'bi-graph-up-arrow', 'color' => '#2563EB'],
            ['name' => 'Artificial Intelligence', 'icon' => 'bi-cpu', 'color' => '#F59E0B'],
            ['name' => 'Mobile Development', 'icon' => 'bi-phone', 'color' => '#22C55E'],
            ['name' => 'Microsoft Office', 'icon' => 'bi-file-earmark-bar-graph', 'color' => '#EF4444'],
            ['name' => 'Islamic Studies', 'icon' => 'bi-book', 'color' => '#8B5CF6'],
        ];

        foreach ($courseCategories as $i => $category) {
            Category::create([...$category, 'type' => 'course', 'sort_order' => $i, 'status' => true]);
        }

        $bookCategories = [
            ['name' => 'Programming Books', 'icon' => 'bi-code-square', 'color' => '#2563EB'],
            ['name' => 'IELTS & Language', 'icon' => 'bi-translate', 'color' => '#0EA5E9'],
            ['name' => 'Design Books', 'icon' => 'bi-brush', 'color' => '#F59E0B'],
            ['name' => 'Marketing Books', 'icon' => 'bi-megaphone', 'color' => '#22C55E'],
            ['name' => 'Business Books', 'icon' => 'bi-briefcase', 'color' => '#EF4444'],
        ];

        foreach ($bookCategories as $i => $category) {
            Category::create([...$category, 'type' => 'book', 'sort_order' => $i, 'status' => true]);
        }
    }
}
