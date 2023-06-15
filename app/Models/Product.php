<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'content', 
        'quantity', 
        'category', 
        'image', 
        'time', 
        'seller_id'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

}
