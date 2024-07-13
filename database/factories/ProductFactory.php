<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product; 
use App\Models\Company; 

class ProductFactory extends Factory
{
    

    protected $model = Product::class;  

    public function definition(): array
{
    return [
        'company_id' => Company::factory(),
        'product_name' => $this->faker->word,  
        'price' => $this->faker->numberBetween(100, 10000),  
        'stock' => $this->faker->randomDigit,  
        'comment' => $this->faker->sentence,  
        'img_path' => $this->faker->optional()->imageUrl(200, 300, 'products', true, 'Dummy Image'), 
    ];
}

}

