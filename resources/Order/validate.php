<?php

return [

    "index" => [
        "order_status.required"             => "140011",
        "order_status.string"               => "140012",
    ],

    "store" => [
        "express_id.required"           => "140003",
        "express_id.integer"            => "140004",
        "address_id.required"           => "140005",
        "address_id.integer"            => "140006",
        "coupon_code.nullable"          => "140007",
        "coupon_code.string"            => "140007",
        "product_sku_ids.required"      => "140008",
        "product_sku_ids.string"        => "140009",
        "remark.nullable"               => "140010",
        "remark.string"                 => "140010",
    ],

];
