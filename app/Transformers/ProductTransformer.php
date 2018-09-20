<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->title,
            'minPrice' => $product->price,
            'pic' => $product->image,
        ];
    }
}