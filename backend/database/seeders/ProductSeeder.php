<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name'        => 'Wireless Noise-Cancelling Headphones',
                'description' => 'Premium over-ear headphones with active noise cancellation, 30-hour battery life, and crystal-clear audio quality. Perfect for travel and work.',
                'price'       => 149.99,
                'image'       => null,
            ],
            [
                'name'        => 'Mechanical Gaming Keyboard',
                'description' => 'RGB backlit mechanical keyboard with tactile switches, anti-ghosting technology, and programmable macro keys for the ultimate gaming experience.',
                'price'       => 89.99,
                'image'       => null,
            ],
            [
                'name'        => '4K Ultra HD Monitor',
                'description' => '27-inch 4K IPS panel monitor with 144Hz refresh rate, HDR400 support, and eye-care technology. Ideal for gaming and creative work.',
                'price'       => 399.99,
                'image'       => null,
            ],
            [
                'name'        => 'Ergonomic Office Chair',
                'description' => 'Fully adjustable ergonomic chair with lumbar support, mesh back, adjustable armrests, and 5-year warranty. Designed for all-day comfort.',
                'price'       => 299.99,
                'image'       => null,
            ],
            [
                'name'        => 'USB-C Hub 7-in-1',
                'description' => 'Multi-port USB-C hub featuring 4K HDMI, 3x USB-A 3.0, SD card reader, microSD, and 100W PD charging. Compatible with all laptops.',
                'price'       => 49.99,
                'image'       => null,
            ],
            [
                'name'        => 'Portable SSD 1TB',
                'description' => 'Ultra-fast portable SSD with USB 3.2 Gen 2 speeds up to 1,050 MB/s read speed. Shock-resistant and password-protected. Perfect for on-the-go storage.',
                'price'       => 119.99,
                'image'       => null,
            ],
            [
                'name'        => 'Wireless Ergonomic Mouse',
                'description' => 'Vertical ergonomic mouse that reduces wrist strain by 57%. Features 6 programmable buttons, DPI adjustable up to 4000, and 90-day battery life.',
                'price'       => 39.99,
                'image'       => null,
            ],
            [
                'name'        => 'Smart LED Desk Lamp',
                'description' => 'Touch-controlled LED desk lamp with 5 color temperatures, 5 brightness levels, USB charging port, and auto-dimming feature for eye protection.',
                'price'       => 35.99,
                'image'       => null,
            ],
            [
                'name'        => 'Webcam 1080p HD',
                'description' => 'Full HD 1080p webcam with built-in stereo microphone, autofocus, low-light correction, and plug-and-play compatibility. Ideal for video calls.',
                'price'       => 69.99,
                'image'       => null,
            ],
            [
                'name'        => 'Cable Management Box',
                'description' => 'Large cable management box with surge-protected power strip, accommodates up to 6 outlets and 3 USB ports. Keeps your workspace tidy and organized.',
                'price'       => 24.99,
                'image'       => null,
            ],
            [
                'name'        => 'Laptop Stand Adjustable',
                'description' => 'Aluminum adjustable laptop stand with 6 height levels. Compatible with 10-17 inch laptops. Improves posture and cooling airflow.',
                'price'       => 29.99,
                'image'       => null,
            ],
            [
                'name'        => 'Bluetooth Speaker Waterproof',
                'description' => 'IPX7 waterproof portable Bluetooth speaker with 360° sound, 24-hour playtime, built-in microphone, and lights up with ambient LEDs.',
                'price'       => 79.99,
                'image'       => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Products seeded successfully! Created ' . count($products) . ' products.');
    }
}
