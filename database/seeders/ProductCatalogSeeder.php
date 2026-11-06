<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $vendor1 = Vendor::where('shop_slug', 'apex-tech')->first() ?? Vendor::first();
        $vendor2 = Vendor::where('shop_slug', 'luxe-living')->first() ?? Vendor::first();

        // 1. Ensure all primary categories exist with clean slugs
        $categoriesData = [
            'electronics' => [
                'name' => 'Electronics & Gadgets',
                'description' => 'Noise-canceling headphones, custom mechanical keyboards, high-fidelity audio and wearable tech.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                'icon' => 'headphones',
            ],
            'fashion' => [
                'name' => 'Fashion & Apparel',
                'description' => 'Modern technical streetwear, merino wool textiles, premium sneakers and everyday apparel.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                'icon' => 'shirt',
            ],
            'home' => [
                'name' => 'Home & Living',
                'description' => 'Architectural solid walnut desks, ambient lighting, ceramic mugs and Scandinavian home decor.',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&auto=format&fit=crop&q=80',
                'icon' => 'armchair',
            ],
            'beauty' => [
                'name' => 'Beauty & Self Care',
                'description' => 'Botanical skincare, hydrating serums, artisanal fragrances and minimalist grooming essentials.',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80',
                'icon' => 'sparkles',
            ],
            'sports' => [
                'name' => 'Sports & Outdoor',
                'description' => 'Zero-drop trail running shoes, insulated titanium flasks, fitness gear and outdoor accessories.',
                'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80',
                'icon' => 'activity',
            ],
            'bags' => [
                'name' => 'Bags & EDC Carry',
                'description' => 'Cordura ballistic backpacks, weatherproof tech slings, and vegetable-tanned cardholders.',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                'icon' => 'briefcase',
            ],
            'books' => [
                'name' => 'Books & Stationery',
                'description' => 'Industrial design monographs, architectural hardcover journals, and machined solid brass pens.',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
                'icon' => 'book-open',
            ],
        ];

        $cats = [];
        foreach ($categoriesData as $slug => $data) {
            $cats[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'image' => $data['image'],
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => count($cats) + 1,
                ]
            );
        }

        // Map existing categories if present
        $catElectronics = $cats['electronics'];
        $catFashion = $cats['fashion'];
        $catHome = $cats['home'];
        $catBeauty = $cats['beauty'];
        $catSports = $cats['sports'];
        $catBags = $cats['bags'];
        $catBooks = $cats['books'];

        $products = [
            // --- 1. Electronics & Gadgets ---
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'Aether Pro Spatial Wireless Headphones',
                'slug' => 'aether-pro-spatial-wireless-headphones',
                'price' => 389.00,
                'sale_price' => 329.00,
                'stock_quantity' => 45,
                'sku' => 'APX-ATH-01',
                'short_description' => 'Bespoke 40mm beryllium drivers with lossless Bluetooth 5.4, ultra-low latency, and hybrid ANC.',
                'description' => 'Forged from aircraft-grade anodized aluminum and lambskin memory foam earcups, the Aether Pro represents the pinnacle of personal acoustic engineering.',
                'images' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.95,
                'rating_count' => 64,
                'sales_count' => 320,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'Vanguard 75% CNC Mechanical Keyboard',
                'slug' => 'vanguard-75-cnc-mechanical-keyboard',
                'price' => 245.00,
                'sale_price' => 219.00,
                'stock_quantity' => 30,
                'sku' => 'APX-KEY-75',
                'short_description' => 'Heavyweight gasket-mounted 6063 aluminum chassis with pre-lubed linear switches and hot-swap PCB.',
                'description' => 'Engineered for tactile precision and deep acoustic resonance. Features Poron acoustic dampening foam, screw-in stabilizers, and wireless triple-mode connectivity.',
                'images' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.88,
                'rating_count' => 84,
                'sales_count' => 219,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'Chronos Horizon Sapphire Smartwatch',
                'slug' => 'chronos-horizon-sapphire-smartwatch',
                'price' => 420.00,
                'sale_price' => 349.00,
                'stock_quantity' => 22,
                'sku' => 'APX-WAT-02',
                'short_description' => 'Ultra-bright AMOLED display encased in Grade 5 Titanium with 14-day battery and biometric health telemetry.',
                'description' => 'A rugged yet elegant timepiece crafted for extreme explorers and executive boardrooms alike. Water-resistant up to 100 meters with continuous ECG monitoring.',
                'images' => [
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.91,
                'rating_count' => 92,
                'sales_count' => 410,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'Pulse Wireless Pro Gaming Controller',
                'slug' => 'pulse-wireless-pro-gaming-controller',
                'price' => 129.00,
                'sale_price' => 99.00,
                'stock_quantity' => 60,
                'sku' => 'APX-CTR-99',
                'short_description' => 'Hall-effect magnetic joysticks with zero drift, tactile mechanical buttons, and custom remappable back paddles.',
                'description' => 'Tournament-grade input precision for esports and PC gaming. Zero drift magnetic sensors, hair-trigger locks, and 1000Hz polling rate.',
                'images' => [
                    'https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1592840496694-26d035b52b48?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.78,
                'rating_count' => 45,
                'sales_count' => 180,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'Lumen 35mm F/1.4 Full-Frame Prime Lens',
                'slug' => 'lumen-35mm-f1-4-full-frame-prime-lens',
                'price' => 499.00,
                'sale_price' => null,
                'stock_quantity' => 15,
                'sku' => 'APX-OPT-35',
                'short_description' => 'Ultra-fast manual prime lens with silky 160-degree focus throw, clicked aperture ring, and brass barrel.',
                'description' => 'Designed for cinematic depth and dreamy bokeh rendition. Features 11 optical elements in 9 groups with nano-crystal anti-reflective coating.',
                'images' => [
                    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.96,
                'rating_count' => 31,
                'sales_count' => 88,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catElectronics->id,
                'name' => 'AcousticWave Hi-Fi Desktop Tube DAC & Amp',
                'slug' => 'acousticwave-hi-fi-desktop-tube-dac-amp',
                'price' => 280.00,
                'sale_price' => 240.00,
                'stock_quantity' => 18,
                'sku' => 'APX-AMP-04',
                'short_description' => 'Dual vacuum tube preamplifier with ESS Sabre 32-bit DAC chip and balanced 4.4mm output.',
                'description' => 'Inject warmth, harmonic depth, and spatial realism into your favorite headphones. Encased in bead-blasted anodized aluminum with orange tube glow.',
                'images' => [
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.82,
                'rating_count' => 27,
                'sales_count' => 95,
            ],

            // --- 2. Fashion & Apparel ---
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catFashion->id,
                'name' => 'Apex Carbon Ultra-Lightweight Sneakers',
                'slug' => 'apex-carbon-ultra-lightweight-sneakers',
                'price' => 180.00,
                'sale_price' => 149.00,
                'stock_quantity' => 50,
                'sku' => 'LX-SNK-02',
                'short_description' => 'Dynamic carbon-fiber rebound plate with breathable engineered knit and high-traction rubber pods.',
                'description' => 'Engineered for urban speed and cushioned all-day walking. High energy return foam absorbs impact while the carbon stabilizer propels every stride.',
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.89,
                'rating_count' => 110,
                'sales_count' => 540,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catFashion->id,
                'name' => 'Minimalist Heavyweight French Terry Hoodie',
                'slug' => 'minimalist-heavyweight-french-terry-hoodie',
                'price' => 115.00,
                'sale_price' => null,
                'stock_quantity' => 40,
                'sku' => 'LX-HD-08',
                'short_description' => '500 GSM organic comb-spun cotton with double-needle reverse cover-stitching and boxy relaxed drape.',
                'description' => 'Uncompromising architectural garment construction. No exterior branding, double-layer hood that stands tall, and preshrunk heavyweight knit.',
                'images' => [
                    'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.85,
                'rating_count' => 52,
                'sales_count' => 230,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catFashion->id,
                'name' => 'Nordic Trailrunner Vibram Zero-Drop Shoes',
                'slug' => 'nordic-trailrunner-vibram-zero-drop-shoes',
                'price' => 165.00,
                'sale_price' => 135.00,
                'stock_quantity' => 28,
                'sku' => 'LX-TR-01',
                'short_description' => 'Cordura ripstop upper with Vibram Megagrip lug outsole and wide ergonomic foot-shaped toe-box.',
                'description' => 'Experience natural grounded locomotion across wet rocks, asphalt, and mountain paths. Zero heel elevation aligns the spine naturally.',
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.76,
                'rating_count' => 38,
                'sales_count' => 112,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catFashion->id,
                'name' => 'Aviator Titanium Polarized Sunglasses',
                'slug' => 'aviator-titanium-polarized-sunglasses',
                'price' => 195.00,
                'sale_price' => 160.00,
                'stock_quantity' => 35,
                'sku' => 'LX-SGL-14',
                'short_description' => 'Ultra-thin Japanese beta-titanium wireframe with polarized anti-glare scratch resistant lenses.',
                'description' => 'Weighing only 18 grams, these precision sunglasses combine featherweight comfort with 100% UV400 optical clarity.',
                'images' => [
                    'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.90,
                'rating_count' => 41,
                'sales_count' => 165,
            ],

            // --- 3. Home & Workspace Living ---
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catHome->id,
                'name' => 'Komorebi Walnut Dual-Monitor Desk Shelf',
                'slug' => 'komorebi-walnut-dual-monitor-desk-shelf',
                'price' => 189.00,
                'sale_price' => 165.00,
                'stock_quantity' => 25,
                'sku' => 'LX-DSK-01',
                'short_description' => 'FSC-certified solid American black walnut with blasted aluminum legs and wool-felt tray.',
                'description' => 'Elevate displays to natural ergonomic eye level while maintaining clean cable management and storage underneath for keyboards and tablets.',
                'images' => [
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.93,
                'rating_count' => 54,
                'sales_count' => 210,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catHome->id,
                'name' => 'Aura Minimalist Dimmable Concrete Table Lamp',
                'slug' => 'aura-minimalist-dimmable-concrete-table-lamp',
                'price' => 135.00,
                'sale_price' => null,
                'stock_quantity' => 30,
                'sku' => 'LX-LMP-03',
                'short_description' => 'Hand-cast architectural concrete base with frosted opal glass globe and solid brass knurled rotary dimmer.',
                'description' => 'Warm 2400K ambient illumination for evening focus and tranquility. Cast from high-density micro-cement with felt protective base.',
                'images' => [
                    'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.87,
                'rating_count' => 42,
                'sales_count' => 140,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catHome->id,
                'name' => 'Nordic Matte Ceramic Pour-Over & Mug Set',
                'slug' => 'nordic-matte-ceramic-pour-over-mug-set',
                'price' => 68.00,
                'sale_price' => 55.00,
                'stock_quantity' => 45,
                'sku' => 'LX-CER-09',
                'short_description' => 'Wheel-thrown stoneware with textured unglazed exterior and food-safe silky matte interior glaze.',
                'description' => 'Craft your morning brew with tactile satisfaction. Optimized 60-degree cone ribbing promotes optimal water flow and coffee extraction.',
                'images' => [
                    'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.79,
                'rating_count' => 60,
                'sales_count' => 310,
            ],

            // --- 4. Beauty & Self Care ---
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBeauty->id,
                'name' => 'Lumina Botanical Restorative Face Serum',
                'slug' => 'lumina-botanical-restorative-face-serum',
                'price' => 78.00,
                'sale_price' => 62.00,
                'stock_quantity' => 55,
                'sku' => 'LX-SRM-01',
                'short_description' => 'Cold-pressed squalane, niacinamide 5%, and multi-molecular hyaluronic acid in violet glass UV bottle.',
                'description' => 'Deeply hydrates and restores skin lipid barrier with clean bio-compatible actives. Vegan, cruelty-free, and formulated without artificial fragrance.',
                'images' => [
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.92,
                'rating_count' => 78,
                'sales_count' => 420,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBeauty->id,
                'name' => 'Atlas Sandalwood & Amber Eau de Parfum',
                'slug' => 'atlas-sandalwood-amber-eau-de-parfum',
                'price' => 140.00,
                'sale_price' => null,
                'stock_quantity' => 25,
                'sku' => 'LX-FRG-02',
                'short_description' => 'Subtle woody fragrance with notes of cedarwood, cardamom, pink peppercorn, and warm amber resin.',
                'description' => 'A distinct olfactory statement that lingers without overpowering. Hand-bottled in minimalist smoked glass with magnetic cap.',
                'images' => [
                    'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.88,
                'rating_count' => 36,
                'sales_count' => 150,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBeauty->id,
                'name' => 'Kinoko Organic Facial Cleansing Balm',
                'slug' => 'kinoko-organic-facial-cleansing-balm',
                'price' => 46.00,
                'sale_price' => 39.00,
                'stock_quantity' => 40,
                'sku' => 'LX-BLM-03',
                'short_description' => 'Melt-away botanical balm infused with jojoba oil, matcha tea extract, and vitamin E.',
                'description' => 'Effortlessly dissolves waterproof SPF and impurities while leaving skin nourished, supple, and non-greasy.',
                'images' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.81,
                'rating_count' => 29,
                'sales_count' => 190,
            ],

            // --- 5. Sports & Outdoor ---
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catSports->id,
                'name' => 'HydroTitan Vacuum Insulated Flask (750ml)',
                'slug' => 'hydrotitan-vacuum-insulated-flask-750ml',
                'price' => 54.00,
                'sale_price' => 45.00,
                'stock_quantity' => 70,
                'sku' => 'APX-BOT-07',
                'short_description' => 'Aerospace Grade 1 Titanium double-wall construction keeping liquids icy cold for 36 hours.',
                'description' => 'Pure titanium delivers zero metallic taste, weighs half as much as stainless steel, and will last a lifetime of mountain expeditions.',
                'images' => [
                    'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.94,
                'rating_count' => 88,
                'sales_count' => 380,
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catSports->id,
                'name' => 'Zenith High-Density Natural Rubber Yoga Mat',
                'slug' => 'zenith-high-density-natural-rubber-yoga-mat',
                'price' => 95.00,
                'sale_price' => null,
                'stock_quantity' => 35,
                'sku' => 'APX-MAT-01',
                'short_description' => '5mm dense tree-rubber cushion with laser-etched alignment grid and sweat-activated non-slip surface.',
                'description' => 'Stay fully grounded in every posture. Biodegradable non-toxic materials with exceptional joint support and zero edge curling.',
                'images' => [
                    'https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.85,
                'rating_count' => 34,
                'sales_count' => 125,
            ],

            // --- 6. Bags & EDC Carry ---
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $catBags->id,
                'name' => 'Apex Commuter 24L Ballistic Tech Backpack',
                'slug' => 'apex-commuter-24l-ballistic-tech-backpack',
                'price' => 175.00,
                'sale_price' => 155.00,
                'stock_quantity' => 35,
                'sku' => 'APX-BAG-24',
                'short_description' => 'Weatherproof Cordura 1680D with dedicated 16-inch suspended laptop vault and magnetic Fidlock buckles.',
                'description' => 'Structured architectural profile that stands upright on its own. Features hidden passport pocket, ergonomic EVA shoulder straps, and luggage pass-through.',
                'images' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.93,
                'rating_count' => 74,
                'sales_count' => 290,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBags->id,
                'name' => 'Nomad Weatherproof Crossbody Tech Sling (4L)',
                'slug' => 'nomad-weatherproof-crossbody-tech-sling-4l',
                'price' => 89.00,
                'sale_price' => 75.00,
                'stock_quantity' => 50,
                'sku' => 'LX-SLG-04',
                'short_description' => 'X-Pac sailcloth waterproof fabric with YKK Aquaguard zips and quick-release magnetic strap.',
                'description' => 'The ultimate everyday compact carry for phone, keys, sunglasses, compact camera, and portable charger.',
                'images' => [
                    'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.86,
                'rating_count' => 62,
                'sales_count' => 310,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBags->id,
                'name' => 'Horween Full-Grain Leather Minimalist Cardholder',
                'slug' => 'horween-full-grain-leather-minimalist-cardholder',
                'price' => 55.00,
                'sale_price' => null,
                'stock_quantity' => 45,
                'sku' => 'LX-WLT-01',
                'short_description' => 'Hand-stitched vegetable-tanned Dublin leather that patinas beautifully with daily pocket carry.',
                'description' => 'Holds 6-8 cards plus folded cash in an ultra-slim 5mm profile. Edge beveling and burnishing done by master artisans.',
                'images' => [
                    'https://images.unsplash.com/photo-1627123424574-724758594e93?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.91,
                'rating_count' => 48,
                'sales_count' => 220,
            ],

            // --- 7. Books & Stationery ---
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBooks->id,
                'name' => 'Solid Brass Precision Machined Rollerball Pen',
                'slug' => 'solid-brass-precision-machined-rollerball-pen',
                'price' => 72.00,
                'sale_price' => 59.00,
                'stock_quantity' => 60,
                'sku' => 'LX-PEN-01',
                'short_description' => 'Single-billet solid raw brass with Schmidt ceramic rollerball refill and balanced center of gravity.',
                'description' => 'A writing instrument built to outlast generations. Will develop a rich golden patina over months of note-taking and sketches.',
                'images' => [
                    'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => true,
                'rating_avg' => 4.95,
                'rating_count' => 55,
                'sales_count' => 240,
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $catBooks->id,
                'name' => 'Swiss Graphic Typography & Grid Systems Monograph',
                'slug' => 'swiss-graphic-typography-grid-systems-monograph',
                'price' => 65.00,
                'sale_price' => null,
                'stock_quantity' => 30,
                'sku' => 'LX-BOK-09',
                'short_description' => 'Linen hardcover 320-page retrospective celebrating modernist grid design and editorial typography.',
                'description' => 'Essential library volume for design engineers, typographers, and architects. Printed on heavy 170gsm archival Munken paper.',
                'images' => [
                    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80',
                ],
                'is_featured' => false,
                'rating_avg' => 4.98,
                'rating_count' => 43,
                'sales_count' => 175,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'slug' => $p['slug'],
                    'vendor_id' => $p['vendor_id'],
                    'category_id' => $p['category_id'],
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'sale_price' => $p['sale_price'],
                    'stock_quantity' => $p['stock_quantity'],
                    'short_description' => $p['short_description'],
                    'description' => $p['description'],
                    'images' => $p['images'],
                    'is_active' => true,
                    'is_featured' => $p['is_featured'],
                    'status' => 'approved',
                    'rating_avg' => $p['rating_avg'],
                    'rating_count' => $p['rating_count'],
                    'sales_count' => $p['sales_count'],
                    'views_count' => rand(1200, 5400),
                ]
            );
        }
    }
}
