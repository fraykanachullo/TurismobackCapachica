<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        Blog::firstOrCreate([
            'title' => 'Descubre los mejores tours en Capachica',
            'content' => 'En este blog te contamos los tours más populares y cómo reservarlos.',
            'status' => 'published',
            'published_at' => now(),
            'company_id' => 1,  // ID válido de una empresa existente
        ]);
    }
}
