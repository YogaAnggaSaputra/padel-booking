<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Club;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index(Club $club) {
        return response()->json($club->courts()->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate(['name' => 'required', 'slug' => 'required|unique:courts,club_id,' . $club->id . ',club_id', 'base_price' => 'required|numeric']);
        $court = $club->courts()->create($validated);
        return response()->json($court, 201);
    }

    public function show(Club $club, Court $court) {
        return response()->json($court->load('pricing', 'availability'));
    }

    public function update(Request $request, Club $club, Court $court) {
        $validated = $request->validate(['name' => 'sometimes|string', 'base_price' => 'sometimes|numeric', 'is_available' => 'sometimes|bool']);
        $court->update($validated);
        return response()->json($court);
    }

    public function availability(Club $club, Court $court) {
        return response()->json($court->availability()->paginate(30));
    }

    public function pricing(Club $club, Court $court) {
        return response()->json($court->pricing()->paginate(30));
    }
}
