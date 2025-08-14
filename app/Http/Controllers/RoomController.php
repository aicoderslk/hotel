<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Public list
    public function index()
    {
        return Room::orderBy('id')->get();
    }

    // Check availability for given date range (ISO YYYY-MM-DD)
    public function availability(Request $r, Room $room)
    {
        $r->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $in = $r->check_in;
        $out = $r->check_out;

        $overlap = $room->bookings()
            ->where('status', 'confirmed')
            ->where('check_in_date', '<', $out)
            ->where('check_out_date', '>', $in)
            ->exists();


        return ['available' => !$overlap];
    }

    public function show($id)
    {
        return Room::find($id);
    }
}
