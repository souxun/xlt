<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'homeUrl' => '/',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'urlManager' => [
            //启用URL美化功能
            'enablePrettyUrl' => true,			//是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则
            'enableStrictParsing' => false,
            //是否在URL中显示入口脚本。是对美化功能的进一步补充。
            'showScriptName' => false,
            'rules' => [
                "wx"=>"weixin/index",
//            "scancode"=>"weixin/scan-offline-code",
//            "C/<f:\w+>"=>"weixin/wx-client",
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>"
            ],
        ],
    ],
    'params' => $params,
];
