<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function mine(Request $r)
    {
        $today = now()->toDateString();

        $bookings = Booking::with('room')
            ->orderBy('check_in_date', 'desc')
            ->get();

        return [
            'upcoming' => $bookings->where('status', 'confirmed')->where('check_out_date', '>', $today)->values(),
            'past' => $bookings->filter(fn($b) => $b->status === 'cancelled' || $b->check_out_date <= $today)->values(),
        ];
    }
    public function show($id)
    {
        $booking = Booking::with(['room', 'user'])
            ->findOrFail($id);

        return response()->json($booking);
    }


    public function store(Request $r)
    {
        $r->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($r->room_id);

        // Check availability
        $overlap = Booking::where('room_id', $room->id)
            ->where(function ($q) use ($r) {
                $q->whereBetween('check_in_date', [$r->check_in_date, $r->check_out_date])
                    ->orWhereBetween('check_out_date', [$r->check_in_date, $r->check_out_date]);
            })->exists();

        if ($overlap) return response()->json(['error' => 'Room not available'], 422);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'check_in_date' => $r->check_in_date,
            'check_out_date' => $r->check_out_date,
            'guests' => $r->guests
        ]);

        return response()->json($booking);
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) return response()->json(['error' => 'Unauthorized'], 403);
        $booking->delete();
        return response()->json(['ok' => true]);
    }
}
