<?php

return [

    "support" => [
        "name"  => env("MAIL_FROM_NAME"),
        "email" => env("SUPPORT_EMAIL"),
        "phone" => env("SUPPORT_PHONE"),
    ],
    
    "sms_service" => [
        "api_url"    => env("SMS_API_URL", null),
        "api_token"  => env("SMS_API_TOKEN", null),
        "sid"        => env("SMS_API_SID", null)
    ],

    // otp
    "otp" => [
        "service_enable"     => ["SMS","EMAIL"], // "SMS", "EMAIL"
        "maximum_per_user"   => 10,
        "expired_in_minutes" => 5,
    ],

    // cms file
    "cms" => [
        "title"       => "Starter Kit",
        "short_title" => "SSK",
        "website"     => "www.sylnovia.com",
        "head_office" => [
            "address"   => "Suvastu Muskan Tower, Level-9, 56 Gulshan Avenue, Road-132, Gulshan-1, Dhaka-1212",
            "phone"     => "+880-2-9513396",
            "hot_line"  => "+88 01701 217876",
        ]
    ],

    "company" => [
        "company_name"     => "Sylnovia",
        "short_name"       => "Sylnovia",
        "address"          => "Dhaka, Bangladesh",
        "email"            => "info@sylnovia.com",
        "phone"            => "+880123456789",
        "meta_title"       => "Sylnovia - Best Software Company",
        "meta_desc"        => "Sylnovia is a leading software company.",
        "meta_tags"        => "software, web, app",
        "logo_white"       => null,
        "logo_dark"        => null,
        "logo_white_small" => null,
        "logo_dark_small"  => null,
        "favicon"          => null,
    ],

    "theme" => [
        "layout_type"     => "vertical",
        "layout_width"    => "fluid",
        "layout_position" => "fixed",
        "topbar_color"    => "light",
        "sidebar_color"   => "light",
        "sidebar_size"    => "lg",
        "layout_mode"     => "light",
    ],
];
