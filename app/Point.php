<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NoPointsException;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\PointFactoryException;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Point extends Model
{
    use Uuids, SingleTableInheritanceTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'latitude',
        'longitude',
        'location',
        'type',
        'created_at',
        'updated_at',
    ];

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
        ObstaclePoint::class,
        CrosswalkPoint::class,
        UrbanFurniturePoint::class,
    ];

    protected static function boot() {
        parent::boot();

        /**
         * Calcula la localización geográfica para PostGIS.
         */
        static::saving(function (Point $point) {
            $point->location = DB::raw("ST_SetSRID(ST_MakePoint($point->longitude, $point->latitude), 4326)");
        });

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
     * Alias para el atributo "latitude"
     *
     * @return float
     */
    public function getLatAttribute() {
        return $this->latitude;
    }

    /**
     * Alias para el atributo "longitude"
     *
     * @return float
     */
    public function getLngAttribute() {
        return $this->longitude;
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
    public static function make(string $type, array $attributes = []) : Point {
        $point = null;

        switch ($type) {
            case 'crosswalk':
                $point = new CrosswalkPoint($attributes);
                break;
            case 'works':
                $point = new WorksPoint($attributes);
                break;
            case 'urbanFurniture':
                $point = new UrbanFurniturePoint($attributes);
                break;
            case 'obstacle':
                $point = new ObstaclePoint($attributes);
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

    /**
     * Devuelve el punto más cercano que no haya sido
     * revisado por el usuario autentificado.
     *
     * @throws \App\Exceptions\NoPointsException
     * @return \App\Point
     */
    public function nearestPoint() : Point {
        $user = auth()->user();

        // Query geoespacial para obtener los puntos más cercanos.
        $query = DB::table('points')->orderBy(
            DB::raw("location <-> st_setsrid(st_makepoint($this->longitude, $this->latitude), 4326)")
        );

        // Si hay un usuario autentificado se descartan los puntos que haya revisado anteriormente.
        if (isset($user)) {
            $query = $query->whereNotIn('id', function ($subQuery) use ($user) {
                $subQuery->select('point_id')
                    ->from('point_versions')
                    ->whereUserId($user->id);
            });
        }

        $nearest = $query->first();

        // Si no se ha encontrado el punto más cercano significa que la BD está vacía.
        if (is_null($nearest)) {
            throw new NoPointsException();
        }

        return Point::make($nearest->type, (array) $nearest);
    }

    /**
     * Devuelve la distancia a otro punto en metros.
     *
     * @param \App\Point $point
     * @return float
     */
    public function distanceTo(Point $point) : float {
        $earthRadius = 6371000;

        $dLat = deg2rad($point->lat - $this->lat);
        $dLng = deg2rad($point->lng - $this->lng);

        $lat1 = deg2rad($this->lat);
        $lat2 = deg2rad($point->lat);

        $a = pow(sin($dLat / 2), 2) + pow(sin($dLng / 2), 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
