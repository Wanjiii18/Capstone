<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karenderia;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\Ingredient;

class CebuKarenderiaSeeder extends Seeder
{
    public function run(): void
    {
        // Create Cebu-based karenderia owners
        $this->createCebuOwners();
        
        // Create Cebu karenderias
        $this->createCebuKarenderias();
        
        // Create detailed recipes with ingredients
        $this->createDetailedRecipes();
        
        // Create menu items from recipes
        $this->createMenuFromRecipes();
        
        $this->command->info('Cebu karenderias with detailed recipes created successfully!');
    }
    
    private function createCebuOwners()
    {
        // Owner 1 - Lola Esperanza (Cebu City)
        User::firstOrCreate([
            'email' => 'esperanza@cebu.com'
        ], [
            'name' => 'Esperanza Santos',
            'role' => 'karenderia_owner',
            'password' => bcrypt('password123'),
            'phone' => '+639171234567',
            'address' => 'Colon Street, Cebu City',
            'is_verified' => true,
            'profile' => [
                'bio' => 'Third-generation cook specializing in traditional Cebuano cuisine',
                'cooking_experience' => '25 years',
                'specialties' => ['Lechon', 'Humba', 'Balbacua']
            ]
        ]);

        // Owner 2 - Kuya Dodong (Lapu-Lapu)
        User::firstOrCreate([
            'email' => 'dodong@lapulapu.com'
        ], [
            'name' => 'Rodolfo "Dodong" Rama',
            'role' => 'karenderia_owner',
            'password' => bcrypt('password123'),
            'phone' => '+639182345678',
            'address' => 'Mactan Island, Lapu-Lapu City',
            'is_verified' => true,
            'profile' => [
                'bio' => 'Seafood specialist and local fisherman turned chef',
                'cooking_experience' => '15 years',
                'specialties' => ['Grilled Fish', 'Sutukil', 'Seafood Kinilaw']
            ]
        ]);

        // Owner 3 - Tita Carmen (Mandaue)
        User::firstOrCreate([
            'email' => 'carmen@mandaue.com'
        ], [
            'name' => 'Carmen Villanueva',
            'role' => 'karenderia_owner',
            'password' => bcrypt('password123'),
            'phone' => '+639193456789',
            'address' => 'A.C. Cortes Avenue, Mandaue City',
            'is_verified' => true,
            'profile' => [
                'bio' => 'Home-style cooking with modern twists on classic dishes',
                'cooking_experience' => '20 years',
                'specialties' => ['Puso Rice', 'Ngohiong', 'Torta']
            ]
        ]);
    }
    
    private function createCebuKarenderias()
    {
        $esperanza = User::where('email', 'esperanza@cebu.com')->first();
        $dodong = User::where('email', 'dodong@lapulapu.com')->first();
        $carmen = User::where('email', 'carmen@mandaue.com')->first();

        // Lola Esperanza's Heritage Kitchen
        Karenderia::firstOrCreate([
            'name' => 'Lola Esperanza\'s Heritage Kitchen',
            'owner_id' => $esperanza->id,
        ], [
            'business_name' => 'Heritage Kitchen Cebu',
            'description' => 'Authentic Cebuano cuisine passed down through generations. Home of the best Humba and Balbacua in Cebu City.',
            'address' => '145 Colon Street, Cebu City, Cebu',
            'city' => 'Cebu City',
            'province' => 'Cebu',
            'phone' => '+639171234567',
            'email' => 'heritage@cebu.com',
            'business_email' => 'business@heritagekitchen.com',
            'latitude' => 10.2966,
            'longitude' => 123.9019,
            'opening_time' => '06:00:00',
            'closing_time' => '20:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'status' => 'active',
            'delivery_fee' => 35.00,
            'delivery_time_minutes' => 25,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'average_rating' => 4.8,
            'total_reviews' => 156
        ]);

        // Kuya Dodong's Seafood Grill
        Karenderia::firstOrCreate([
            'name' => 'Kuya Dodong\'s Seafood Grill',
            'owner_id' => $dodong->id,
        ], [
            'business_name' => 'Dodong\'s Sutukil House',
            'description' => 'Fresh catch daily! Specializing in Sutukil - Sugba (grill), Tula (soup), Kilaw (ceviche). Located in the heart of Mactan.',
            'address' => '88 M.L. Quezon Street, Lapu-Lapu City, Cebu',
            'city' => 'Lapu-Lapu City',
            'province' => 'Cebu',
            'phone' => '+639182345678',
            'email' => 'dodong@sutukil.com',
            'business_email' => 'business@sutukil.com',
            'latitude' => 10.3103,
            'longitude' => 123.9494,
            'opening_time' => '10:00:00',
            'closing_time' => '22:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'status' => 'active',
            'delivery_fee' => 40.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'average_rating' => 4.6,
            'total_reviews' => 89
        ]);

        // Tita Carmen's Modern Karenderia
        Karenderia::firstOrCreate([
            'name' => 'Tita Carmen\'s Modern Karenderia',
            'owner_id' => $carmen->id,
        ], [
            'business_name' => 'Carmen\'s Kitchen Mandaue',
            'description' => 'Traditional Cebuano comfort food with a modern twist. Famous for our special Ngohiong and Puso rice varieties.',
            'address' => '234 A.C. Cortes Avenue, Mandaue City, Cebu',
            'city' => 'Mandaue City',
            'province' => 'Cebu',
            'phone' => '+639193456789',
            'email' => 'carmen@modern.com',
            'business_email' => 'business@carmenskitchen.com',
            'latitude' => 10.3157,
            'longitude' => 123.8854,
            'opening_time' => '07:00:00',
            'closing_time' => '21:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'status' => 'active',
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 20,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'average_rating' => 4.5,
            'total_reviews' => 127
        ]);
    }
    
    private function createDetailedRecipes()
    {
        $esperanza = User::where('email', 'esperanza@cebu.com')->first();
        $dodong = User::where('email', 'dodong@lapulapu.com')->first();
        $carmen = User::where('email', 'carmen@mandaue.com')->first();

        $esperanzaKarenderia = Karenderia::where('owner_id', $esperanza->id)->first();
        $dodongKarenderia = Karenderia::where('owner_id', $dodong->id)->first();
        $carmenKarenderia = Karenderia::where('owner_id', $carmen->id)->first();

        // ESPERANZA'S RECIPES
        
        // 1. Classic Cebuano Humba
        Recipe::firstOrCreate([
            'name' => 'Classic Cebuano Humba',
            'karenderia_id' => $esperanzaKarenderia->id,
        ], [
            'description' => 'Traditional braised pork belly in sweet soy sauce with salted black beans',
            'ingredients' => [
                'pork_belly' => ['amount' => '1', 'unit' => 'kg', 'notes' => 'cut into chunks'],
                'soy_sauce' => ['amount' => '1/2', 'unit' => 'cup', 'notes' => 'dark soy sauce preferred'],
                'brown_sugar' => ['amount' => '1/4', 'unit' => 'cup', 'notes' => 'or palm sugar'],
                'tausi' => ['amount' => '3', 'unit' => 'tbsp', 'notes' => 'salted black beans'],
                'garlic' => ['amount' => '6', 'unit' => 'cloves', 'notes' => 'minced'],
                'onion' => ['amount' => '1', 'unit' => 'large', 'notes' => 'sliced'],
                'bay_leaves' => ['amount' => '3', 'unit' => 'pieces', 'notes' => 'dried'],
                'whole_peppercorns' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'black peppercorns'],
                'star_anise' => ['amount' => '2', 'unit' => 'pieces', 'notes' => 'optional for aroma'],
                'rice_wine' => ['amount' => '2', 'unit' => 'tbsp', 'notes' => 'or white wine'],
                'water' => ['amount' => '2', 'unit' => 'cups', 'notes' => 'or pork stock'],
                'cooking_oil' => ['amount' => '3', 'unit' => 'tbsp', 'notes' => 'for sautéing']
            ],
            'instructions' => [
                '1. Heat oil in a heavy-bottom pot and brown the pork belly on all sides',
                '2. Add onions and garlic, sauté until fragrant',
                '3. Add tausi and cook for 1 minute',
                '4. Pour in soy sauce, rice wine, and add brown sugar',
                '5. Add bay leaves, peppercorns, and star anise',
                '6. Pour water to cover the meat',
                '7. Bring to boil, then simmer covered for 1.5-2 hours until tender',
                '8. Adjust seasoning and simmer uncovered to reduce sauce',
                '9. Serve hot with steamed rice'
            ],
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 120,
            'difficulty_level' => 'medium',
            'servings' => 6,
            'category' => 'main_course',
            'cuisine_type' => 'cebuano',
            'cost_estimate' => 280.00,
            'nutritional_info' => [
                'calories_per_serving' => 520,
                'protein_grams' => 35,
                'carbs_grams' => 12,
                'fat_grams' => 38
            ]
        ]);

        // 2. Authentic Balbacua
        Recipe::firstOrCreate([
            'name' => 'Authentic Cebuano Balbacua',
            'karenderia_id' => $esperanzaKarenderia->id,
        ], [
            'description' => 'Rich and hearty beef and oxtail stew with collagen-rich broth',
            'ingredients' => [
                'beef_shank' => ['amount' => '1', 'unit' => 'kg', 'notes' => 'with bone, cut into pieces'],
                'oxtail' => ['amount' => '500', 'unit' => 'g', 'notes' => 'cut into pieces'],
                'beef_skin' => ['amount' => '200', 'unit' => 'g', 'notes' => 'cleaned and cubed'],
                'lemongrass' => ['amount' => '3', 'unit' => 'stalks', 'notes' => 'bruised'],
                'ginger' => ['amount' => '2', 'unit' => 'inches', 'notes' => 'sliced'],
                'onion' => ['amount' => '2', 'unit' => 'large', 'notes' => 'quartered'],
                'garlic' => ['amount' => '8', 'unit' => 'cloves', 'notes' => 'crushed'],
                'tomatoes' => ['amount' => '3', 'unit' => 'large', 'notes' => 'quartered'],
                'fish_sauce' => ['amount' => '1/4', 'unit' => 'cup', 'notes' => 'adjust to taste'],
                'black_pepper' => ['amount' => '2', 'unit' => 'tsp', 'notes' => 'ground'],
                'salt' => ['amount' => '1', 'unit' => 'tbsp', 'notes' => 'to taste'],
                'water' => ['amount' => '3', 'unit' => 'liters', 'notes' => 'for boiling'],
                'green_onions' => ['amount' => '4', 'unit' => 'stalks', 'notes' => 'chopped for garnish'],
                'fried_garlic' => ['amount' => '1/4', 'unit' => 'cup', 'notes' => 'for topping']
            ],
            'instructions' => [
                '1. Boil beef shank and oxtail in water for 10 minutes, drain and rinse',
                '2. In a pressure cooker, combine cleaned meat with lemongrass, ginger, onion, and garlic',
                '3. Add water to cover and pressure cook for 45 minutes',
                '4. Add tomatoes and beef skin, cook for another 15 minutes',
                '5. Season with fish sauce, salt, and black pepper',
                '6. Simmer until meat is fork-tender and broth is rich',
                '7. Adjust seasoning to taste',
                '8. Serve hot garnished with green onions and fried garlic',
                '9. Best enjoyed with steamed rice or puso'
            ],
            'prep_time_minutes' => 45,
            'cook_time_minutes' => 180,
            'difficulty_level' => 'hard',
            'servings' => 8,
            'category' => 'soup',
            'cuisine_type' => 'cebuano',
            'cost_estimate' => 450.00,
            'nutritional_info' => [
                'calories_per_serving' => 380,
                'protein_grams' => 42,
                'carbs_grams' => 8,
                'fat_grams' => 18
            ]
        ]);

        // DODONG'S SEAFOOD RECIPES

        // 3. Grilled Tanigue with Lato
        Recipe::firstOrCreate([
            'name' => 'Grilled Tanigue with Lato Salad',
            'karenderia_id' => $dodongKarenderia->id,
        ], [
            'description' => 'Fresh tanigue grilled to perfection served with native seaweed salad',
            'ingredients' => [
                'tanigue_fillet' => ['amount' => '1', 'unit' => 'kg', 'notes' => 'fresh, cut into steaks'],
                'lato_seaweed' => ['amount' => '200', 'unit' => 'g', 'notes' => 'fresh green seaweed'],
                'tomatoes' => ['amount' => '2', 'unit' => 'large', 'notes' => 'diced'],
                'red_onion' => ['amount' => '1', 'unit' => 'medium', 'notes' => 'thinly sliced'],
                'ginger' => ['amount' => '2', 'unit' => 'inches', 'notes' => 'julienned'],
                'calamansi' => ['amount' => '10', 'unit' => 'pieces', 'notes' => 'juiced'],
                'coconut_vinegar' => ['amount' => '3', 'unit' => 'tbsp', 'notes' => 'native vinegar'],
                'fish_sauce' => ['amount' => '2', 'unit' => 'tbsp', 'notes' => 'for seasoning'],
                'sea_salt' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'coarse'],
                'black_pepper' => ['amount' => '1/2', 'unit' => 'tsp', 'notes' => 'freshly ground'],
                'garlic' => ['amount' => '4', 'unit' => 'cloves', 'notes' => 'minced'],
                'coconut_oil' => ['amount' => '3', 'unit' => 'tbsp', 'notes' => 'for marinating'],
                'banana_leaves' => ['amount' => '2', 'unit' => 'pieces', 'notes' => 'for wrapping, optional']
            ],
            'instructions' => [
                '1. Marinate tanigue with calamansi juice, salt, pepper, and garlic for 30 minutes',
                '2. Prepare lato by washing thoroughly in cold water',
                '3. Mix lato with tomatoes, onions, and ginger',
                '4. Season lato salad with vinegar, fish sauce, and calamansi juice',
                '5. Preheat grill to medium-high heat',
                '6. Brush fish with coconut oil and grill 4-5 minutes per side',
                '7. Fish is done when it flakes easily',
                '8. Serve grilled tanigue with lato salad on the side',
                '9. Garnish with additional calamansi wedges'
            ],
            'prep_time_minutes' => 45,
            'cook_time_minutes' => 15,
            'difficulty_level' => 'easy',
            'servings' => 4,
            'category' => 'main_course',
            'cuisine_type' => 'cebuano_seafood',
            'cost_estimate' => 320.00,
            'nutritional_info' => [
                'calories_per_serving' => 290,
                'protein_grams' => 45,
                'carbs_grams' => 8,
                'fat_grams' => 8
            ]
        ]);

        // 4. Sutukil Mixed Platter
        Recipe::firstOrCreate([
            'name' => 'Sutukil Mixed Seafood Platter',
            'karenderia_id' => $dodongKarenderia->id,
        ], [
            'description' => 'The famous Cebuano Sutukil - Sugba (grilled), Tula (soup), Kilaw (ceviche) combination',
            'ingredients' => [
                // For Sugba (Grilled)
                'red_snapper' => ['amount' => '500', 'unit' => 'g', 'notes' => 'whole fish, cleaned'],
                'squid' => ['amount' => '300', 'unit' => 'g', 'notes' => 'cleaned'],
                'shrimp' => ['amount' => '300', 'unit' => 'g', 'notes' => 'large, shell-on'],
                // For Tula (Soup)
                'fish_head' => ['amount' => '1', 'unit' => 'large', 'notes' => 'grouper or similar'],
                'okra' => ['amount' => '200', 'unit' => 'g', 'notes' => 'sliced'],
                'kangkong' => ['amount' => '1', 'unit' => 'bunch', 'notes' => 'water spinach'],
                'eggplant' => ['amount' => '2', 'unit' => 'medium', 'notes' => 'sliced'],
                'sitaw' => ['amount' => '200', 'unit' => 'g', 'notes' => 'string beans'],
                // For Kilaw (Ceviche)
                'fresh_tuna' => ['amount' => '300', 'unit' => 'g', 'notes' => 'sashimi grade'],
                // Common ingredients
                'ginger' => ['amount' => '3', 'unit' => 'inches', 'notes' => 'sliced and julienned'],
                'onions' => ['amount' => '3', 'unit' => 'medium', 'notes' => 'various cuts'],
                'tomatoes' => ['amount' => '4', 'unit' => 'medium', 'notes' => 'wedged and diced'],
                'coconut_vinegar' => ['amount' => '1/2', 'unit' => 'cup', 'notes' => 'native vinegar'],
                'calamansi' => ['amount' => '20', 'unit' => 'pieces', 'notes' => 'juiced'],
                'coconut_milk' => ['amount' => '1', 'unit' => 'cup', 'notes' => 'fresh or canned'],
                'sea_salt' => ['amount' => '2', 'unit' => 'tbsp', 'notes' => 'to taste'],
                'black_pepper' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'ground'],
                'chili_peppers' => ['amount' => '3', 'unit' => 'pieces', 'notes' => 'siling labuyo']
            ],
            'instructions' => [
                'SUGBA (Grilled):',
                '1. Season fish, squid, and shrimp with salt, pepper, and calamansi',
                '2. Grill over medium coals until cooked through',
                '3. Serve with spiced vinegar dip',
                '',
                'TULA (Soup):',
                '4. Boil fish head with ginger and onions until broth is clear',
                '5. Add vegetables in order of cooking time needed',
                '6. Season with salt and pepper',
                '',
                'KILAW (Ceviche):',
                '7. Cube fresh tuna and marinate in vinegar and calamansi for 30 minutes',
                '8. Add onions, tomatoes, ginger, and chili',
                '9. Mix with coconut milk just before serving',
                '',
                '10. Serve all three together as a complete Sutukil experience'
            ],
            'prep_time_minutes' => 60,
            'cook_time_minutes' => 45,
            'difficulty_level' => 'hard',
            'servings' => 6,
            'category' => 'seafood_platter',
            'cuisine_type' => 'cebuano_seafood',
            'cost_estimate' => 680.00,
            'nutritional_info' => [
                'calories_per_serving' => 420,
                'protein_grams' => 55,
                'carbs_grams' => 15,
                'fat_grams' => 16
            ]
        ]);

        // CARMEN'S MODERN RECIPES

        // 5. Special Ngohiong
        Recipe::firstOrCreate([
            'name' => 'Special Cebuano Ngohiong',
            'karenderia_id' => $carmenKarenderia->id,
        ], [
            'description' => 'Crispy spring rolls filled with seasoned ground pork and vegetables, served with special sauce',
            'ingredients' => [
                'ground_pork' => ['amount' => '500', 'unit' => 'g', 'notes' => 'not too lean'],
                'shrimp' => ['amount' => '200', 'unit' => 'g', 'notes' => 'peeled and chopped'],
                'water_chestnuts' => ['amount' => '100', 'unit' => 'g', 'notes' => 'diced'],
                'carrots' => ['amount' => '2', 'unit' => 'medium', 'notes' => 'julienned'],
                'cabbage' => ['amount' => '1', 'unit' => 'cup', 'notes' => 'shredded'],
                'green_onions' => ['amount' => '4', 'unit' => 'stalks', 'notes' => 'chopped'],
                'garlic' => ['amount' => '6', 'unit' => 'cloves', 'notes' => 'minced'],
                'onion' => ['amount' => '1', 'unit' => 'medium', 'notes' => 'diced'],
                'egg' => ['amount' => '2', 'unit' => 'pieces', 'notes' => 'beaten'],
                'spring_roll_wrapper' => ['amount' => '20', 'unit' => 'pieces', 'notes' => 'thin lumpia wrapper'],
                'five_spice_powder' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'Chinese five-spice'],
                'soy_sauce' => ['amount' => '3', 'unit' => 'tbsp', 'notes' => 'light soy sauce'],
                'oyster_sauce' => ['amount' => '2', 'unit' => 'tbsp', 'notes' => 'for flavor'],
                'sesame_oil' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'for aroma'],
                'cornstarch' => ['amount' => '2', 'unit' => 'tbsp', 'notes' => 'for binding'],
                'cooking_oil' => ['amount' => '3', 'unit' => 'cups', 'notes' => 'for deep frying'],
                'salt' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'to taste'],
                'white_pepper' => ['amount' => '1/2', 'unit' => 'tsp', 'notes' => 'ground']
            ],
            'instructions' => [
                '1. Sauté garlic and onions until fragrant',
                '2. Add ground pork and cook until browned',
                '3. Add shrimp and cook until pink',
                '4. Add vegetables and seasonings, cook until tender',
                '5. Let filling cool completely',
                '6. Place filling on wrapper, roll tightly, seal with beaten egg',
                '7. Deep fry in hot oil until golden brown',
                '8. Drain on paper towels',
                '9. Serve hot with sweet and sour sauce or spiced vinegar'
            ],
            'prep_time_minutes' => 90,
            'cook_time_minutes' => 30,
            'difficulty_level' => 'medium',
            'servings' => 4,
            'category' => 'appetizer',
            'cuisine_type' => 'cebuano_chinese',
            'cost_estimate' => 180.00,
            'nutritional_info' => [
                'calories_per_serving' => 340,
                'protein_grams' => 22,
                'carbs_grams' => 25,
                'fat_grams' => 18
            ]
        ]);

        // 6. Torta Cebuano Style
        Recipe::firstOrCreate([
            'name' => 'Cebuano Style Torta',
            'karenderia_id' => $carmenKarenderia->id,
        ], [
            'description' => 'Traditional egg-based dish with ground pork and vegetables, pan-fried to perfection',
            'ingredients' => [
                'eggs' => ['amount' => '8', 'unit' => 'pieces', 'notes' => 'large, beaten'],
                'ground_pork' => ['amount' => '300', 'unit' => 'g', 'notes' => 'seasoned'],
                'potatoes' => ['amount' => '2', 'unit' => 'medium', 'notes' => 'diced small'],
                'carrots' => ['amount' => '1', 'unit' => 'large', 'notes' => 'diced'],
                'green_peas' => ['amount' => '1/2', 'unit' => 'cup', 'notes' => 'fresh or frozen'],
                'bell_pepper' => ['amount' => '1', 'unit' => 'medium', 'notes' => 'diced'],
                'onion' => ['amount' => '1', 'unit' => 'medium', 'notes' => 'minced'],
                'garlic' => ['amount' => '4', 'unit' => 'cloves', 'notes' => 'minced'],
                'cheese' => ['amount' => '100', 'unit' => 'g', 'notes' => 'grated cheddar'],
                'milk' => ['amount' => '1/4', 'unit' => 'cup', 'notes' => 'evaporated milk'],
                'fish_sauce' => ['amount' => '1', 'unit' => 'tbsp', 'notes' => 'for seasoning'],
                'black_pepper' => ['amount' => '1/2', 'unit' => 'tsp', 'notes' => 'ground'],
                'cooking_oil' => ['amount' => '1/4', 'unit' => 'cup', 'notes' => 'for frying'],
                'salt' => ['amount' => '1', 'unit' => 'tsp', 'notes' => 'to taste']
            ],
            'instructions' => [
                '1. Sauté garlic and onions in a little oil',
                '2. Add ground pork and cook until browned',
                '3. Add potatoes and carrots, cook until tender',
                '4. Add bell pepper and peas, cook briefly',
                '5. Season with fish sauce, salt, and pepper',
                '6. Let filling cool completely',
                '7. Beat eggs with milk and cheese',
                '8. Mix cooled filling with beaten eggs',
                '9. Pour into hot oiled pan, cook like a thick omelet',
                '10. Flip carefully when bottom is set',
                '11. Cook until golden brown on both sides',
                '12. Slice and serve hot'
            ],
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 25,
            'difficulty_level' => 'medium',
            'servings' => 6,
            'category' => 'main_course',
            'cuisine_type' => 'cebuano_comfort',
            'cost_estimate' => 150.00,
            'nutritional_info' => [
                'calories_per_serving' => 285,
                'protein_grams' => 20,
                'carbs_grams' => 12,
                'fat_grams' => 18
            ]
        ]);
    }
    
    private function createMenuFromRecipes()
    {
        // Get all karenderias and their recipes
        $karenderias = Karenderia::with('recipes')->get();
        
        foreach ($karenderias as $karenderia) {
            foreach ($karenderia->recipes as $recipe) {
                MenuItem::firstOrCreate([
                    'karenderia_id' => $karenderia->id,
                    'name' => $recipe->name
                ], [
                    'recipe_id' => $recipe->id,
                    'description' => $recipe->description,
                    'price' => $this->calculateMenuPrice($recipe->cost_estimate),
                    'cost_price' => $recipe->cost_estimate / $recipe->servings,
                    'category' => $recipe->category,
                    'is_available' => true,
                    'is_featured' => in_array($recipe->name, [
                        'Classic Cebuano Humba',
                        'Sutukil Mixed Seafood Platter',
                        'Special Cebuano Ngohiong'
                    ]),
                    'preparation_time_minutes' => $recipe->prep_time_minutes + $recipe->cook_time_minutes,
                    'calories' => $recipe->nutritional_info['calories_per_serving'] ?? 300,
                    'ingredients' => array_keys($recipe->ingredients),
                    'allergens' => $this->getRecipeAllergens($recipe),
                    'spice_level' => $this->getSpiceLevel($recipe),
                    'average_rating' => rand(40, 50) / 10,
                    'total_reviews' => rand(15, 45),
                    'total_orders' => rand(25, 89)
                ]);
            }
        }
    }
    
    private function calculateMenuPrice($costEstimate)
    {
        // Add 60-80% markup for profit
        $markup = rand(160, 180) / 100;
        return round($costEstimate * $markup, 2);
    }
    
    private function getRecipeAllergens($recipe)
    {
        $allergens = [];
        $ingredients = array_keys($recipe->ingredients);
        
        $allergenMap = [
            'shrimp' => 'shellfish',
            'squid' => 'shellfish',
            'fish' => 'fish',
            'egg' => 'eggs',
            'milk' => 'dairy',
            'cheese' => 'dairy',
            'soy_sauce' => 'soy'
        ];
        
        foreach ($ingredients as $ingredient) {
            foreach ($allergenMap as $allergenIngredient => $allergen) {
                if (str_contains($ingredient, $allergenIngredient)) {
                    $allergens[] = $allergen;
                }
            }
        }
        
        return array_unique($allergens);
    }
    
    private function getSpiceLevel($recipe)
    {
        $ingredients = array_keys($recipe->ingredients);
        $spiceIngredients = ['chili', 'pepper', 'spice'];
        
        foreach ($ingredients as $ingredient) {
            foreach ($spiceIngredients as $spice) {
                if (str_contains($ingredient, $spice)) {
                    return rand(1, 3);
                }
            }
        }
        
        return 0;
    }
}
