<?php

namespace App\Transformers;


use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['properties','skus','catalog'];

    public function transform(Product $product)
    {

        return [
            'id'                => $product->id,
            "type"              => $product->type,
            "catalog_id"        => $product->catalog_id,
            "title"             => $product->title,
            "long_title"        => $product->long_title,
            "cover_image"       => $product->cover_image,
            "album_image"       => explode(",", $product->album_image),
            "content_image"     => explode(",", $product->content_image),
            "on_sale"           => $product->on_sale,
            "rating"            => $product->rating,
            "sold_count"        => $product->sold_count,
            "review_count"      => $product->review_count,
            "price"             => $product->price,
            "created_at"        => $product->created_at->toDateTimeString(),
            "updated_at"        => $product->updated_at->toDateTimeString(),
        ];
    }

    public function includeProperties(Product $product)
    {
        return $this->collection($product->properties, new ProductPropertyTransformer());
    }


    public function includeSkus(Product $product)
    {
        return $this->collection($product->skus, new ProductSkuTransformer());
    }

    public function includeCatalog(Product $product)
    {
        return $this->item($product->catalog, new CatalogTransformer());
    }
}
