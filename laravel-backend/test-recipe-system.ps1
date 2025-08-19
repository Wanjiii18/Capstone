# Test Recipe Management System

# Create a new admin user first if needed
Write-Host "Creating admin user for testing..." -ForegroundColor Yellow
$adminData = @{
    name = "Test Admin"
    email = "admin@karenderia.test"
    password = "admin123"
    password_confirmation = "admin123"
    phone = "+639123456789"
    address = "Cebu City"
    role = "admin"
} | ConvertTo-Json -Depth 3

try {
    $adminResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/register" `
        -Method POST `
        -Body $adminData `
        -ContentType "application/json"
    Write-Host "Admin created successfully!" -ForegroundColor Green
} catch {
    Write-Host "Admin might already exist or other error occurred" -ForegroundColor Yellow
}

# Create a karenderia owner
Write-Host "`nCreating karenderia owner..." -ForegroundColor Yellow
$ownerData = @{
    name = "Maria Santos"
    email = "maria@cebu.test"
    password = "password123"
    password_confirmation = "password123"
    phone = "+639987654321"
    address = "Lahug, Cebu City"
    role = "karenderia_owner"
    karenderia_name = "Maria's Authentic Cebuano Kitchen"
    karenderia_address = "Lahug Circle, Cebu City"
    latitude = 10.3157
    longitude = 123.8854
} | ConvertTo-Json -Depth 3

try {
    $ownerResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/register-karenderia-owner" `
        -Method POST `
        -Body $ownerData `
        -ContentType "application/json"
    
    Write-Host "Karenderia owner created successfully!" -ForegroundColor Green
    $ownerToken = $ownerResponse.token
    Write-Host "Owner Token: $ownerToken" -ForegroundColor Cyan
} catch {
    Write-Host "Error creating owner: $($_.Exception.Message)" -ForegroundColor Red
    # Try to login instead
    $loginData = @{
        email = "maria@cebu.test"
        password = "password123"
    } | ConvertTo-Json

    try {
        $loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" `
            -Method POST `
            -Body $loginData `
            -ContentType "application/json"
        
        $ownerToken = $loginResponse.token
        Write-Host "Logged in successfully! Token: $ownerToken" -ForegroundColor Green
    } catch {
        Write-Host "Failed to login: $($_.Exception.Message)" -ForegroundColor Red
        exit 1
    }
}

# Test health endpoint
Write-Host "`nTesting health endpoint..." -ForegroundColor Yellow
try {
    $healthResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/health" -Method GET
    Write-Host "Health Status: $($healthResponse.status)" -ForegroundColor Green
} catch {
    Write-Host "Health check failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Create a recipe
Write-Host "`nCreating a detailed recipe..." -ForegroundColor Yellow
$recipeData = @{
    name = "Classic Cebuano Humba"
    description = "A traditional Cebuano braised pork dish with a sweet and savory sauce, slow-cooked to perfection"
    ingredients = @{
        "pork belly" = "1 kg, cut into chunks"
        "soy sauce" = "1/2 cup dark soy sauce"
        "vinegar" = "1/4 cup native vinegar"
        "brown sugar" = "3 tablespoons"
        "star anise" = "3 pieces"
        "bay leaves" = "3 pieces"
        "garlic" = "8 cloves, minced"
        "onion" = "1 large, sliced"
        "ginger" = "2 inches, sliced"
        "black beans" = "2 tablespoons salted black beans"
        "hard boiled eggs" = "4 pieces, peeled"
        "banana blossoms" = "1 piece, sliced (optional)"
        "water" = "2 cups"
        "cooking oil" = "2 tablespoons"
        "ground black pepper" = "1/2 teaspoon"
        "salt" = "to taste"
    }
    instructions = @(
        "Heat oil in a heavy-bottomed pot over medium heat",
        "Sauté garlic, onion, and ginger until fragrant and golden",
        "Add pork belly chunks and brown on all sides (about 8-10 minutes)",
        "Add soy sauce, vinegar, and brown sugar. Mix well",
        "Add star anise, bay leaves, black beans, and black pepper",
        "Pour in water and bring to a boil",
        "Lower heat to low-medium, cover and simmer for 1.5 hours",
        "Add hard boiled eggs and banana blossoms (if using)",
        "Continue cooking for another 30 minutes until pork is tender",
        "Adjust seasoning with salt and sugar if needed",
        "Simmer uncovered for final 10 minutes to thicken sauce",
        "Serve hot with steamed rice"
    )
    prep_time_minutes = 30
    cook_time_minutes = 120
    difficulty_level = "medium"
    servings = 6
    category = "Main Course"
    cuisine_type = "Cebuano"
    cost_estimate = 450.00
    nutritional_info = @{
        calories_per_serving = 380
        protein_g = 28
        fat_g = 22
        carbs_g = 15
        fiber_g = 2
        sodium_mg = 890
    }
    is_signature = $true
} | ConvertTo-Json -Depth 4

$headers = @{
    "Authorization" = "Bearer $ownerToken"
    "Content-Type" = "application/json"
}

try {
    $recipeResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/recipes" `
        -Method POST `
        -Body $recipeData `
        -Headers $headers
    
    Write-Host "Recipe created successfully!" -ForegroundColor Green
    Write-Host "Recipe ID: $($recipeResponse.data.id)" -ForegroundColor Cyan
    $recipeId = $recipeResponse.data.id
} catch {
    Write-Host "Error creating recipe: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Response: $($_.Exception.Response)" -ForegroundColor Red
}

# Get recipe stats
Write-Host "`nGetting recipe statistics..." -ForegroundColor Yellow
try {
    $statsResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/recipes/stats" `
        -Method GET `
        -Headers $headers
    
    Write-Host "Recipe Stats:" -ForegroundColor Green
    Write-Host "  Total Recipes: $($statsResponse.data.total_recipes)" -ForegroundColor White
    Write-Host "  Published: $($statsResponse.data.published_recipes)" -ForegroundColor White
    Write-Host "  Signature Dishes: $($statsResponse.data.signature_recipes)" -ForegroundColor White
    Write-Host "  Ready for Menu: $($statsResponse.data.ready_for_menu)" -ForegroundColor White
} catch {
    Write-Host "Error getting stats: $($_.Exception.Message)" -ForegroundColor Red
}

# List all recipes
Write-Host "`nListing all recipes..." -ForegroundColor Yellow
try {
    $recipesResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/recipes" `
        -Method GET `
        -Headers $headers
    
    Write-Host "Found $($recipesResponse.data.Count) recipes:" -ForegroundColor Green
    foreach ($recipe in $recipesResponse.data) {
        Write-Host "  - $($recipe.name) (ID: $($recipe.id))" -ForegroundColor White
        Write-Host "    Difficulty: $($recipe.difficulty_level) | Servings: $($recipe.servings)" -ForegroundColor Gray
        Write-Host "    Ingredients: $($recipe.ingredients_count) | Can create menu item: $($recipe.can_create_menu_item)" -ForegroundColor Gray
    }
} catch {
    Write-Host "Error listing recipes: $($_.Exception.Message)" -ForegroundColor Red
}

# Create menu item from recipe if we have a recipe ID
if ($recipeId) {
    Write-Host "`nCreating menu item from recipe..." -ForegroundColor Yellow
    $menuItemData = @{
        price = 180.00
        is_featured = $true
        spice_level = 1
    } | ConvertTo-Json

    try {
        $menuItemResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/recipes/$recipeId/create-menu-item" `
            -Method POST `
            -Body $menuItemData `
            -Headers $headers
        
        Write-Host "Menu item created successfully!" -ForegroundColor Green
        Write-Host "Menu Item: $($menuItemResponse.data.name) - ₱$($menuItemResponse.data.price)" -ForegroundColor Cyan
    } catch {
        Write-Host "Error creating menu item: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Test nearby karenderias to verify distance calculations work
Write-Host "`nTesting nearby karenderias..." -ForegroundColor Yellow
try {
    $nearbyResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/karenderias/nearby?latitude=10.3157&longitude=123.8854&radius=10" `
        -Method GET
    
    Write-Host "Found $($nearbyResponse.data.Count) nearby karenderias:" -ForegroundColor Green
    foreach ($karenderia in $nearbyResponse.data) {
        Write-Host "  - $($karenderia.name)" -ForegroundColor White
        Write-Host "    Distance: $($karenderia.distance) km | Address: $($karenderia.address)" -ForegroundColor Gray
    }
} catch {
    Write-Host "Error getting nearby karenderias: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`nRecipe management system testing completed!" -ForegroundColor Green
