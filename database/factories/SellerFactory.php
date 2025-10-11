<?php

namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'image' => 'seller.jpg',
            'password' => bcrypt('password'),
            'content' => $this->faker->paragraph(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'remember_token' => Str::random(10),
        ];
    }
}
