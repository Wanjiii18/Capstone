<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MealPlan;

class MealPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mealPlans = MealPlan::all();
        return response()->json($mealPlans);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'breakfast' => 'required|string',
            'lunch' => 'required|string',
            'dinner' => 'required|string',
        ]);

        // Add default values
        $validated['user_id'] = 1; // Use the default user
        $validated['plan_date'] = now()->format('Y-m-d');

        $mealPlan = MealPlan::create($validated);

        return response()->json([
            'message' => 'Meal Plan created successfully!',
            'data' => $mealPlan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
