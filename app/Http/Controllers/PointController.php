<?php

namespace App\Http\Controllers;

use App\Point;
use Illuminate\Http\Request;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    public function import(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimetypes:text/xml',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->store('imports');

        $pointService = new PointService();
        $pointService->importFromXml(basename($path));

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * A partir de las coordenadas dadas obtiene el punto más 
     * cercano si se encuentra dentro de la distancia máxima.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearestPoint(Request $request) {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'threshold' => 'nullable|integer',
        ]);

        $threshold = $request->query('threshold', 10);
        $currentPosition = new Point([
            'latitude' => $request->query('lat'),
            'longitude' => $request->query('lng'),
        ]);

        $nearest = $currentPosition->nearestPoint();
        
        if ($currentPosition->distanceTo($nearest) > $threshold) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ningún punto cercano',
                'point' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Punto cercano encontrado',
            'point' => $nearest,
        ]);
    }

    public function index(Request $request) {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|integer',
        ]);

        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 200);

        $points = Point::whereRaw("ST_DWithin(
                Geography(location),
                Geography(ST_MakePoint($lng, $lat)),
                $radius
            )")->get();

        return response()->json([
            'success' => true,
            'message' => "Puntos en un radio de $radius metros",
            'points' => $points,
        ]);
    }
}
