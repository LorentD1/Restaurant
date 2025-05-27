<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use App\Models\ReservationChangeRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function statistics()
    {
        try {
            $userCount = User::count();
            $reservationCount = Reservation::count();
            $pendingChangeRequests = ReservationChangeRequest::where('status', 'pending')->count();
            $approvedChangeRequests = ReservationChangeRequest::where('status', 'approved')->count();

            return response()->json([
                'users' => $userCount,
                'reservations' => $reservationCount,
                'pending_requests' => $pendingChangeRequests,
                'approved_requests' => $approvedChangeRequests,
            ]);
        } catch (\Throwable $e) {
            \Log::error('[Admin Statistics Error] ' . $e->getMessage());
            return response()->json(['message' => 'Gabim i brendshëm në statistikë'], 500);
        }
    }
}
