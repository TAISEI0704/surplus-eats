<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'content', 
        'quantity', 
        'category', 
        'image', 
        'available_time', 
        'seller_id'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_products')->withPivot('quantity');
    }

    // public function cartedBy($user)
    // {
    //     return Cart::where("user_id", $user->id)->where("product_id", $this->id);
    // }

    public function purchase_histories()
    {
        return $this->hasMany(purchaseHistory::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

}
