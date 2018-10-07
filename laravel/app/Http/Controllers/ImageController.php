<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{

    public function index(Request $request)
    {
        $query = Image::orderBy('taken_date', 'asc')
            ->orderBy('taken_time', 'asc');

        $where = [];
        $locationId = $request->input('location_id');
        if ($locationId) {
            $where['location_id'] = $locationId;
        }

        $cameraId = $request->input('camera_id');
        if ($cameraId) {
            $where['camera_id'] = $cameraId;
        }

        $takenDate = $request->input('taken_date');
        if ($takenDate) {
            $where['taken_date'] = $takenDate;
        }

        $query->where($where);
        $total = $query->count();

        $offset = $request->input('start', 0);
        $limit = $request->input('limit', 20);
        $query->limit($limit)->offset($offset);

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $total
        ]);
    }

    public function display(Request $request, $id)
    {
        $id = (int) $id;
        $image = Image::find($id);
        $fullpath = $image->dir . '/' . $image->filename;
//        print_r($fullpath); exit();
        header('Content-type: image/jpeg');
        echo file_get_contents($fullpath);
        exit();
    }

}