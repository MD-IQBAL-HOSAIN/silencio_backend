<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            ['id' => 1, 'page_title' => 'About Us', 'page_content' => 'This is the about us page'],
            ['id' => 2, 'page_title' => 'Terms & Conditions', 'page_content' => 'This is the Terms & Conditions page'],
            ['id' => 3, 'page_title' => 'Privacy Policy', 'page_content' => 'This is the privacy policy page'],
        ];

        foreach ($pages as $page) {
            if (!Page::where('id', $page['id'])->exists()) {
                Page::create($page);
            }
        }
    }
}
