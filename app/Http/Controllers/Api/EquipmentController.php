<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Equipment;
use App\Models\EquipmentRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index(Club $club) {
        return response()->json($club->equipment()->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate(['name' => 'required', 'price_per_hour' => 'required|numeric', 'quantity_total' => 'required|integer']);
        $validated['quantity_available'] = $validated['quantity_total'];
        $equipment = $club->equipment()->create($validated);
        return response()->json($equipment, 201);
    }

    public function rent(Request $request, Club $club, Equipment $equipment) {
        $validated = $request->validate(['booking_id' => 'required', 'quantity' => 'required|integer|min:1', 'pickup_at' => 'required|date', 'return_at' => 'required|date|after:pickup_at']);
        if ($equipment->quantity_available < $validated['quantity']) {
            return response()->json(['message' => 'Not enough stock'], 422);
        }
        $validated['club_id'] = $club->id;
        $validated['user_id'] = Auth::id();
        $validated['unit_price'] = $equipment->price_per_hour;
        $validated['total_price'] = $equipment->price_per_hour * $validated['quantity'];
        $validated['due_at'] = $validated['return_at'];
        $rental = EquipmentRental::create($validated);
        $equipment->decrement('quantity_available', $validated['quantity']);
        return response()->json($rental, 201);
    }
}
