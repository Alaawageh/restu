<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $prods = Product::where('id',$product->product_id)->get();
            foreach($prods as $pro) {
                $productData = [
                    'id' => $pro->id,
                    'name' => $pro->name,
                    'name_ar' => $pro->name_ar,
                    'description' => $pro->description,
                    'description_ar' => $pro->description_ar,
                    'price' => $pro->price,
                    'image' => url($pro->image),
                    'estimated_time' => $pro->estimated_time,
                    'status' => $pro->status,
                    'qty' => $product['qty'],
                    'note' => $product['note']
                ];
            }
            if(isset($product->ingredients)){

                $removeIngredient = [];
                foreach ($product->ingredients as $Ingredient) {
                    
                    $IngredientData = [
                        'id' => $Ingredient->id,
                        'name' => $Ingredient->name,
                        
                    ];
                    $removeIngredient[] = $IngredientData;
                    
                }
                
                $productData['removeIngredient'] = $removeIngredient;
            }
            
            if(isset($product->extra)){
                
                $xx = [];
                foreach ($product->extra as $extraIngredient) {
                   
                    $ingredientData = Ingredient::find($extraIngredient['id']);
                    
                    $productExtra = ProductExtraIngredient::where('product_id',$pro->id)->where('ingredient_id',$ingredientData->id)->first();
                    if($productExtra) {
                        $extraIngredientData = [
                            'id' => $ingredientData->id,
                            'name' => $ingredientData->name,
                            'price_per_piece' => $productExtra->price_per_piece,
                        ];
                    }else{
                        $extraIngredientData = [
                            'id' => $ingredientData->id,
                            'name' => $ingredientData->name,
                        ];
                    }

                    
                    $xx[] = $extraIngredientData;
                    
                }
                
                $productData['extra'] = $xx;
            }

            
            $products[] = $productData;
        
        }
        return $products;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'takeaway' => $this->takeaway,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'is_update' => $this->is_update,
            'time' => $this->time,
            'time_start' => Carbon::parse($this->time_start)->format("Y-m-d H:i:s"),
            'time_end' => Carbon::parse($this->time_end)->format("Y-m-d H:i:s"),
            'time_Waiter' => Carbon::parse($this->time_Waiter)->format("Y-m-d H:i:s"),
            'total_price' => $this->total_price,
            'estimatedForOrder' => $this->estimatedForOrder,
            'table' => TableResource::make($this->table),
            'products' => $this->withProductsAndExtra($this->resource),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at   
            
        ];
    }
}
