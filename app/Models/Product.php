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

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function cartedBy($user)
    {
        return Cart::where("user_id", $user->id)->where("product_id", $this->id);
    }

}
