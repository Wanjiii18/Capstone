<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Karenderia;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Hash;

echo "🏪 Creating Mock Karenderias Near Talisay City, South Town Center...\n\n";

// Talisay City locations around South Town Center area
$locations = [
    // Near South Town Center Mall
    [
        'name' => 'Lola Meding\'s Kusina',
        'business_name' => 'Meding\'s Traditional Cooking',
        'address' => 'Near South Town Center Mall, Talisay City, Cebu',
        'latitude' => 10.2442,
        'longitude' => 123.8492,
        'phone' => '+639171234567',
        'description' => 'Traditional Cebuano dishes right beside South Town Center'
    ],
    [
        'name' => 'Kuya Joel\'s Carinderia',
        'business_name' => 'Joel\'s Lutong Bahay',
        'address' => 'Tabunok Road, Talisay City, Cebu',
        'latitude' => 10.2458,
        'longitude' => 123.8512,
        'phone' => '+639182345678',
        'description' => 'Budget-friendly meals for shoppers and locals'
    ],
    
    // Tabunok Area
    [
        'name' => 'Tita Rosa\'s Eatery',
        'business_name' => 'Rosa\'s Cebuano Kitchen',
        'address' => 'Tabunok Main Road, Talisay City, Cebu',
        'latitude' => 10.2475,
        'longitude' => 123.8535,
        'phone' => '+639193456789',
        'description' => 'Authentic Cebuano flavors in Tabunok since 1998'
    ],
    [
        'name' => 'Nanay Linda\'s Karinderya',
        'business_name' => 'Linda\'s Home Cooking',
        'address' => 'Tabunok Central, Talisay City, Cebu',
        'latitude' => 10.2495,
        'longitude' => 123.8548,
        'phone' => '+639204567890',
        'description' => 'Home-style cooking that tastes like Nanay\'s recipe'
    ],
    
    // Poblacion Area
    [
        'name' => 'Manoy Eddie\'s Turo-Turo',
        'business_name' => 'Eddie\'s Point & Eat',
        'address' => 'Poblacion, Talisay City, Cebu',
        'latitude' => 10.2515,
        'longitude' => 123.8425,
        'phone' => '+639215678901',
        'description' => 'Popular turo-turo in the heart of Talisay Poblacion'
    ],
    [
        'name' => 'Aling Nene\'s Kitchen',
        'business_name' => 'Nene\'s Comfort Food',
        'address' => 'Poblacion Main Street, Talisay City, Cebu',
        'latitude' => 10.2532,
        'longitude' => 123.8445,
        'phone' => '+639226789012',
        'description' => 'Comfort food that brings back childhood memories'
    ],
    
    // Lagtang Area
    [
        'name' => 'Kuya Ben\'s Lutong Bahay',
        'business_name' => 'Ben\'s Family Restaurant',
        'address' => 'Lagtang, Talisay City, Cebu',
        'latitude' => 10.2385,
        'longitude' => 123.8375,
        'phone' => '+639237890123',
        'description' => 'Family restaurant serving generous portions'
    ],
    [
        'name' => 'Tita Carmen\'s Carinderia',
        'business_name' => 'Carmen\'s Daily Meals',
        'address' => 'Lagtang Highway, Talisay City, Cebu',
        'latitude' => 10.2402,
        'longitude' => 123.8398,
        'phone' => '+639248901234',
        'description' => 'Fresh daily meals for busy commuters'
    ],
    
    // Bulacao Area (nearby)
    [
        'name' => 'Mang Tony\'s Seaside Eatery',
        'business_name' => 'Tony\'s Coastal Kitchen',
        'address' => 'Bulacao, Talisay City, Cebu',
        'latitude' => 10.2325,
        'longitude' => 123.8315,
        'phone' => '+639259012345',
        'description' => 'Fresh seafood and local favorites by the sea'
    ],
    [
        'name' => 'Ate Mely\'s Karinderya',
        'business_name' => 'Mely\'s Neighborhood Kitchen',
        'address' => 'Bulacao Main Road, Talisay City, Cebu',
        'latitude' => 10.2345,
        'longitude' => 123.8335,
        'phone' => '+639260123456',
        'description' => 'Neighborhood favorite for quick and tasty meals'
    ]
];

// Popular Cebuano and Filipino dishes
$menuItems = [
    // Cebuano Specialties
    'Lechon Cebu' => ['price' => 250, 'description' => 'World-famous Cebuano roasted pig with crispy skin'],
    'Puso (Hanging Rice)' => ['price' => 15, 'description' => 'Traditional Cebuano rice cooked in coconut leaves'],
    'Linarang na Isda' => ['price' => 180, 'description' => 'Fresh fish soup with ginger and tomatoes'],
    'Humba' => ['price' => 160, 'description' => 'Cebuano braised pork belly with fermented black beans'],
    'Balbacua' => ['price' => 140, 'description' => 'Rich beef skin and bone marrow stew'],
    'Sutukil' => ['price' => 200, 'description' => 'Fresh seafood cooked sugba, tula, or kilaw style'],
    
    // Filipino Classics
    'Adobong Manok' => ['price' => 120, 'description' => 'Classic Filipino chicken adobo'],
    'Sinigang na Baboy' => ['price' => 150, 'description' => 'Sour pork soup with kangkong'],
    'Kare-Kare' => ['price' => 180, 'description' => 'Oxtail stew in peanut sauce'],
    'Pancit Canton' => ['price' => 100, 'description' => 'Stir-fried noodles with vegetables'],
    'Tinolang Manok' => ['price' => 125, 'description' => 'Chicken soup with green papaya and malunggay'],
    'Beef Caldereta' => ['price' => 170, 'description' => 'Beef stew in tomato sauce'],
    'Pinakbet' => ['price' => 105, 'description' => 'Mixed vegetables with bagoong'],
    'Bicol Express' => ['price' => 145, 'description' => 'Spicy pork in coconut milk'],
    'Laing' => ['price' => 110, 'description' => 'Taro leaves in coconut milk'],
    
    // Seafood (popular in coastal Talisay)
    'Grilled Bangus' => ['price' => 135, 'description' => 'Grilled milkfish with soy-calamansi sauce'],
    'Sweet & Sour Fish' => ['price' => 155, 'description' => 'Fried fish in sweet and sour sauce'],
    'Ginataang Hipon' => ['price' => 175, 'description' => 'Shrimp cooked in coconut milk']
];

$categories = ['Main_course', 'Soup', 'Appetizer', 'Side_dish', 'Seafood'];

foreach ($locations as $index => $location) {
    try {
        echo "🏪 Creating karenderia: {$location['name']}...\n";
        
        // Create owner user
        $ownerEmail = "talisay_owner" . ($index + 1) . "@kaplato.com";
        $ownerName = explode("'s", $location['name'])[0]; // Extract the name part
        if (strpos($ownerName, ' ') !== false) {
            $ownerName = explode(' ', $ownerName)[1]; // Get the actual name after title
        }
        
        $user = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => $ownerName,
                'password' => Hash::make('talisay123'),
                'role' => 'karenderia_owner',
                'username' => strtolower(str_replace(' ', '', $ownerName)) . ($index + 1),
                'phone' => $location['phone'],
                'address' => $location['address']
            ]
        );
        
        // Create karenderia
        $karenderia = Karenderia::firstOrCreate(
            ['name' => $location['name']],
            [
                'business_name' => $location['business_name'],
                'owner_id' => $user->id,
                'address' => $location['address'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'phone' => $location['phone'],
                'description' => $location['description'],
                'status' => 'active',
                'opening_time' => '06:00:00',
                'closing_time' => '21:00:00',
                'delivery_fee' => rand(25, 60), // Slightly higher for provincial area
                'minimum_order' => rand(150, 250), // Adjusted for local market
                'is_open' => true,
                'rating' => round(rand(38, 50) / 10, 1), // 3.8 to 5.0 rating
                'total_reviews' => rand(10, 80)
            ]
        );
        
        echo "   ✅ Created karenderia: {$karenderia->name}\n";
        echo "   📍 Location: {$karenderia->latitude}, {$karenderia->longitude}\n";
        echo "   👤 Owner: {$user->name} ({$ownerEmail})\n";
        
        // Add 6-10 menu items per karenderia (more variety for testing)
        $selectedItems = array_rand($menuItems, rand(6, 10));
        if (!is_array($selectedItems)) {
            $selectedItems = [$selectedItems];
        }
        
        foreach ($selectedItems as $itemKey) {
            $itemName = array_keys($menuItems)[$itemKey];
            $itemData = $menuItems[$itemName];
            
            // Price adjustment for Talisay market
            $adjustedPrice = $itemData['price'] + rand(-30, 20);
            if ($adjustedPrice < 50) $adjustedPrice = 50; // Minimum price
            
            $menuItem = MenuItem::firstOrCreate(
                [
                    'karenderia_id' => $karenderia->id,
                    'name' => $itemName
                ],
                [
                    'description' => $itemData['description'],
                    'price' => $adjustedPrice,
                    'cost' => round($adjustedPrice * 0.65), // 65% of price as cost
                    'category' => $categories[array_rand($categories)],
                    'image_url' => null,
                    'is_available' => true,
                    'prep_time' => rand(15, 35),
                    'ingredients' => json_encode(['main ingredient', 'spices', 'vegetables', 'seasonings']),
                    'allergens' => json_encode(['soy', 'seafood']),
                    'spice_level' => rand(1, 4), // Cebuanos like it spicy
                    'is_vegetarian' => in_array($itemName, ['Pinakbet', 'Laing', 'Puso (Hanging Rice)']),
                    'calories_per_serving' => rand(180, 550),
                    'serving_size' => '1 serving'
                ]
            );
        }
        
        echo "   🍽️  Added " . count($selectedItems) . " menu items\n";
        echo "   💰 Password: talisay123\n\n";
        
    } catch (Exception $e) {
        echo "   ❌ Error creating {$location['name']}: " . $e->getMessage() . "\n\n";
    }
}

echo "🎉 Talisay City Mock Karenderias Creation Complete!\n\n";

// Display summary
echo "📋 TESTING ACCOUNTS CREATED (Talisay City Area):\n";
echo "================================================\n";
foreach ($locations as $index => $location) {
    $ownerEmail = "talisay_owner" . ($index + 1) . "@kaplato.com";
    echo "🏪 {$location['name']}\n";
    echo "   📧 Email: {$ownerEmail}\n";
    echo "   🔑 Password: talisay123\n";
    echo "   📍 Address: {$location['address']}\n";
    echo "   📱 Phone: {$location['phone']}\n\n";
}

echo "🗺️  LOCATIONS COVERAGE (Talisay City):\n";
echo "======================================\n";
echo "• Near South Town Center Mall (2 karenderias)\n";
echo "• Tabunok Area (2 karenderias)\n";
echo "• Poblacion Area (2 karenderias)\n";
echo "• Lagtang Area (2 karenderias)\n";
echo "• Bulacao Area (2 karenderias)\n\n";

echo "🧪 HOW TO TEST:\n";
echo "================\n";
echo "1. Open your KaPlato mobile app\n";
echo "2. Make sure your location is set to Talisay City area\n";
echo "3. Go to the map view or browse karenderias\n";
echo "4. You should see these 10 karenderias around your area\n";
echo "5. Test distance calculations from South Town Center\n";
echo "6. Login as any test owner to manage their karenderias\n\n";

echo "🍽️  MENU HIGHLIGHTS:\n";
echo "====================\n";
echo "• Authentic Cebuano dishes (Lechon, Humba, Linarang)\n";
echo "• Traditional Filipino favorites\n";
echo "• Fresh seafood options (coastal location)\n";
echo "• Puso (Hanging Rice) - Cebuano staple\n";
echo "• Varied pricing for local market\n\n";

echo "📱 CUSTOMER TESTING:\n";
echo "====================\n";
echo "Use your existing customer account to:\n";
echo "• Browse karenderias near South Town Center\n";
echo "• Test GPS distance calculations\n";
echo "• Order Cebuano specialties\n";
echo "• Test delivery to Talisay addresses\n";
echo "• Compare prices across different areas\n\n";

echo "✨ All done! Your Talisay City test karenderias are ready!\n";
echo "🌴 Perfect for testing your local food delivery app!\n";
?>
