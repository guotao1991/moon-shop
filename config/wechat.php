<?php

return [
    // 必要配置
    'app_id' => env("WECHAT_APP_ID"), // 公众号 AppID 或企业号 CorpID
    'secret' => env("WECHAT_SECRET"), // 公众号 AppSecret 或企业号 CorpSecret
    'token' => env("WECHAT_TOKEN"), // 公众号 Token 或企业号 AccessToken
    'aes_key' => env("WECHAT_ENCODING_AES_KEY"), // 公众号 EncodingAESKey 或企业号 EncodingAESKey

    // 更多配置项
    'response_type' => 'array',
    'log' => [
        'level' => 'debug',
        'file' => __DIR__ . '/wechat.log',
    ],

    "miniprogram" => [
        "app_id" => env("WECHAT_MINIPROGRAM_APPID"),
        "secret" => env("WECHAT_MINIPROGRAM_SECRET"),
    ],

    "platform" => [
        "app_id" => env("WECHAT_OFFICIAL_ACCOUNT_APPID"),
        'secret' => env("WECHAT_OFFICIAL_ACCOUNT_SECRET"),
        'token' => env("WECHAT_OFFICIAL_ACCOUNT_TOKEN"),
        'aes_key' => env("WECHAT_OFFICIAL_ACCOUNT_AES_KEY"),
    ]
];
