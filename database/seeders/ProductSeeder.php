<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'name' => 'Smartphone Pro Max', 'description' => 'Teléfono inteligente de última generación con pantalla OLED de 6.7 pulgadas, cámara de 108MP y 256GB de almacenamiento.', 'price' => 899.99, 'promotion_price' => 749.99, 'promotion_active' => true, 'promotion_start' => now()->subDay(), 'promotion_end' => now()->addDays(15), 'stock' => 15, 'image' => 'https://picsum.photos/seed/smartphone/800/800'],
            ['category_id' => 1, 'name' => 'Laptop Ultrabook', 'description' => 'Laptop ligera con procesador i7, 16GB RAM, SSD 512GB y pantalla táctil 4K.', 'price' => 1299.99, 'stock' => 8, 'image' => 'https://picsum.photos/seed/laptop/800/800'],
            ['category_id' => 1, 'name' => 'Auriculares Bluetooth', 'description' => 'Auriculares inalámbricos con cancelación de ruido activa y 30 horas de batería.', 'price' => 149.99, 'promotion_price' => 99.99, 'promotion_active' => true, 'promotion_start' => now()->subDay(), 'promotion_end' => now()->addDays(30), 'stock' => 25, 'image' => 'https://picsum.photos/seed/headphones/800/800'],
            ['category_id' => 1, 'name' => 'Tablet 10 Pulgadas', 'description' => 'Tablet con pantalla Full HD, 64GB de almacenamiento y compatible con lápiz táctil.', 'price' => 349.99, 'stock' => 12, 'image' => 'https://picsum.photos/seed/tablet/800/800'],
            ['category_id' => 2, 'name' => 'Chaqueta de Cuero', 'description' => 'Chaqueta de cuero genuino con forro interior térmico. Disponible en varios colores.', 'price' => 199.99, 'promotion_price' => 159.99, 'promotion_active' => true, 'promotion_start' => now()->subDay(), 'promotion_end' => now()->addDays(7), 'stock' => 20, 'image' => 'https://picsum.photos/seed/jacket/800/800'],
            ['category_id' => 2, 'name' => 'Zapatillas Running', 'description' => 'Zapatillas deportivas con amortiguación avanzada y suela antideslizante.', 'price' => 89.99, 'stock' => 30, 'image' => 'https://picsum.photos/seed/sneakers/800/800'],
            ['category_id' => 2, 'name' => 'Reloj de Pulsera', 'description' => 'Reloj elegante con correa de acero inoxidable y movimiento de cuarzo suizo.', 'price' => 249.99, 'stock' => 10, 'image' => 'https://picsum.photos/seed/watch/800/800'],
            ['category_id' => 3, 'name' => 'Lámpara de Mesa LED', 'description' => 'Lámpara moderna con luz LED regulable, temperatura de color ajustable y base giratoria.', 'price' => 59.99, 'promotion_price' => 44.99, 'promotion_active' => true, 'promotion_start' => now()->subDay(), 'promotion_end' => now()->addDays(20), 'stock' => 18, 'image' => 'https://picsum.photos/seed/lamp/800/800'],
            ['category_id' => 3, 'name' => 'Set de Sartenes Antiadherentes', 'description' => 'Juego de 3 sartenes con recubrimiento cerámico antiadherente y mangos ergonómicos.', 'price' => 79.99, 'stock' => 22, 'image' => 'https://picsum.photos/seed/pans/800/800'],
            ['category_id' => 3, 'name' => 'Organizador de Escritorio', 'description' => 'Organizador multifuncional de bambú con compartimentos para accesorios de oficina.', 'price' => 34.99, 'stock' => 40, 'image' => 'https://picsum.photos/seed/organizer/800/800'],
            ['category_id' => 4, 'name' => 'Bicicleta Montaña 21 Velocidades', 'description' => 'Bicicleta de montaña con cuadro de aluminio, frenos de disco y suspensión delantera.', 'price' => 499.99, 'stock' => 5, 'image' => 'https://picsum.photos/seed/bike/800/800'],
            ['category_id' => 4, 'name' => 'Pesas Rusa 16kg', 'description' => 'Pesa rusa de hierro fundido con agarre cómodo para entrenamiento funcional.', 'price' => 39.99, 'stock' => 35, 'image' => 'https://picsum.photos/seed/kettlebell/800/800'],
            ['category_id' => 4, 'name' => 'Esterilla Yoga Premium', 'description' => 'Esterilla de yoga antideslizante de 6mm de grosor con bolsa de transporte incluida.', 'price' => 44.99, 'stock' => 28, 'image' => 'https://picsum.photos/seed/yogamat/800/800'],
            ['category_id' => 5, 'name' => 'Cien Años de Soledad', 'description' => 'Obra maestra de Gabriel García Márquez. Edición conmemorativa con ilustraciones.', 'price' => 24.99, 'stock' => 50, 'image' => 'https://picsum.photos/seed/book1/800/800'],
            ['category_id' => 5, 'name' => 'El Principito', 'description' => 'Clásico de Antoine de Saint-Exupéry. Edición ilustrada tapa dura.', 'price' => 15.99, 'stock' => 45, 'image' => 'https://picsum.photos/seed/book2/800/800'],
        ];

        foreach ($products as $data) {
            $product = Product::create($data);

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $product->image,
                'order' => 0,
            ]);

            if ($product->id % 2 === 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => str_replace('/seed/', '/seed/' . $product->slug . '-alt', $product->image ?? 'https://picsum.photos/seed/default-alt/800/800'),
                    'order' => 1,
                ]);
            }
        }
    }
}
