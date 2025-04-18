<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    //
    protected $fillable = [
        'product_id',
        'name',
        'quantity',
        'cost',
        'price',
        'production_date',
        'expiry_date',
        'comments'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
