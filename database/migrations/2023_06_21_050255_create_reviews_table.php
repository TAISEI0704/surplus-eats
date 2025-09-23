<?php

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('content');
            $table->string('rating_taste');
            $table->string('rating_price');
            $table->string('rating_service');
            $table->integer('rating_total');
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Seller::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
