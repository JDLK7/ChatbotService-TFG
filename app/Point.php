<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\PointFactoryException;

class Point extends Model
{
    use Uuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'points';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * FactoryMehtod para instanciar los diferentes tipos de puntos.
     *
     * @param string $type
     * @return \App\Point
     */
    public static function make(string $type): Point {
        $point = null;

        switch ($type) {
            case 'crosswalk':
                $point = new CrosswalkPoint();
                break;
            case 'works':
                $point = new WorksPoint();
                break;
            case 'urbanFurniture':
                $point = new UrbanFurniturePoint();
                break;

            default:
                throw new PointFactoryException($type);
                break;
        }

        return $point;
    }
}
