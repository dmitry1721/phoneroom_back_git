<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyWarehouse\ConnectRequest;
use App\Http\Requests\MyWarehouse\ExportRequest;
use App\Jobs\OrderExportJob;
use App\Jobs\SendOrderEmailJob;
use App\Jobs\SendTelegramNotificationJob;
use App\Models\MyWarehouse;
use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Models\Variant;
use App\Notifications\OrderToTelegram;
use App\Notifications\Telegram;
use App\Services\MyWarehouse\Service;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Request;

class OrderController  extends Controller
{

    public function index(){

        $orders = Order::all();
        $products = [];
        $order_product = \DB::table("order_products")->select('product_id', 'order_id', 'quantity', 'price', 'created_at')->get();
        foreach ($order_product as $op) {
            if (Product::where('id', $op->product_id)->first()){
                if (isset($products[$op->order_id])){
                    $products[$op->order_id][] = [
                        Product::where('id', $op->product_id)->first()->toArray(),
                        $op->quantity,
                        $op->price,
                    ];
                } else{
                    $products[$op->order_id] = [[
                        Product::where('id', $op->product_id)->first()->toArray(),
                        $op->quantity,
                        $op->price,
                    ]];
                }
            }
            else{
                foreach (Variant::all() as $variants){
                    if (is_string($variants->variants_json) ? (int)json_decode($variants->variants_json, true)['id'] === $op->product_id
                        : $variants->variants_json['id'] === $op->product_id){
                        $variant = is_string($variants->variants_json) ? json_decode($variants->variants_json, true) : $variants->variants_json;
                        if (isset($products[$op->order_id])){
                            $products[$op->order_id][] = [
                                $variant,
                                $op->quantity,
                                $op->price,
                            ];
                        } else{
                            $products[$op->order_id] = [[
                                $variant,
                                $op->quantity,
                                $op->price,
                            ]];
                        }
                    }
                }
            }
        }
        return view('admin.order.index', compact('orders', 'products'));
    }

    public function choose_delivery(Order $order, \App\Services\Order\Service $service){

        if($order->status_id === 5){
            $data = $service->get_order_products($order, false);
            $products = $data['products'];
//        $products_weight = $data['products_weight'];
            $delivery = '';
//        $delivery = $this->get_delivery_costs($order, $products_weight, 1);
            return view('admin.order.choose_delivery', compact('order', 'products', 'delivery'));
        }
        else{
            return redirect(route('admin.order.index'));
        }
    }

    public function parsel_create(Order $order, \App\Services\Order\Service $service, $delivery_id){
        $customer = User::where('id', $order->user_id)->first();
        $products = $service->get_order_products($order)['products'];
        $token = env('BOXBERRY_TOKEN', '');
        if ($delivery_id == 1){
            /*
             * $SDATA['vid'] = '1' - Доставка до ПВЗ
             * $SDATA['vid'] = '2' - Курьерская доставка
             * $SDATA['vid'] = '3' - Доставка почтой России
            * */

            $SDATA = [];
            $SDATA['order_id'] = (string)$order->id;
            $SDATA['vid'] = '2';

            $SDATA['customer'] = [
                'fio' => $customer->profile->last_name.' '.$customer->first_name.' '.$customer->profile->middle_name,
                'phone' => $customer->phone,
            ];
            if ($customer->email !== null){
                $SDATA['customer'] += [
                    'email' => $customer->email,
                ];
            }
            $SDATA['kurdost'] = [
                'index'  =>  $order->zip,
//                'citi'  =>  'Город',
                'addressp'  =>  $order->ship_address,
            ];

            foreach ($products as $i => $product) {
                $SDATA['items'][] = [
                    'id' => $product['product']['id'],
                    'name' => $product['product']['product_name'] ?? $product['product']['name'],
                    'nds' => $product['vat'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                ];
                if ($i > 0){
                    $SDATA['weights'] += [
                        'weight'.$i+1 => $product['weight'],
                    ];
                }
                else{
                    $SDATA['weights'] = [
                        'weight' => $product['weight'],
                    ];
                }
            }
            dd($SDATA);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.boxberry.ru/json.php');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'token' => $token,
                'method' => 'ParselCreate',
                'sdata' => json_encode($SDATA)
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = json_decode(curl_exec($ch),1);

            dd($data);
        }
        elseif ($delivery_id == 2){
            dd(112313);
        }
        dd($delivery_id);
        return view('admin.order.index');
    }

    public function update_delivery(){
        return view('admin.order.chose_delivery');
    }

    public function get_delivery_costs(Order $order, $products_weight, $delivery_id)
    {
        if ($delivery_id === 1){
            $token = env('BOXBERRY_TOKEN', '');
            $zip = $order->zip;
            $url = 'https://api.boxberry.ru/json.php?token='.$token.'&method=DeliveryCosts&weight='.$products_weight.'&zip='.$zip;
            $handle = fopen($url, "rb");
            $contents = stream_get_contents($handle);
            fclose($handle);
            $data=json_decode($contents,true);

            return $data;
        }
        elseif ($delivery_id === 2){
            return '';
        }
        return '';
    }

    public function export(\App\Services\MyWarehouse\Service $service){
        $myWarehouse = MyWarehouse::select('token')->first();
//        $agent = $service->getAgent($myWarehouse, 'Гасанян Дмитрий');
//        $order_data = [
//            'ship_address' => '107664, адрес адрес адрес',
//            'positions' => [],
//        ];
//        $contract = $service->createContract($myWarehouse, $agent, 10, 22333);
//        $newOrder = $service->createOrder($myWarehouse, $agent, 10, $order_data, '', $contract);
////        return $newOrder;
//        $file = $service->createExportFile($myWarehouse, $newOrder['id']);
//        ['uri' => $filepath] = stream_get_meta_data(tmpfile());
//        file_put_contents($filepath, $file);
//        if (ob_get_level()) {
//            ob_end_clean();
//        }
//        $user = User::where('id', 1)->first();
//        $user->notify(new Telegram(Order::with('status')->where('id', 10)->first(), $filepath));
//        dd(readfile($file));
//        return $file;
        $new_order = Order::with('status')->where('id', 20)->first();
        OrderExportJob::dispatch($new_order)->onConnection('redis-long-processes')->onQueue('exports');

        //        return $service->getEmbeddedTemplate($myWarehouse);
    }
}