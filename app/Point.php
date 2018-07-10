<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\PointFactoryException;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Point extends Model
{
    use Uuids, SingleTableInheritanceTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'points';

    /**
     * Nombre de la columna por la que se diferencia el modelo a instanciar.
     *
     * @var string
     */
    protected static $singleTableTypeField = 'type';

    /**
     * Subclases cuyos modelos se mapean en la misma tabla.
     *
     * @var array
     */
    protected static $singleTableSubclasses = [
        WorksPoint::class,
        CrosswalkPoint::class,
        UrbanFurniturePoint::class,
    ];

    protected static function boot() {
        parent::boot();

        /**
         * Crea la primera versión del punto que en principio
         * no tendrá ningún usuario asociado.
         */
        static::created(function (Point $point) {
            $point->createVersion();
        });
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Devuelve el nombre que el usuario para el tipo de punto.
     *
     * @return string
     */
    public function getDisplayNameAttribute() : string {
        return __('points/types.' . static::$singleTableType);
    }

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
    public static function make(string $type) : Point {
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

    /**
     * Genera una versión nueva del punto sin guardarla en la base de datos.
     *
     * @param \App\User $creator
     * @return \App\PointVersion
     */
    public function makeVersion(User $creator = null) : PointVersion {
        $version = new PointVersion();
        $version->point()->associate($this);

        if (isset($creator)) {
            $version->user()->associate($creator);
        }

        return $version;
    }

    /**
     * Genera y guarda en la base de datos una versión nueva del punto.
     *
     * @param \App\User $creator
     * @return \App\PointVersion
     */
    public function createVersion(User $creator = null) : PointVersion {
        $version = $this->makeVersion($creator);
        $version->save();

        return $version;
    }
}
