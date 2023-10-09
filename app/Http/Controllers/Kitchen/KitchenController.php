<?php

namespace App\Http\Controllers\Kitchen;

use App\Events\IngredientMin;
use App\Events\ToCasher;
use App\Events\ToWaiter;
use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\ExtraIngredient;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use App\Models\Table;
use Carbon\Carbon;

class KitchenController extends Controller
{
    use ApiResponseTrait;

    public function getOrders(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',1)->where('is_paid',0)->get();
        if($orders) {
        return $this->apiResponse(OrderResource::collection($orders), 'this Orders are Befor_Preparing', 200);

        }
        return $this->apiResponse(null, 'No Order Befor_Preparing', 404);

    }

    public function ChangeToPreparing(Order $order)
    {
        if ($order->status = 1) {
            $order->update([
                'status' => 2,
                'time_start' => now()
            ]);
            $order->save();
            return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 200);

        }

    } 
    public function getToDone(Branch $branch)
    {
        $orders = Order::where('branch_id',$branch->id)->where('status',2)->get();
        if($orders){
            return $this->apiResponse(OrderResource::collection($orders), 'This orders are Perparing', 200);  

        }
        return $this->apiResponse($orders, 'Not Found', 404);  

    }

    public function ChangeToDone(Order $order)
    {
        if ($order->status = '2'){
            $order->update([
                'status' => '3',
                'time_end' => now(),
            ]);
            $order->save();
            event(new ToWaiter($order));
            $branch = $order->branch;
            $bill = Bill::where('id',$order->bill_id)->where('is_paid',0)->latest()->first();
            if($bill) {
                event(new ToCasher($bill,$branch));
            }
            
            return $this->apiResponse(OrderResource::make($order), 'Changes saved successfully', 201);
        }
    }

    public function delete(Order $order)
    {
        $order->delete();
        return $this->apiResponse(null,'Deleted Successfully',200);

    }
}
