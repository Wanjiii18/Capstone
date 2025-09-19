<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karenderia;

class KarenderiaController extends Controller
{
    //
    public function showKarenderiaProfile($id)
    {
        $karenderia = Karenderia::findOrFail($id)->toArray();

        return view('karenderia.karenderiaProfile', ['karenderia' => $karenderia]);
    }

    public function showKarenderiaDashboard()
    {
        $karenderias = Karenderia::select('id', 'name', 'description')
            ->whereIn('status', ['active', 'approved']) 
            ->get()
            ->toArray();

        return view('karenderia.karenderiaDash', ['karenderias' => $karenderias]);
    }

    public function edit($id)
    {
        $karenderia = Karenderia::findOrFail($id);
        return view('karenderia.karenderiaCRUD', ['karenderia' => $karenderia]);
    }

    public function update(Request $request, $id)
    {
        $karenderia = Karenderia::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'status' => 'required|in:pending,approved,active,inactive,rejected',
        ]);

        $karenderia->update($validatedData);

        return redirect()->route('karenderia.karenderiaDash')
            ->with('success', 'Karenderia updated successfully.');
    }

    public function approve($id)
    {
        $karenderia = Karenderia::findOrFail($id);
        $karenderia->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('karenderia.karenderiaProfile', ['id' => $karenderia->id])
            ->with('success', 'Karenderia approved successfully.');
    }

    public function destroy($id)
    {
        $karenderia = Karenderia::findOrFail($id);
        $karenderia->delete();

        return redirect()->route('karenderia.karenderia')
            ->with('success', 'Karenderia removed successfully.');
    }
}
