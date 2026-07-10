<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Court;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitlistController extends Controller
{
    public function store(Request $request, Club $club, Court $court) {
        $validated = $request->validate(['desired_start_time' => 'required|date', 'desired_end_time' => 'required|date|after:desired_start_time', 'number_of_players' => 'required|integer', 'guest_name' => 'nullable|string', 'guest_email' => 'nullable|email', 'guest_phone' => 'nullable|string']);
        $validated['club_id'] = $club->id;
        $validated['court_id'] = $court->id;
        $validated['user_id'] = Auth::id();
        $validated['expires_at'] = now()->addDays(7);
        $entry = WaitlistEntry::create($validated);
        return response()->json($entry, 201);
    }

    public function index(Request $request, Club $club, Court $court) {
        return response()->json($court->waitlistEntries()->waiting()->paginate(20));
    }
}
