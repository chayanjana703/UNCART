<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\PromotionalBanner;
use App\Models\StoreSetting;
use App\Models\ActivityLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@shopicart.com',
            'phone' => '+1 (555) 019-2831',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
            'bio' => 'Head of platform infrastructure, security & vendor compliance at ShopiCart.',
            'two_factor_enabled' => false,
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        $vendor1User = User::create([
            'name' => 'Alex Vance (Apex Tech)',
            'email' => 'vendor@shopicart.com',
            'phone' => '+1 (555) 302-8492',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
            'bio' => 'Hardware engineer & founder of Apex Tech Innovations. Certified premium seller.',
            'two_factor_enabled' => false,
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        $vendor2User = User::create([
            'name' => 'Elena Rostova (Luxe Lifestyle)',
            'email' => 'vendor2@shopicart.com',
            'phone' => '+1 (555) 891-2304',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80',
            'bio' => 'Curator of minimalist Scandinavian design and artisanal workspace accessories.',
            'two_factor_enabled' => false,
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Chayan Jana',
            'email' => 'customer@shopicart.com',
            'phone' => '+91 98765 43210',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&auto=format&fit=crop&q=80',
            'bio' => 'Designer and design engineer passionate about high-fidelity physical & digital products.',
            'two_factor_enabled' => false,
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        // 2. Create Vendors
        $vendor1 = Vendor::create([
            'user_id' => $vendor1User->id,
            'shop_name' => 'Apex Tech Innovations',
            'shop_slug' => 'apex-tech',
            'shop_description' => 'Flagship audio gear, mechanical peripherals, and high-performance tactile consumer electronics.',
            'shop_logo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&auto=format&fit=crop&q=80',
            'shop_banner' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1400&auto=format&fit=crop&q=80',
            'phone' => '+1 (555) 302-8492',
            'email' => 'contact@apextech.io',
            'address' => '742 Evergreen Terrace, Cupertino, CA 95014',
            'kyc_status' => 'verified',
            'bank_name' => 'Silicon Valley Bank',
            'account_number' => '409281729012',
            'ifsc_code' => 'SVBL000412',
            'commission_rate' => 8.50,
            'status' => 'active',
            'return_policy' => '30 days unconditional money-back guarantee. Paid return label provided.',
            'shipping_policy' => 'All orders processed within 12 hours. Express carrier tracked shipping worldwide.',
            'total_payouts' => 24890.00,
        ]);

        $vendor2 = Vendor::create([
            'user_id' => $vendor2User->id,
            'shop_name' => 'Luxe Living & Studio',
            'shop_slug' => 'luxe-living',
            'shop_description' => 'Architectural desk furniture, vegetable-tanned leather goods, and refined lifestyle goods.',
            'shop_logo' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=400&auto=format&fit=crop&q=80',
            'shop_banner' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1400&auto=format&fit=crop&q=80',
            'phone' => '+1 (555) 891-2304',
            'email' => 'hello@luxeliving.co',
            'address' => '108 King Street, Soho, New York, NY 10012',
            'kyc_status' => 'verified',
            'bank_name' => 'JPMorgan Chase',
            'account_number' => '889210041293',
            'ifsc_code' => 'CHAS000108',
            'commission_rate' => 10.00,
            'status' => 'active',
            'return_policy' => '45 days trial period on all ergonomic studio furniture.',
            'shipping_policy' => 'White-glove courier delivery for furniture; 2-day delivery on studio accessories.',
            'total_payouts' => 17450.00,
        ]);

        // 3. Customer Addresses
        Address::create([
            'user_id' => $customer->id,
            'name' => 'Chayan Jana',
            'phone' => '+91 98765 43210',
            'type' => 'shipping',
            'address_line1' => 'Suite 404, Tech Park Boulevard',
            'address_line2' => 'Sector V, Bidhannagar',
            'city' => 'Kolkata',
            'state' => 'West Bengal',
            'postal_code' => '700091',
            'country' => 'India',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $customer->id,
            'name' => 'Chayan Jana (Design Studio)',
            'phone' => '+91 98765 43210',
            'type' => 'billing',
            'address_line1' => 'Tower B, 14th Floor, Skyline Studios',
            'address_line2' => 'Lower Parel',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400013',
            'country' => 'India',
            'is_default' => false,
        ]);

        // 4. Categories
        $categoriesData = [
            [
                'name' => 'Audio & Acoustic Gear',
                'slug' => 'audio-gadgets',
                'description' => 'Precision transducers, noise-canceling headphones, and studio monitors.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                'icon' => 'headphones',
            ],
            [
                'name' => 'Wearable Tech & Watches',
                'slug' => 'wearable-tech',
                'description' => 'OLED smartwatches, mechanical precision timepieces, and fitness trackers.',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80',
                'icon' => 'watch',
            ],
            [
                'name' => 'Ergonomic Workspace & Living',
                'slug' => 'home-living',
                'description' => 'Solid walnut desks, magnetic cable managers, and ambient lighting.',
                'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&auto=format&fit=crop&q=80',
                'icon' => 'armchair',
            ],
            [
                'name' => 'Computing & Keyboards',
                'slug' => 'computing-keyboards',
                'description' => 'Custom mechanical keyboards, CNC aluminum cases, and wireless precision mice.',
                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80',
                'icon' => 'keyboard',
            ],
            [
                'name' => 'Modern Apparel & Textiles',
                'slug' => 'apparel-textiles',
                'description' => 'Heavyweight cotton tees, waterproof technical outerwear, and merino wool caps.',
                'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80',
                'icon' => 'shirt',
            ],
            [
                'name' => 'Bags & EDC Carry',
                'slug' => 'bags-edc',
                'description' => 'Cordura ballistic backpacks, weatherproof tech slings, and leather cardholders.',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80',
                'icon' => 'briefcase',
            ],
            [
                'name' => 'Minimalist Footwear',
                'slug' => 'footwear',
                'description' => 'Vibram-soled sneakers, full-grain chelsea boots, and indoor wool slippers.',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&auto=format&fit=crop&q=80',
                'icon' => 'footprints',
            ],
            [
                'name' => 'Optics & Photography',
                'slug' => 'optics-photography',
                'description' => 'Prime lenses, rangefinder bodies, and precision milled camera straps.',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                'icon' => 'camera',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $idx => $cat) {
            $categories[$cat['slug']] = Category::create([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => $cat['description'],
                'image' => $cat['image'],
                'icon' => $cat['icon'],
                'is_active' => true,
                'sort_order' => $idx + 1,
            ]);
        }

        // 5. Products Catalog
        $productsData = [
            [
                'vendor' => $vendor1,
                'cat' => 'audio-gadgets',
                'name' => 'Aether Pro Spatial Wireless Headphones',
                'slug' => 'aether-pro-spatial-wireless-headphones',
                'price' => 389.00,
                'sale_price' => 329.00,
                'stock' => 45,
                'sku' => 'APX-ATH-01',
                'short' => 'Bespoke 40mm beryllium drivers with lossless Bluetooth 5.4, ultra-low latency, and hybrid ANC.',
                'desc' => "Forged from aircraft-grade anodized aluminum and lambskin memory foam earcups, the Aether Pro represents the pinnacle of personal acoustic engineering. Featuring dual DSP processors with real-time room calibration and 45-hour continuous battery life.",
                'images' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Frequency Response' => '5 Hz – 45,000 Hz',
                    'Driver Type' => '40mm Custom Beryllium Dome',
                    'Battery Life' => '45 Hours (ANC On)',
                    'Weight' => '284g',
                    'Connectivity' => 'Bluetooth 5.4, USB-C Lossless, 3.5mm Analog',
                    'Warranty' => '2 Years Global Manufacturer Coverage',
                ],
                'featured' => true,
                'rating_avg' => 4.95,
                'rating_count' => 128,
                'sales' => 342,
            ],
            [
                'vendor' => $vendor1,
                'cat' => 'computing-keyboards',
                'name' => 'Vanguard 75% CNC Mechanical Keyboard',
                'slug' => 'vanguard-75-cnc-mechanical-keyboard',
                'price' => 245.00,
                'sale_price' => 199.00,
                'stock' => 18,
                'sku' => 'APX-KB-75',
                'short' => 'Gasket-mounted 6063 aluminum chassis with custom lubricated linear switches and PBT keycaps.',
                'desc' => "Engineered for uncompromising typing feel and deep acoustic thock. Features tri-mode 2.4GHz/Bluetooth/Type-C connectivity, hot-swappable PCB, FR4 plate, and RGB per-key backlighting with customizable VIA firmware.",
                'images' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1595225476474-87563907a212?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Layout' => '75% Compact (82 Keys)',
                    'Mounting Style' => 'Poron Gasket Mount',
                    'Switches' => 'Factory Pre-Lubed Linear Jade (45g)',
                    'Chassis' => 'CNC Milled 6063 Aluminum',
                    'Battery' => '4,000 mAh Rechargeable Li-Ion',
                    'Warranty' => '1 Year Replacement Guarantee',
                ],
                'featured' => true,
                'rating_avg' => 4.88,
                'rating_count' => 84,
                'sales' => 219,
            ],
            [
                'vendor' => $vendor2,
                'cat' => 'wearable-tech',
                'name' => 'Chronos Minimalist Sapphire Automatic Watch',
                'slug' => 'chronos-minimalist-sapphire-automatic-watch',
                'price' => 520.00,
                'sale_price' => 460.00,
                'stock' => 12,
                'sku' => 'LX-WAT-09',
                'short' => 'Japanese automatic movement, anti-reflective domed sapphire crystal, and vegetable-tanned Horween strap.',
                'desc' => "Subtle perfection on the wrist. Clean matte black dial with hairline indices, exhibition case back revealing the 21-jewel self-winding caliber, and 50-meter water resistance.",
                'images' => [
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Case Diameter' => '39mm (9.8mm thickness)',
                    'Glass' => 'Domed Scratch-Resistant Sapphire',
                    'Movement' => 'Miyota 9015 Automatic (28,800 bph)',
                    'Power Reserve' => '42 Hours',
                    'Water Resistance' => '5 ATM / 50 Meters',
                    'Strap' => '20mm Italian Full-Grain Leather',
                ],
                'featured' => true,
                'rating_avg' => 4.92,
                'rating_count' => 67,
                'sales' => 140,
            ],
            [
                'vendor' => $vendor2,
                'cat' => 'home-living',
                'name' => 'Komorebi Walnut Desk Shelf & Dual Monitor Riser',
                'slug' => 'komorebi-walnut-desk-shelf-dual-monitor-riser',
                'price' => 189.00,
                'sale_price' => null,
                'stock' => 24,
                'sku' => 'LX-DSK-01',
                'short' => 'Solid American black walnut with blasted aluminum legs and built-in felt-lined utility tray.',
                'desc' => "Reclaim your desktop clarity. The Komorebi desk shelf elevates dual displays to natural ergonomic eye-level while creating an organized storage corridor underneath for keyboards, audio interfaces, and notebooks.",
                'images' => [
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Dimensions' => '115cm (L) x 23cm (W) x 11cm (H)',
                    'Material' => 'FSC-Certified Solid American Walnut',
                    'Legs' => 'Matte Anodized Aluminum with Cork Feet',
                    'Max Weight Load' => '45 kg (100 lbs)',
                    'Finish' => 'Zero-VOC Natural Hardwax Oil',
                ],
                'featured' => false,
                'rating_avg' => 4.79,
                'rating_count' => 52,
                'sales' => 188,
            ],
            [
                'vendor' => $vendor1,
                'cat' => 'audio-gadgets',
                'name' => 'SonicBeam Magnetic Desktop Hi-Fi DAC & Amp',
                'slug' => 'sonicbeam-magnetic-desktop-hi-fi-dac-amp',
                'price' => 165.00,
                'sale_price' => 139.00,
                'stock' => 30,
                'sku' => 'APX-DAC-03',
                'short' => 'ES9038Q2M digital-to-analog converter delivering up to 32-bit/768kHz DSD512 studio clarity.',
                'desc' => "Transform your desktop listening station into an authentic mastering room. High-current output drives demanding planar magnetic headphones effortlessly with near-zero harmonic distortion.",
                'images' => [
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'DAC Chip' => 'Dual ESS Sabre ES9038Q2M',
                    'Resolution' => 'PCM up to 768kHz / DSD up to DSD512',
                    'Outputs' => '4.4mm Balanced + 6.35mm Single-Ended',
                    'THD+N' => '0.00015%',
                    'Chassis' => 'Solid Billet Aluminum',
                ],
                'featured' => false,
                'rating_avg' => 4.85,
                'rating_count' => 41,
                'sales' => 97,
            ],
            [
                'vendor' => $vendor2,
                'cat' => 'bags-edc',
                'name' => 'Apex Commuter Weatherproof Tech Backpack 24L',
                'slug' => 'apex-commuter-weatherproof-tech-backpack-24l',
                'price' => 175.00,
                'sale_price' => 149.00,
                'stock' => 35,
                'sku' => 'LX-BAG-24',
                'short' => 'X-Pac sailcloth fabric with YKK Aquaguard zippers and dedicated suspended 16" laptop pod.',
                'desc' => "Built to navigate torrential city downpours and international transit gates. Engineered with magnetic Fidlock sternum buckles, hidden passport compartment, and luggage pass-through.",
                'images' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Volume' => '24 Liters',
                    'Shell' => 'VX21 Multi-layer Sailcloth laminate',
                    'Laptop Pod' => 'Suspended foam padded up to 16.2" MacBook Pro',
                    'Hardware' => 'Fidlock V-Buckle & Duraflex clips',
                    'Weight' => '1.1 kg',
                ],
                'featured' => true,
                'rating_avg' => 4.90,
                'rating_count' => 95,
                'sales' => 310,
            ],
            [
                'vendor' => $vendor2,
                'cat' => 'footwear',
                'name' => 'Nordic Trailrunner Vibram Zero-Drop Sneakers',
                'slug' => 'nordic-trailrunner-vibram-zero-drop-sneakers',
                'price' => 160.00,
                'sale_price' => 135.00,
                'stock' => 20,
                'sku' => 'LX-SNEAK-01',
                'short' => 'Breathable ripstop upper with Vibram Megagrip lug outsole and wide ergonomic toe-box.',
                'desc' => "Natural movement meets technical all-terrain grip. Zero heel-to-toe drop encourages healthy posture and stability across damp cobblestones, forest trails, and city streets.",
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Drop' => '0mm (Zero-Drop)',
                    'Outsole' => 'Vibram Megagrip with 4mm directional lugs',
                    'Upper' => 'Recycled Ripstop Cordura with TPU overlays',
                    'Insole' => 'Ortholite Eco-hybrid foam',
                ],
                'featured' => false,
                'rating_avg' => 4.76,
                'rating_count' => 38,
                'sales' => 112,
            ],
            [
                'vendor' => $vendor1,
                'cat' => 'optics-photography',
                'name' => 'Lumen 35mm F/1.4 Full-Frame Manual Prime Lens',
                'slug' => 'lumen-35mm-f1-4-full-frame-manual-prime-lens',
                'price' => 499.00,
                'sale_price' => null,
                'stock' => 14,
                'sku' => 'APX-OPT-35',
                'short' => 'Ultra-fast wide-angle prime featuring 11 elements in 9 groups with creamy bokeh and cinematic flare.',
                'desc' => "Crafted for visual storytellers who savor tactile focus rings and organic optical rendition. Features stepless clicked/de-clicked aperture ring and all-metal weather sealed barrel.",
                'images' => [
                    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=1000&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=1000&auto=format&fit=crop&q=80',
                ],
                'specs' => [
                    'Focal Length' => '35mm (Full Frame)',
                    'Aperture Range' => 'f/1.4 – f/16 (12 Rounded Blades)',
                    'Focus' => 'Manual Focus with 160° Precision Throw',
                    'Mount' => 'Available for Sony E, Leica L, and Fuji X',
                    'Filter Thread' => '62mm',
                ],
                'featured' => false,
                'rating_avg' => 4.94,
                'rating_count' => 29,
                'sales' => 74,
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $cat = $categories[$pData['cat']];
            $prod = Product::create([
                'vendor_id' => $pData['vendor']->id,
                'category_id' => $cat->id,
                'name' => $pData['name'],
                'slug' => $pData['slug'],
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'],
                'stock_quantity' => $pData['stock'],
                'sku' => $pData['sku'],
                'short_description' => $pData['short'],
                'description' => $pData['desc'],
                'images' => $pData['images'],
                'specifications' => $pData['specs'],
                'is_active' => true,
                'is_featured' => $pData['featured'],
                'status' => 'approved',
                'rating_avg' => $pData['rating_avg'],
                'rating_count' => $pData['rating_count'],
                'views_count' => rand(1500, 4800),
                'sales_count' => $pData['sales'],
            ]);
            $createdProducts[] = $prod;
        }

        // 6. Product Reviews & Ratings
        $firstProduct = $createdProducts[0];
        ProductReview::create([
            'product_id' => $firstProduct->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'Incredible spatial imaging and build quality',
            'comment' => "Hands down the most comfortable ANC headphones I have ever worn for 8-hour design sessions. The aluminum earcups have a silky feel, the clamping force is tuned to perfection, and the soundstage is astonishingly wide.",
            'helpful_votes' => 38,
            'unhelpful_votes' => 1,
            'vendor_reply' => "Thank you Chayan! We spent over 18 months tuning the beryllium driver dome for that exact separation. Thrilled you are enjoying them!",
            'vendor_replied_at' => now()->subDays(2),
            'status' => 'approved',
        ]);

        ProductReview::create([
            'product_id' => $createdProducts[1]->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'The acoustic thock is unbeatable',
            'comment' => "The gasket mounting gives a cushiony, responsive bounce to every keystroke. Heavy aluminum weight keeps it completely anchored on my desk.",
            'helpful_votes' => 19,
            'unhelpful_votes' => 0,
            'status' => 'approved',
        ]);

        // 7. Coupons
        Coupon::create([
            'code' => 'WELCOME10',
            'discount_type' => 'percent',
            'discount_value' => 10.00,
            'min_spend' => 50.00,
            'max_discount' => 50.00,
            'usage_limit' => 500,
            'used_count' => 42,
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SUPER25',
            'discount_type' => 'fixed',
            'discount_value' => 25.00,
            'min_spend' => 150.00,
            'max_discount' => null,
            'usage_limit' => 200,
            'used_count' => 18,
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLASH50',
            'discount_type' => 'percent',
            'discount_value' => 50.00,
            'min_spend' => 200.00,
            'max_discount' => 100.00,
            'usage_limit' => 50,
            'used_count' => 12,
            'expires_at' => now()->addDays(5),
            'is_active' => true,
        ]);

        // 8. Promotional Banners
        PromotionalBanner::create([
            'title' => 'Spatial Acoustics Re-Imagined',
            'subtitle' => 'Lossless transmission, 45-hour battery, and aircraft aluminum chassis.',
            'badge' => 'New Release 2026',
            'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=1600&auto=format&fit=crop&q=80',
            'link' => '/product/aether-pro-spatial-wireless-headphones',
            'button_text' => 'Experience Aether',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        PromotionalBanner::create([
            'title' => 'Architectural Studio Workspaces',
            'subtitle' => 'Solid American walnut organizers crafted for pure focus.',
            'badge' => 'Curated Selection',
            'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1600&auto=format&fit=crop&q=80',
            'link' => '/shop?category=home-living',
            'button_text' => 'Elevate Your Desk',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 9. Orders and Real-Time Tracking
        $shippingAddr = [
            'name' => 'Chayan Jana',
            'phone' => '+91 98765 43210',
            'address_line1' => 'Suite 404, Tech Park Boulevard',
            'address_line2' => 'Sector V, Bidhannagar',
            'city' => 'Kolkata',
            'state' => 'West Bengal',
            'postal_code' => '700091',
            'country' => 'India',
        ];

        // Completed Order #1
        $order1 = Order::create([
            'order_number' => 'ORD-94821',
            'user_id' => $customer->id,
            'subtotal' => 329.00,
            'discount_amount' => 32.90,
            'shipping_amount' => 0.00,
            'tax_amount' => 25.17,
            'total_amount' => 321.27,
            'coupon_code' => 'WELCOME10',
            'shipping_method' => 'Express Courier (Free)',
            'shipping_address' => $shippingAddr,
            'billing_address' => $shippingAddr,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-90248192-STRIPE',
            'order_status' => 'delivered',
            'tracking_number' => 'EXP-849204128US',
            'carrier_name' => 'DHL Global Express',
            'notes' => 'Please leave at the front reception desk.',
            'delivered_at' => now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $firstProduct->id,
            'vendor_id' => $vendor1->id,
            'product_name' => $firstProduct->name,
            'product_image' => $firstProduct->featured_image,
            'price' => 329.00,
            'quantity' => 1,
            'total' => 329.00,
            'options' => ['Color' => 'Matte Obsidian Black', 'Edition' => 'Wireless Studio'],
            'vendor_status' => 'delivered',
        ]);

        // Active Order #2 (Shipped / Out for delivery)
        $order2 = Order::create([
            'order_number' => 'ORD-73910',
            'user_id' => $customer->id,
            'subtotal' => 199.00,
            'discount_amount' => 0.00,
            'shipping_amount' => 15.00,
            'tax_amount' => 16.92,
            'total_amount' => 230.92,
            'shipping_method' => 'Next-Day Air',
            'shipping_address' => $shippingAddr,
            'billing_address' => $shippingAddr,
            'payment_method' => 'upi',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-77821092-UPI',
            'order_status' => 'out_for_delivery',
            'tracking_number' => 'FEDEX-910284721',
            'carrier_name' => 'FedEx Express Direct',
            'notes' => 'Call on mobile before arrival.',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[1]->id,
            'vendor_id' => $vendor1->id,
            'product_name' => $createdProducts[1]->name,
            'product_image' => $createdProducts[1]->featured_image,
            'price' => 199.00,
            'quantity' => 1,
            'total' => 199.00,
            'options' => ['Switches' => 'Linear Jade (Lubed)', 'Keycaps' => 'Dual-Tone Dark Slate'],
            'vendor_status' => 'shipped',
        ]);

        // 10. Notifications
        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Package Delivered Successfully',
            'message' => 'Your order #ORD-94821 (Aether Pro Spatial Headphones) was delivered with signature confirmation.',
            'type' => 'order',
            'link' => '/orders/ORD-94821',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Courier Out For Delivery',
            'message' => 'FedEx has picked up #ORD-73910 and is scheduled to arrive at your door today before 6:00 PM.',
            'type' => 'order',
            'link' => '/orders/ORD-73910/track',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $customer->id,
            'title' => 'Weekend Flash Sale Is Live!',
            'message' => 'Use promo code FLASH50 to get up to 50% discount on select studio living gear.',
            'type' => 'promotion',
            'link' => '/shop?category=home-living',
            'is_read' => true,
        ]);

        // 11. Store Settings
        StoreSetting::set('store_name', 'SHOPICART');
        StoreSetting::set('store_tagline', 'The Precision Commerce Platform');
        StoreSetting::set('store_email', 'contact@shopicart.com');
        StoreSetting::set('store_phone', '+1 (800) 892-0199');
        StoreSetting::set('store_address', '100 Silicon Way, San Francisco, CA 94107');
        StoreSetting::set('currency_symbol', '$');
        StoreSetting::set('currency_code', 'USD');
        StoreSetting::set('tax_rate', '8.5');
        StoreSetting::set('shipping_standard_rate', '12.00');
        StoreSetting::set('shipping_express_rate', '25.00');
        StoreSetting::set('free_shipping_threshold', '99.00');

        // 12. Activity Log
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'Platform Initialized',
            'description' => 'System seeded with verified vendors, catalog, and secure payment pathways.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
    }
}
