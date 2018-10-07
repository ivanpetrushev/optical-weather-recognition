<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->input('query');

        if ($query) {
            $data = Location::where('name', 'like', '%'.$query.'%')->get();
        } else {
            $data = Location::all();
        }

        $total = Location::count();
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total
        ]);
    }

}