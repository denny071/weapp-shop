<?php

return [

    "add" => [
        "amount.required" => "120001",
        "amount.numeric" => "120002",
        "sku_id.required" => "120003",
        "sku_id.integer" => "120004",
    ],
    "update" => [
        "amount.required" => "120001",
        "amount.numeric" => "120002",
        "checked.in:0,1" => "120005",
    ],


];
