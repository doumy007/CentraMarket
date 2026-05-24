<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrónica', 'description' => 'Productos electrónicos y tecnología'],
            ['name' => 'Ropa y Moda', 'description' => 'Prendas de vestir y accesorios'],
            ['name' => 'Hogar', 'description' => 'Artículos para el hogar y decoración'],
            ['name' => 'Deportes', 'description' => 'Equipamiento y ropa deportiva'],
            ['name' => 'Libros', 'description' => 'Libros, revistas y material educativo'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
