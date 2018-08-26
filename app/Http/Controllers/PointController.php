<?php

namespace App\Http\Controllers;

use App\Point;
use Illuminate\Http\Request;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\CrosswalkPoint;

class PointController extends Controller
{
    /**
     * @var \App\Services\PointService
     */
    private $pointService;

    public function __construct()
    {
        $this->pointService = new PointService();
    }

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

    /**
     * Devuelve los puntos que corresponden a un determinado 
     * tipo de problema de accesibilidad detectado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pointsByAlertType(Request $request) {
        $request->validate([
            'type' => 'required|string',
        ]);

        $type = $request->query('type');

        $query = Point::select('points.*')
            ->join('point_versions', 'points.id', 'point_versions.point_id');
        $points;

        switch ($type) {
            case 'non_existent_points':
                $points = $query->where('shouldExist', true)->where('exists', false)->get();
                break;
            
            case 'crosswalk_bad_visibility':
                $points = $query->where('properties->visibility', 'bad')->get();
                break;
            
            case 'crosswalk_no_curb_ramps':
                $points = $query->where('properties->hasCurbRamps', 'false')->get();
                break;

            case 'obstacle_points':
                $points = Point::whereType('obstacle')->get();
                break;

            default: $points = [];
                break;
        }

        // JSON_NUMERIC_CHECK sirve para que NO se serialicen los números como strings.
        return response()->json($points, 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Devuelve un punto con sus versiones y datos sobre sus revisiones.
     *
     * @param \App\Point $point
     * @return \Illuminate\Http\Request
     */
    public function show(Point $point) {
        if (is_a($point, CrosswalkPoint::class)) {
            return response()->json(
                $this->pointService->crosswalkPointWithDetails($point)
            );
        }

        return response()->json($point);
    }
}
