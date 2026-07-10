<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() {
        return response()->json(Auth::user()->notifications()->paginate(20));
    }

    public function markRead(Notification $notification) {
        $notification->update(['read_at' => now()]);
        return response()->json($notification);
    }
}
