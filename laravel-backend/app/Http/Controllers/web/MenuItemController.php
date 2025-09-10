<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem; // Ensure you include the MenuItem model

class MenuItemController extends Controller
{
    public function showMenuDashboard()
    {
        $menuItems = MenuItem::select('id', 'name', 'description')
            ->where('is_available', true)
            ->get()
            ->toArray();

        return view('dashboard.menuDash', ['menuItems' => $menuItems]);
    }

    public function show($id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $data = [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
            'description' => $menuItem->description,
            'price' => $menuItem->price,
            'category' => $menuItem->category,
            'is_available' => $menuItem->is_available,
            'image_url' => $menuItem->image_url ?? '/assets/images/food-placeholder.jpg',
            'ingredients' => $menuItem->ingredients ?? [],
            'allergens' => $menuItem->allergens ?? []
        ];

        return view('dashboardProfile.menuItemProfile', ['menuItem' => $data]);
    }
}
