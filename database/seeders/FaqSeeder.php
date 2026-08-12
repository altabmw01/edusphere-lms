<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['category' => 'Getting Started', 'question' => 'Do I need any prior experience to start a course?', 'answer' => 'No. Most of our courses are designed for beginners, with clear labeling for intermediate and advanced content.'],
            ['category' => 'Getting Started', 'question' => 'How do I create an account?', 'answer' => 'Click Register in the top navigation, fill in your name, email, and password, and you\'re ready to start learning.'],
            ['category' => 'Courses', 'question' => 'How long do I have access to a purchased course?', 'answer' => 'All course purchases include full lifetime access, so you can revisit lessons whenever you like.'],
            ['category' => 'Courses', 'question' => 'Do I get a certificate after finishing a course?', 'answer' => 'Yes, courses with certification enabled automatically issue a downloadable PDF certificate once you reach 100% progress.'],
            ['category' => 'Books', 'question' => 'Can I download books for offline reading?', 'answer' => 'Yes, all digital books can be downloaded in PDF format from your dashboard after purchase.'],
            ['category' => 'Payments', 'question' => 'What payment methods are supported?', 'answer' => 'We support Cash on Delivery and SSLCommerz (cards, mobile banking, and net banking).'],
            ['category' => 'Payments', 'question' => 'Can I get a refund?', 'answer' => 'Refund eligibility depends on course/book usage. Contact support within 7 days of purchase for a review.'],
            ['category' => 'Account', 'question' => 'How do I become a teacher on EduSphere?', 'answer' => 'Teacher accounts are created by our Admin team. Reach out via the Contact page to apply.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::create([...$faq, 'sort_order' => $i, 'status' => true]);
        }
    }
}
