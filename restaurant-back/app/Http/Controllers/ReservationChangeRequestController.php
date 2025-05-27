<?php

namespace App\Http\Controllers;

use App\Models\ReservationChangeRequest;
use App\Models\Reservation;
// use App\Services\MongoLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Change Requests",
 *     description="Menaxhimi i kërkesave për ndryshime ose fshirje rezervimesh"
 * )
 */
class ReservationChangeRequestController extends Controller
{
    // protected $logger;

    // public function __construct(MongoLogger $logger)
    // {
    //     $this->logger = $logger;
    // }

    /**
     * @OA\Get(
     *     path="/api/change-requests",
     *     summary="Kthen të gjitha kërkesat për ndryshim",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista e kërkesave u kthye me sukses")
     * )
     */
    public function index()
    {
        try {
            return response()->json(
                ReservationChangeRequest::with(['reservation', 'user'])->get()
            );
        } catch (\Exception $e) {
            \Log::error('Gabim në index: ' . $e->getMessage());
            return response()->json(['message' => 'Gabim gjatë ngarkimit të kërkesave'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/change-requests/my",
     *     summary="Kthen kërkesat për ndryshim të përdoruesit të kyçur",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista e kërkesave personale u kthye")
     * )
     */
    public function my()
    {
        return ReservationChangeRequest::with('reservation')
            ->where('user_id', Auth::id())
            ->get();
    }

    /**
     * @OA\Post(
     *     path="/api/change-requests",
     *     summary="Krijon një kërkesë të re për ndryshim ose fshirje",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reservation_id", "type"},
     *             @OA\Property(property="reservation_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", enum={"edit", "delete"}, example="edit"),
     *             @OA\Property(property="new_data", type="object",
     *                 @OA\Property(property="customer_name", type="string", example="Bardh"),
     *                 @OA\Property(property="customer_phone", type="string", example="044123456"),
     *                 @OA\Property(property="reservation_time", type="string", format="date-time", example="2025-04-18 19:00:00"),
     *                 @OA\Property(property="guest_count", type="integer", example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Kërkesa u krijua me sukses"),
     *     @OA\Response(response=422, description="Kërkesë e pavlefshme")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'type' => 'required|in:edit,delete',
            'new_data' => 'nullable'
        ]);

        if (isset($validated['new_data']) && is_string($validated['new_data'])) {
            $decoded = json_decode($validated['new_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'new_data nuk është JSON valid'], 422);
            }
            $validated['new_data'] = $decoded;
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $changeRequest = ReservationChangeRequest::create($validated);

        // $this->logger->log('Create Change Request', Auth::id(), $validated);

        return response()->json($changeRequest, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/change-requests/{id}/approve",
     *     summary="Aprovon një kërkesë për ndryshim",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Kërkesa u aprovua"),
     *     @OA\Response(response=400, description="Kërkesa është tashmë e përpunuar"),
     *     @OA\Response(response=500, description="Gabim i brendshëm")
     * )
     */
    public function approve($id)
    {
        try {
            $request = ReservationChangeRequest::findOrFail($id);

            if ($request->status !== 'pending') {
                return response()->json(['message' => 'Kërkesa është tashmë e përpunuar.'], 400);
            }

            if ($request->type === 'edit') {
                $data = $request->new_data;

                if ($data instanceof \Illuminate\Support\Collection) {
                    $data = $data->toArray();
                }

                if (is_string($data)) {
                    $data = json_decode($data, true);
                }

                if (!is_array($data)) {
                    \Log::error("[Approve Error] new_data për request ID {$id} nuk është array.");
                    return response()->json(['message' => 'Të dhënat e reja janë të pavlefshme'], 422);
                }

                $reservation = Reservation::findOrFail($request->reservation_id);
                $reservation->update($data);
            }

            if ($request->type === 'delete') {
                $reservation = Reservation::findOrFail($request->reservation_id);
                $reservation->delete();
            }

            $request->status = 'approved';
            $request->approved_by = Auth::user()->name;
            $request->save();

            // $this->logger->log('Approved Change Request', Auth::id(), ['request_id' => $id]);

            return response()->json(['message' => 'Kërkesa u aprovua.']);
        } catch (\Throwable $e) {
            \Log::error("[Approve Error] Exception për request ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Gabim i brendshëm gjatë aprovimit'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/change-requests/{id}/reject",
     *     summary="Refuzon një kërkesë për ndryshim",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Kërkesa u refuzua"),
     *     @OA\Response(response=400, description="Kërkesa është tashmë e përpunuar"),
     *     @OA\Response(response=500, description="Gabim gjatë refuzimit")
     * )
     */
    public function reject($id)
    {
        try {
            $request = ReservationChangeRequest::findOrFail($id);

            if ($request->status !== 'pending') {
                return response()->json(['message' => 'Kërkesa është tashmë e përpunuar.'], 400);
            }

            $request->status = 'rejected';
            $request->approved_by = Auth::user()->name;
            $request->save();

            // $this->logger->log('Rejected Change Request', Auth::id(), ['request_id' => $id]);

            return response()->json(['message' => 'Kërkesa u refuzua.']);
        } catch (\Throwable $e) {
            \Log::error("[Reject Error] Exception për request ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Gabim i brendshëm gjatë refuzimit'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/change-requests/{id}",
     *     summary="Fshin një kërkesë për ndryshim",
     *     tags={"Change Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Kërkesa u fshi me sukses"),
     *     @OA\Response(response=403, description="Nuk keni leje për të fshirë këtë kërkesë")
     * )
     */
    public function destroy($id)
    {
        $request = ReservationChangeRequest::findOrFail($id);

        if ($request->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Nuk keni leje për të fshirë këtë kërkesë'], 403);
        }

        $request->delete();

        // $this->logger->log('Deleted Change Request', Auth::id(), ['request_id' => $id]);

        return response()->json(['message' => 'Kërkesa u fshi me sukses']);
    }
}
