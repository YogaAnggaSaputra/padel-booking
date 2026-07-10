<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    public function index() {
        return response()->json(Club::active()->paginate(20));
    }

    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required', 'slug' => 'required|unique:clubs,slug', 'city' => 'required']);
        $club = Club::create($validated);
        ClubMember::create(['club_id' => $club->id, 'user_id' => Auth::id(), 'role' => 'owner', 'joined_at' => now()]);
        return response()->json($club, 201);
    }

    public function show(Club $club) {
        return response()->json($club->load('courts', 'coaches'));
    }

    public function update(Request $request, Club $club) {
        $validated = $request->validate(['name' => 'sometimes|string', 'slug' => 'sometimes|string|unique:clubs,slug,' . $club->id]);
        $club->update($validated);
        return response()->json($club);
    }
}
