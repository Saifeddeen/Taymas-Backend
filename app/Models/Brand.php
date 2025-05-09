<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    //
    use SoftDeletes;

    protected $dates = ["deleted_at"];

    protected $fillable = [
        'name',
        'image',
        'description'
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
