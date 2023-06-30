<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\StoreRequest;
use App\Http\Requests\Favorite\UpdateRequest;
use App\Http\Resources\Product\MiniProductCollection;
use App\Models\Product;
use App\Models\User;
use App\Services\Favorite\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(User $user, Service $service) {

        if ($user->favorite){
            $favorite_products = \DB::table('favorite_product')->where('favorite_id', $user->favorite->id,)->get();
            $products = [];
            foreach ($favorite_products as $product){
                $product_ = Product::find($product->product_id);
                if ($product_){
                    $products += $product_ ;
                }
                else{
                    $product_variant = json_decode(\DB::select("
                        select variants_json->>0 as data , product_id
                        from variants
                        where (variants_json->>0)::jsonb  @> '{\"id\": \"$product->product_id\"}'"
                    )[0]->data, true);
                    $product_variant['price'] = (int)$product_variant['price'];
                    $product_variant['old_price'] = (int)$product_variant['old_price'];

                    $products[] = $product_variant ;
                }
            }
            return $products;
        }
        else{
            $service->create($user);
            return [];
        }
    }

    public function update(UpdateRequest $request, Service $service, User $user){
        if (!$user->favorite){
            $service->create($user);
        }
        $data = $request->validated();

        return $service->update($data, $user);
    }

    public function destroy(UpdateRequest $request, Service $service, User $user){
        $data = $request->validated();
        return $service->destroy($data, $user);
    }

    public function products(Request $request){
//        return 12;
        $data = $request->validate([
            'products' => ['required']
        ]);
        if (!count(json_decode($data['products'], true))){
            return [];
        }
        $products = [];
        foreach (json_decode($data['products'], true) as $product) {
            $_product = Product::where('id', (int)$product)->first();
            if ($_product){
                $products[] = $_product->toArray();
            }
            else{
                $variant = DB::select("
                        select variants_json->>0 as data
                        from variants
                        where (variants_json->>0)::jsonb  @> '{\"id\": \"$product\"}'"
                );
                $products[] = json_decode($variant[0]->data, true);
            }
        }
        if (count($products)){
            return new MiniProductCollection($products);
        }
        return [];
    }

}
