<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'brand_id',
        'name',
        'image',
        'quantity',
        'package_average_cost',
        'package_average_price',
        'description'
    ];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function productions() {
        return $this->hasMany(Brand::class);
    }

    public function transactions() {
        return $this->morphTo(Transaction::class, 'entity', 'entity_type', 'entity_id');
    }
}
