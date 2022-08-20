<?php

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cart', function(): JsonResponse {
    $user = User::first();
    $order = Order::first();
    $product = $order->products()->first();
    $data = [
        'data' => [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'order_id' => $order->id,
            'order_total' => $order->total,
            'product_name' => $product->name,
            'product_image' => $product->image_url
        ]
    ];

    $message = json_encode($data);

    $exchangeName = 'store_order_exchange';
    $routingKey = 'order_confirmation';
    $connection = AMQPStreamConnection::create_connection([
        ['host' => 'producermq', 'port' => '5672', 'user' => 'user', 'password' => 'password'],
    ]);

    $channel = $connection->channel();

    $message = new AMQPMessage($message);
    $channel->batch_basic_publish($message, $exchangeName, $routingKey);
    $channel->publish_batch();

    return response()->json($data);
});