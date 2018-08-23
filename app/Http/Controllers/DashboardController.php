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
}
