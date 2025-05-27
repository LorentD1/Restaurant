<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationChangeRequest;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function overview()
    {
        $totalReservations = Reservation::count();
        $reservationsToday = Reservation::whereDate('created_at', today())->count();
        $mostUsedTables = Reservation::select('table_id', DB::raw('COUNT(*) as total'))
            ->groupBy('table_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('table')
            ->get();

        $changeRequests = ReservationChangeRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'total_reservations' => $totalReservations,
            'reservations_today' => $reservationsToday,
            'most_used_tables' => $mostUsedTables,
            'change_requests_summary' => $changeRequests
        ]);
    }
}
