<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faq = Faq::where('question', 'What is FAQ?')->first();

        if (!$faq) {
            $faq = new Faq();
            $faq->question = 'What is FAQ?';
            $faq->answer = 'FAQ stands for Frequently Asked Questions, which are common questions and their answers.';
            $faq->status = 'active';
            $faq->save();
        } else {
            $this->command->info('FAQ seed already exists, skipped seeding.');
        }
    }
}
