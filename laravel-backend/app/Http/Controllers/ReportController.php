<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reports;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Reports::all();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'description' => 'required|string',
            'status' => 'string|in:pending,approved,rejected',
        ]);

        $report = Reports::create($validated);
        return response()->json($report, 201);
    }

    public function show($id)
    {
        $report = Reports::findOrFail($id);
        return response()->json($report);
    }

    public function update(Request $request, $id)
    {
        $report = Reports::findOrFail($id);

        $validated = $request->validate([
            'type' => 'string',
            'description' => 'string',
            'status' => 'string|in:pending,approved,rejected',
        ]);

        $report->update($validated);
        return response()->json($report);
    }

    public function destroy($id)
    {
        $report = Reports::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
