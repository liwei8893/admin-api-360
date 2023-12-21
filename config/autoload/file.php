<?php

declare(strict_types=1);
return [
    'default' => 'qiniu',
    'storage' => [
        'local' => [
            'driver' => \Hyperf\Filesystem\Adapter\LocalAdapterFactory::class,
            'root' => __DIR__ . '/../../public/' . \Hyperf\Support\env('UPLOAD_PATH', 'uploadfile'),
        ],
        'oss' => [
            'driver' => \Hyperf\Filesystem\Adapter\AliyunOssAdapterFactory::class,
            'accessId' => '',
            'accessSecret' => '',
            'bucket' => '',
            'endpoint' => '',
            'domain' => '',
            'schema' => 'http://',
            'isCName' => false,
            // 'timeout'        => 3600,
            // 'connectTimeout' => 10,
            // 'token'          => '',
        ],
        'qiniu' => [
            'driver' => \Hyperf\Filesystem\Adapter\QiniuAdapterFactory::class,
            'accessKey' => \Hyperf\Support\env('QINIU_ACCESS_KEY'),
            'secretKey' => \Hyperf\Support\env('QINIU_SECRET_KEY'),
            'bucket' => \Hyperf\Support\env('QINIU_BUCKET'),
            'domain' => \Hyperf\Support\env('QINIU_DOMAIN'),
        ],
        'cos' => [
            'driver' => \Hyperf\Filesystem\Adapter\CosAdapterFactory::class,
            'region' => '',
            'domain' => '',
            'schema' => 'http://',
            // overtrue/flysystem-cos ^2.0 配置如下
            'credentials' => [
                'appId' => '',
                'secretId' => '',
                'secretKey' => '',
            ],
            // overtrue/flysystem-cos ^3.0 配置如下
            // 'app_id' => env('COS_APPID'),
            // 'secret_id' => env('COS_SECRET_ID'),
            // 'secret_key' => env('COS_SECRET_KEY'),
            // 可选，如果 bucket 为私有访问请打开此项
            // 'signed_url' => false,
            'bucket' => '',
            'read_from_cdn' => false,
            // 'timeout'         => 60,
            // 'connect_timeout' => 60,
            // 'cdn'             => '',
            // 'scheme'          => 'https',
        ],
        'ftp' => [
            'driver' => \Hyperf\Filesystem\Adapter\FtpAdapterFactory::class,
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
            // 'port' => 21,
            // 'root' => '/path/to/root',
            // 'passive' => true,
            // 'ssl' => true,
            // 'timeout' => 30,
            // 'ignorePassiveAddress' => false,
        ],
        'memory' => [
            'driver' => \Hyperf\Filesystem\Adapter\MemoryAdapterFactory::class,
        ],
        's3' => [
            'driver' => \Hyperf\Filesystem\Adapter\S3AdapterFactory::class,
            'credentials' => [
                'key' => \Hyperf\Support\env('S3_KEY'),
                'secret' => \Hyperf\Support\env('S3_SECRET'),
            ],
            'region' => \Hyperf\Support\env('S3_REGION'),
            'version' => 'latest',
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => false,
            'endpoint' => \Hyperf\Support\env('S3_ENDPOINT'),
            'bucket_name' => \Hyperf\Support\env('S3_BUCKET'),
        ],
        'minio' => [
            'driver' => \Hyperf\Filesystem\Adapter\S3AdapterFactory::class,
            'credentials' => [
                'key' => \Hyperf\Support\env('S3_KEY'),
                'secret' => \Hyperf\Support\env('S3_SECRET'),
            ],
            'region' => \Hyperf\Support\env('S3_REGION'),
            'version' => 'latest',
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true,
            'endpoint' => \Hyperf\Support\env('S3_ENDPOINT'),
            'bucket_name' => \Hyperf\Support\env('S3_BUCKET'),
        ],
    ],
];
