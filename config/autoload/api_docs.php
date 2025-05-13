<?php

declare(strict_types=1);

use Mine\GlobalResponse;
use function Hyperf\Support\env;

return [
    // enable false 将不会启动 swagger 服务
    'enable' => env('APP_ENV') !== 'prod',
    'format' => 'json',
    'output_dir' => BASE_PATH . '/runtime/swagger',
    'prefix_url' => env('API_DOCS_PREFIX_URL', '/swagger'),
    // 替换验证属性
    'validation_custom_attributes' => true,
    /*
|--------------------------------------------------------------------------
| 设置全局返回的代理类
|--------------------------------------------------------------------------
|
| 全局返回 如:[code=>200,data=>null] 格式,设置会后会全局生成对应文档
| 配合ApiVariable注解使用,示例参考GlobalResponse类
| 返回数据格式可以利用AOP统一返回
|
*/
    'global_return_responses_class' => GlobalResponse::class,
    // 全局responses
    'responses' => [
        ['response' => 401, 'description' => 'Unauthorized'],
        ['response' => 500, 'description' => 'System error'],
    ],
    // swagger 的基础配置  会映射到OpenAPI对象
    'swagger' => [
        'info' => [
            'title' => 'MineAdmin API DOC',
            'version' => '1.1',
            'description' => 'MineAdmin后台接口列表',
        ],
        'servers' => [
            [
                'url' => 'http://127.0.0.1:9501',
                'description' => '本地服务器',
            ],
        ],
        'components' => [
            'securitySchemes' => [
                [
                    'securityScheme' => 'Authorization',
                    'type' => 'apiKey',
                    'in' => 'header',
                    'name' => 'Authorization',
                ],
            ],
        ],
        'security' => [
            ['Authorization' => []],
        ],
        'externalDocs' => [
            'description' => 'Find out more about Swagger',
            'url' => 'https://github.com/tw2066/api-docs',
        ],
    ],
];
