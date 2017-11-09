<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => '雪莲贴管理后台',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'homeUrl' => '/admin',
    "modules" => [
        "admin" => [
            "class" => "mdm\admin\Module",
        ],
    ],
    "aliases" => [
        "@mdm/admin" => "@vendor/mdmsoft/yii2-admin",
    ],
    'components' => [
        "authManager" => [
            "class" => 'yii\rbac\DbManager', //这里记得用单引号而不是双引号
            "defaultRoles" => ["guest"],
        ],
        'request' => [
            'baseUrl' => '/admin',
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'urlManager' => [
            //启用URL美化功能
            'enablePrettyUrl' => true,
            //是否在URL中显示入口脚本。是对美化功能的进一步补充。
            'showScriptName' => false,
            //是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则
            'enableStrictParsing' => false,
//            'suffix' => '.html',  // 伪后缀
            'rules'=>[
//              'Admin/login'=>'Admin/default/login',
                "<controller:\w+>/<id:\d+>"=>"<controller>/view",
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>"
            ],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //这里是允许访问的action
            //controller/action
            '*'
        ]
    ],
    'on beforeRequest' => function($event) {
    \yii\base\Event::on(
        \yii\db\BaseActiveRecord::className(), 
        \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE, 
        ['backend\components\AdminLog', 'write']
        );
    },
    'params' => $params,
];
