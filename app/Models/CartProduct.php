<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// カート全体の取得ではなく、カートに入っている各商品についてのレコードを作成しているモデル
class CartProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity', 
    ];

    public function cart() : BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
