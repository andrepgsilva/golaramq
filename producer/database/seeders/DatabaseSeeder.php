<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'John Constantine',
            'email' => 'john@example.com',
        ]);

        $productsNumberToCreate = 1;
        $productsNumberToTake = 1;

        $products = Product::factory($productsNumberToCreate)->create();
        $products = $products->take($productsNumberToTake);
        $productsSum = $products->sum(function($product) {
            return $product->price;
        });

        $productsIds = $products->map(function($product) {
            return $product->id;
        });

        $order = Order::create([
            'user_id' => User::first()->id,
            'total' => $productsSum
        ]);

        $order->products()->sync($productsIds);        
    }
}
