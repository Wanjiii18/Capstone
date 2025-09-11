<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Karenderia;
use App\Models\MenuItem;

echo "🍽️  Adding Menu Items to Talisay Karenderias...\n\n";

// Popular Cebuano and Filipino dishes
$menuItems = [
    // Cebuano Specialties
    'Lechon Cebu' => ['price' => 250, 'description' => 'World-famous Cebuano roasted pig with crispy skin', 'category' => 'Main_course'],
    'Puso (Hanging Rice)' => ['price' => 15, 'description' => 'Traditional Cebuano rice cooked in coconut leaves', 'category' => 'Side_dish'],
    'Linarang na Isda' => ['price' => 180, 'description' => 'Fresh fish soup with ginger and tomatoes', 'category' => 'Soup'],
    'Humba' => ['price' => 160, 'description' => 'Cebuano braised pork belly with fermented black beans', 'category' => 'Main_course'],
    'Balbacua' => ['price' => 140, 'description' => 'Rich beef skin and bone marrow stew', 'category' => 'Soup'],
    'Sutukil' => ['price' => 200, 'description' => 'Fresh seafood cooked sugba, tula, or kilaw style', 'category' => 'Seafood'],
    
    // Filipino Classics
    'Adobong Manok' => ['price' => 120, 'description' => 'Classic Filipino chicken adobo', 'category' => 'Main_course'],
    'Sinigang na Baboy' => ['price' => 150, 'description' => 'Sour pork soup with kangkong', 'category' => 'Soup'],
    'Kare-Kare' => ['price' => 180, 'description' => 'Oxtail stew in peanut sauce', 'category' => 'Main_course'],
    'Pancit Canton' => ['price' => 100, 'description' => 'Stir-fried noodles with vegetables', 'category' => 'Main_course'],
    'Tinolang Manok' => ['price' => 125, 'description' => 'Chicken soup with green papaya and malunggay', 'category' => 'Soup'],
    'Beef Caldereta' => ['price' => 170, 'description' => 'Beef stew in tomato sauce', 'category' => 'Main_course'],
    'Pinakbet' => ['price' => 105, 'description' => 'Mixed vegetables with bagoong', 'category' => 'Side_dish'],
    'Bicol Express' => ['price' => 145, 'description' => 'Spicy pork in coconut milk', 'category' => 'Main_course'],
    'Laing' => ['price' => 110, 'description' => 'Taro leaves in coconut milk', 'category' => 'Side_dish'],
    
    // Seafood (popular in coastal Talisay)
    'Grilled Bangus' => ['price' => 135, 'description' => 'Grilled milkfish with soy-calamansi sauce', 'category' => 'Seafood'],
    'Sweet & Sour Fish' => ['price' => 155, 'description' => 'Fried fish in sweet and sour sauce', 'category' => 'Seafood'],
    'Ginataang Hipon' => ['price' => 175, 'description' => 'Shrimp cooked in coconut milk', 'category' => 'Seafood'],
    'Fried Rice' => ['price' => 80, 'description' => 'Garlic fried rice with egg', 'category' => 'Side_dish'],
    'Bulalo' => ['price' => 190, 'description' => 'Beef bone marrow soup', 'category' => 'Soup']
];

$talisayKarenderias = Karenderia::where('address', 'LIKE', '%Talisay%')->get();

foreach ($talisayKarenderias as $karenderia) {
    echo "🏪 Adding menu items to {$karenderia->name}...\n";
    
    // Add 6-8 random menu items per karenderia
    $numItems = rand(6, 8);
    $menuItemKeys = array_keys($menuItems);
    $selectedItemKeys = array_rand($menuItemKeys, $numItems);
    
    // Ensure $selectedItemKeys is always an array
    if (!is_array($selectedItemKeys)) {
        $selectedItemKeys = [$selectedItemKeys];
    }
    
    $addedCount = 0;
    foreach ($selectedItemKeys as $keyIndex) {
        $itemName = $menuItemKeys[$keyIndex];
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
                'category' => $itemData['category'],
                'image_url' => null,
                'is_available' => true,
                'prep_time' => rand(15, 35),
                'ingredients' => json_encode(['main ingredient', 'spices', 'vegetables', 'seasonings']),
                'allergens' => json_encode(['soy', 'seafood']),
                'spice_level' => rand(1, 4), // Cebuanos like it spicy
                'is_vegetarian' => in_array($itemName, ['Pinakbet', 'Laing', 'Puso (Hanging Rice)', 'Fried Rice']),
                'calories_per_serving' => rand(180, 550),
                'serving_size' => '1'
            ]
        );
        
        if ($menuItem->wasRecentlyCreated) {
            $addedCount++;
        }
    }
    
    echo "   ✅ Added {$addedCount} menu items\n\n";
}

// Update ratings for the karenderias
foreach ($talisayKarenderias as $karenderia) {
    $karenderia->update([
        'rating' => round(rand(38, 50) / 10, 1), // 3.8 to 5.0 rating
        'total_reviews' => rand(10, 80)
    ]);
}

echo "🎉 Menu items added successfully!\n\n";

// Final summary
echo "📋 FINAL SUMMARY:\n";
echo "==================\n";
foreach ($talisayKarenderias as $k) {
    $menuCount = $k->menuItems()->count();
    echo "🏪 {$k->name}\n";
    echo "   📍 {$k->address}\n";
    echo "   🍽️  Menu Items: {$menuCount}\n";
    echo "   🌟 Rating: {$k->rating}/5.0 ({$k->total_reviews} reviews)\n";
    echo "   💰 Delivery Fee: ₱{$k->delivery_fee}\n\n";
}

echo "✨ Your Talisay City karenderias are now fully stocked with delicious Filipino and Cebuano dishes!\n";
echo "🧪 Ready for comprehensive testing!\n";
?>
