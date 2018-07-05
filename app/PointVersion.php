<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class PointVersion extends Model
{
    use Uuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'point_versions';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Devuelve el punto al que pertenece la versión.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function point() {
        return $this->belongsTo(Point::class);
    }

    /**
     * Devuelve el usuario que creó la versión.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
