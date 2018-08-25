<?php

namespace App\Http\Controllers;

use App\Point;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Devuelve el total de puntos y el número de puntos revisados por usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function overallRevisions() {
        $total = Point::count();
        // Puntos que tengan al menos 1 revisión con usuario
        $revised = Point::join('point_versions', 'points.id', 'point_versions.point_id')
            ->whereNotNull('user_id')->count();

        return response()->json([
            'total' => $total,
            'revised' => $revised,
        ]);
    }

    /**
     * Devuelve los 6 meses anteriores al actual y el 
     * total de revisiones para cada uno de ellos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function revisionsPerMonth() {
        $currentMonth = Carbon::now()->month;
        $months = trans('dates.months');

        // Si es un mes anterior a Julio, se duplica cada
        // mes para obtener los meses del año anterior.
        if ($currentMonth-7 < 0) {
            foreach ($months as $m) {
                array_push($months, $m);
            }
            $currentMonth += 12;
        }
        // Meses que se van a mostrar en el gráfico
        $displayedMonths = array_slice($months, $currentMonth-7, 6);

        $totalPerMonth = [];

        // Revisiones totales para cada mes
        for ($i = ($currentMonth-6); $i < $currentMonth; $i++) {
            $totalPerMonth[] = Point::join('point_versions', 'points.id', 'point_versions.point_id')
                ->whereNotNull('user_id')
                ->whereMonth('point_versions.created_at', $i)
                ->count();
        }

        return response()->json([
            'months' => $displayedMonths,
            'totalPerMonth' => $totalPerMonth,
        ]);
    }

    /**
     * Devuelve los avisos de problemas de accesibilidad que se
     * han detectado gracias a las revisiones de los usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function alerts() {
        $alerts = [];

        // Número de obstáculos varios.
        $obstacles = Point::whereType('obstacle')->count();
        if ($obstacles > 0) {
            $alerts[] = [
                'title' => 'Obstáculos',
                'category' => 'problem',
                'type' => 'obstacle_points',
                'text' => "Se han detectado $obstacles obstáculos.",
            ];
        }

        // Número de pasos de cebra que no tienen vados.
        $crossNoCurbRamps = Point::join('point_versions', 'points.id', 'point_versions.point_id')
            ->where('properties->hasCurbRamps', 'false')->count();
        if ($crossNoCurbRamps > 0) {
            $alerts[] = [
                'title' => 'Pasos de cebra sin vados',
                'category' => 'problem',
                'type' => 'crosswalk_no_curb_ramps',
                'text' => "Se han detectado $crossNoCurbRamps pasos de cebra sin vados.",
            ];
        }

        // Número de puntos que deberían existir pero según X usuarios no existen.
        $nonExistentPoints = Point::join('point_versions', 'points.id', 'point_versions.point_id')
            ->where('shouldExist', true)->where('exists', false)->count();
        if ($nonExistentPoints > 0) {
            $alerts[] = [
                'title' => 'Puntos no existentes',
                'category' => 'warning',
                'type' => 'non_existent_points',
                'text' => "Se han detectado $nonExistentPoints puntos cuya existencia no ha sido verificada.",
            ];
        }

        // Número de pasos de cebra con mala visibilidad.
        $crossBadVisibility = Point::join('point_versions', 'points.id', 'point_versions.point_id')
            ->where('properties->visibility', 'bad')->count();
        if ($crossBadVisibility > 0) {
            $alerts[] = [
                'title' => 'Pasos de cebra con mala visibilidad',
                'category' => 'warning',
                'type' => 'crosswalk_bad_visibility',
                'text' => "Se han detectado $crossBadVisibility pasos de cebra con mala visibilidad para cruzar.",
            ];
        }

        return response()->json($alerts);
    }
}
