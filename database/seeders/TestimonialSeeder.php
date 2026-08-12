<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Jamie Cooper', 'designation' => 'Frontend Developer', 'rating' => 5, 'content' => 'EduSphere completely changed how I approach learning. The web development course was practical and easy to follow.'],
            ['name' => 'Priya Nair', 'designation' => 'Graduate Student', 'rating' => 5, 'content' => 'The IELTS course helped me score a 7.5 in just six weeks. Clear structure, great instructor feedback.'],
            ['name' => 'Tom Becker', 'designation' => 'Freelance Designer', 'rating' => 4, 'content' => 'Affordable, well-organized, and the certificate helped me land a new freelance client within weeks.'],
            ['name' => 'Hana Suzuki', 'designation' => 'Data Analyst', 'rating' => 5, 'content' => 'The AI course made complex concepts approachable. I finally understand how machine learning models work.'],
            ['name' => 'Karim Haddad', 'designation' => 'Marketing Manager', 'rating' => 5, 'content' => 'Great mobile experience — I completed most lessons during my commute. Highly recommend.'],
            ['name' => 'Laura Bennett', 'designation' => 'Product Designer', 'rating' => 5, 'content' => 'The books library is a hidden gem. I paired the design course with two related e-books and it clicked.'],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create([...$testimonial, 'status' => true]);
        }
    }
}
