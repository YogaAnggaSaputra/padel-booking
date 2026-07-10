<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Club $club, Coach $coach) {
        return response()->json($coach->lessons()->paginate(20));
    }

    public function store(Request $request, Club $club, Coach $coach) {
        $validated = $request->validate(['court_id' => 'nullable|exists:courts,id', 'start_time' => 'required|date', 'end_time' => 'required|date|after:start_time', 'max_students' => 'required|integer|min:1', 'price_per_student' => 'required|numeric']);
        $validated['club_id'] = $club->id;
        $validated['total_amount'] = $validated['price_per_student'];
        $lesson = $coach->lessons()->create($validated);
        return response()->json($lesson, 201);
    }

    public function enroll(Request $request, Club $club, Coach $coach, Lesson $lesson) {
        $validated = $request->validate(['user_id' => 'required|exists:users,id']);
        $lesson->enrollments()->create(array_merge($validated, ['status' => 'confirmed']));
        $lesson->increment('current_students');
        return response()->json(['message' => 'Enrolled'], 201);
    }
}
