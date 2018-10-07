<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Camera;
use Illuminate\Support\Facades\Log;

class CameraController extends Controller
{

    public function index(Request $request)
    {
        $order = [
            'field' => 'name',
            'direction' => 'asc'
        ];

        $query = Camera::orderBy($order['field'], $order['direction']);

        $where = [];
        $locationId = $request->input('location_id');
        if ($locationId) {
            $where[] = ['location_id' => $locationId];
        }

        if (count($where) == 1) {
            $where = $where[0];
        }
        $query->where($where);
        $total = $query->count();

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $page = $request->input('page', -1);
        if ($page > -1) {
            $query->limit($limit)->offset($limit * $offset);
        } else {
            $query->limit($limit)->offset($offset);
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total
        ]);
    }

}