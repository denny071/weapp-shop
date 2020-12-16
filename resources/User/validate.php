<?php


return [

    "update" => [

        "name.nullable" => "100001",
        "name.string" => "100001",
        "email.nullable" => "100002",
        "email.email" => "100002",
        "introduction.nullable" => "100003",
        "introduction.string" => "100003",
        "avatar_image_id.nullable" => "100004",
        "avatar_image_id.integer" => "100004",
    ],
    "bindMobile" => [
        "verification_key.required" => "100005",
        "verification_key.string" => "100006",
        "verification_code.required" => "100007",
        "verification_code.string" => "100008",
        "phone.required" => "100009",
        "phone.regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/" => "100010",

    ]
];
