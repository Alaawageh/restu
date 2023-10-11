<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'name_ar','branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,'product_ingredient')->withPivot('is_remove');
    }
    public function extraIngredients()
    {
        return $this->belongsToMany(Ingredient::class,'product_extra_ingredient')->withPivot('price_per_piece');
    }
    public function orderProducts()
    {
        return $this->belongsToMany(OrderProduct::class, 'remove_ingredients')->withPivot('order_product_id','ingredient_id');
    }
    public function orderProduct()
    {
        return $this->belongsToMany(OrderProduct::class,'order_products');
                    
    }

}
