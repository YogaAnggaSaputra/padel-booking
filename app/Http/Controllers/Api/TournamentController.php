<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Club $club) {
        return response()->json($club->tournaments()->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate([
            'name' => 'required', 'slug' => 'required', 'format' => 'required',
            'registration_start' => 'required|date', 'registration_end' => 'required|date|after:registration_start',
            'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date',
            'entry_fee' => 'nullable|numeric', 'max_participants' => 'nullable|integer',
        ]);
        $validated['club_id'] = $club->id;
        $validated['status'] = 'draft';
        $tournament = Tournament::create($validated);
        return response()->json($tournament, 201);
    }

    public function show(Club $club, Tournament $tournament) {
        return response()->json($tournament->load('matches', 'registrations'));
    }
}
