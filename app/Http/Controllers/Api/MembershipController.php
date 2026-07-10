<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\MembershipPlan;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function plans(Club $club) {
        return response()->json($club->membershipPlans()->active()->paginate(20));
    }

    public function storePlan(Request $request, Club $club) {
        $validated = $request->validate(['name' => 'required', 'slug' => 'required|unique:membership_plans,club_id,' . $club->id . ',club_id', 'price' => 'required|numeric', 'duration_days' => 'required|integer']);
        $plan = $club->membershipPlans()->create($validated);
        return response()->json($plan, 201);
    }

    public function subscribe(Request $request, Club $club, MembershipPlan $plan) {
        $membership = UserMembership::create([
            'user_id' => Auth::id(), 'club_id' => $club->id, 'plan_id' => $plan->id,
            'status' => 'active', 'starts_at' => now(), 'expires_at' => now()->addDays($plan->duration_days),
        ]);
        return response()->json($membership, 201);
    }

    public function myMemberships(Request $request) {
        return response()->json(Auth::user()->memberships()->with('club', 'plan')->paginate(20));
    }
}
