<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name'=>'HealthCheck',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '12011993',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailerGmail' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'healthcheck.magenmagic@gmail.com',
                'password' => 'qwe321bl89',
                'port' => '587',
                'encryption' => 'tls',
            ],
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api\log'],
                [
                    'pattern' => 'api/get/<token>/<entity>',
                    'route' => 'api/get',
                    'defaults' => ['entity' => 'message', 'token' => ''],
                ],
                [
                    'pattern' => 'api/stat/<token>/<level>/<from>/<to>',
                    'route' => 'api/stat',
                    'defaults' => ['level' => '', 'token' => '', 'from' => 0, 'to' => 0],
                ],
                [
                    'pattern' => 'api/daystat/<token>',
                    'route' => 'api/daystat'
                ],
                [
                    'pattern' => 'api/message/stat/<id>/<token>/<from>/<to>',
                    'route' => 'api/messtat',
                    'defaults' => ['token' => '', 'from' => 0, 'to' => 0],
                ],
                [
                    'pattern' => 'api/notification/set/<token>',
                    'route' => 'api/savenotification',
                    'defaults' => ['token' => ''],
                ],
                [
                    'pattern' => 'api/notification/remove/<token>/<id:\d+>',
                    'route' => 'api/removenotification',
                    'defaults' => ['token' => ''],
                ],
                [
                    'pattern' => 'api/user/remove/<token>/<id:\d+>',
                    'route' => 'api/removeuser',
                    'defaults' => ['token' => ''],
                ],
                [
                    'pattern' => 'api/user/add/<token>/<email>',
                    'route' => 'api/adduser',
                    'defaults' => ['token' => ''],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1','172.18.0.*'],
    ];
}

return $config;
