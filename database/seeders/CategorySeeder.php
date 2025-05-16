<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;  // ← IMPORT

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    // database/seeders/CategorySeeder.php
    public function run()
    {
    $cats = ['Comida típica','Hospedaje','Tours guiados','Experiencias culturales','Artesanía'];
    foreach($cats as $c){
        Category::firstOrCreate(['name'=>$c]);
    }
    }

}
