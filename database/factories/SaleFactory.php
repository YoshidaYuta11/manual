<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
