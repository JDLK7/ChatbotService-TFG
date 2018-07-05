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

    protected static function boot() {
        parent::boot();

        /**
         * Crea la primera versión del punto que en principio
         * no tendrá ningún usuario asociado.
         */
        static::created(function (Point $point) {
            $version = new PointVersion();
            $version->point()->associate($point);
            $version->save();
        });
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Devuelve todas las versiones de un punto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions() {
        return $this->hasMany(PointVersion::class, 'point_id', 'id');
    }

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
