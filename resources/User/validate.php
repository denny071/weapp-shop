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
        "mobile.required" => "100009",
        "mobile.regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/" => "100010",

    ],
    "get" => [
        "page.integer" => "100011",
    ],

    "addressStore" => [
        "province.required" => "100012",
        "province.string" => "100013",
        "province_code.required" => "100014",
        "province_code.string" => "100015",
        "city.required" => "100016",
        "city.string" => "100017",
        "city_code.required" => "100018",
        "city_code.string" => "100019",
        "district.required" => "100020",
        "district.string" => "100021",
        "district_code.required" => "100022",
        "district_code.string" => "100023",
        "address.required" => "100024",
        "address.string" => "100025",
    ],

];
