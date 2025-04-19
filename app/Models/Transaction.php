<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
        'type',
        'entity_type',
        'entity_id',
        'date',
        'time'
    ];

    public function entity() {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }
}
