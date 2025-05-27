<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Helpers\MongoLogger;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return Reservation::with(['table', 'user'])->get();
        }

        return Reservation::with('table')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|regex:/^[0-9]+$/',
            'reservation_time' => 'required|date',
            'guest_count' => 'required|integer|min:1',
        ]);

        $requestedTime = \Carbon\Carbon::parse($request->reservation_time);

        $table = Table::findOrFail($request->table_id);
        if ($request->guest_count > $table->seats) {
            return response()->json([
                'message' => "Tavolina e zgjedhur ka vetëm {$table->seats} vende dhe nuk mund të strehojë {$request->guest_count} persona."
            ], 409);
        }

        $start = $requestedTime->copy()->subHours(2)->subMinutes(59);
        $end = $requestedTime->copy()->addHours(2)->addMinutes(59);

        $conflictingReservation = Reservation::where('table_id', $request->table_id)
            ->whereBetween('reservation_time', [$start, $end])
            ->first();

        if ($conflictingReservation) {
            $freeTime = \Carbon\Carbon::parse($conflictingReservation->reservation_time)
                        ->addHours(2)->addMinutes(59)
                        ->format('H:i');

            return response()->json([
                'message' => "Kjo tavolinë është e rezervuar deri në orën $freeTime."
            ], 409);
        }

        $reservation = Reservation::create([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'guest_count' => $request->guest_count,
            'user_id' => Auth::id(),
        ]);

        

        return response()->json($reservation, 201);
    }

    public function show($id)
    {
        $reservation = Reservation::with('table')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Nuk ke akses te kjo rezervim'], 403);
        }

        return $reservation;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|regex:/^[0-9]+$/',
            'reservation_time' => 'required|date',
        ]);

        $reservation = Reservation::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Nuk ke akses të editosh këtë rezervim'], 403);
        }

        $reservation->update([
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'guest_count' => $request->guest_count,
        ]);

        return $reservation;
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Nuk ke akses të fshish këtë rezervim'], 403);
        }

        $reservation->delete();

        return response()->json(['message' => 'Rezervimi u fshi me sukses']);
    }
}
