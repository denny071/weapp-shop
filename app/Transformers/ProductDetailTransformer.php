<?php

namespace App\Transformers;


use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductDetailTransformer extends TransformerAbstract
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
            "album_image"       => $this->getImageList($product->album_image),
            "content_image"     => $this->getImageList($product->content_image),
            "on_sale"           => $product->on_sale,
            "rating"            => $product->rating,
            "sold_count"        => $product->sold_count,
            "review_count"      => $product->review_count,
            "price"             => $product->price,
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

    public function getImageList($images)
    {
        $data = [];
        if(!$images) {
            return $data;
        }
        foreach(explode(",",$images) as $image) {
            $data[] = ['picture_url' => $image];
        }
        return $data;
    }
}
