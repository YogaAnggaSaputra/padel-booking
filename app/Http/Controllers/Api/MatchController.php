<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Match;
use App\Models\MatchParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function index(Club $club) {
        return response()->json($club->matches()->with('participants')->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_players' => 'required|integer|min:2',
            'skill_level_min' => 'required|integer|min:1|max:10',
            'skill_level_max' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);
        $validated['club_id'] = $club->id;
        $validated['created_by'] = Auth::id();
        $match = Match::create($validated);
        MatchParticipant::create(['match_id' => $match->id, 'user_id' => Auth::id(), 'skill_level' => $validated['skill_level_min'], 'is_confirmed' => true, 'joined_at' => now()]);
        return response()->json($match, 201);
    }

    public function join(Request $request, Club $club, Match $match) {
        $validated = $request->validate(['user_id' => 'required|exists:users,id']);
        MatchParticipant::create(array_merge($validated, ['match_id' => $match->id, 'joined_at' => now()]));
        return response()->json(['message' => 'Joined'], 201);
    }
}
