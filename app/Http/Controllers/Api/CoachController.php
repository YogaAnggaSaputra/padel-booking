<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    public function index(Club $club) {
        return response()->json($club->coaches()->active()->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate(['user_id' => 'required|exists:users,id', 'hourly_rate' => 'required|numeric', 'bio' => 'nullable|string']);
        $coach = $club->coaches()->create($validated);
        return response()->json($coach, 201);
    }

    public function show(Club $club, Coach $coach) {
        return response()->json($coach->load('user', 'availability', 'lessons'));
    }
}
