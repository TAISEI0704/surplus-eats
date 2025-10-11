<?php

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informations', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('date')->useCurrent();
            $table->string('content');
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Seller::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informations');
    }
};
