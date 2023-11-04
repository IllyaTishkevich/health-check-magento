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
        'cookies' => [
            'class' => 'yii\web\Cookie',
            'httpOnly' => true,
            'secure' => true
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
                    'route' => 'api/run',
                    'defaults' => ['entity' => 'message', 'token' => '', 'action' => 'Get'],
                ],
                [
                    'pattern' => 'api/stat/<token>/<level>/<from>/<to>',
                    'route' => 'api/run',
                    'defaults' => ['level' => '', 'token' => '', 'from' => 0, 'to' => 0, 'action' => 'Stat'],
                ],
                [
                    'pattern' => 'api/daystat/<token>',
                    'route' => 'api/run',
                    'defaults' => ['action' => 'DayStat']
                ],
                [
                    'pattern' => 'api/message/stat/<id>/<token>/<from>/<to>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'from' => 0, 'to' => 0, 'action' => 'MessageStat'],
                ],
                [
                    'pattern' => 'api/notification/set/<token>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'action' => 'notification\\Save'],
                ],
                [
                    'pattern' => 'api/notification/remove/<token>/<id:\d+>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'action' => 'notification\\Remove'],
                ],
                [
                    'pattern' => 'api/user/remove/<token>/<id:\d+>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'action' => 'user\\Remove'],
                ],
                [
                    'pattern' => 'api/user/add/<token>/<email>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'action' => 'user\\Add'],
                ],
                [
                    'pattern' => 'api/set/<entity>/<token>/<value>',
                    'route' => 'api/run',
                    'defaults' => ['token' => '', 'action' => 'SetSetting'],
                ],
                [
                    'pattern' => 'api/signin/<login>/<password>/<key>',
                    'route' => 'api/run',
                    'defaults' => ['action' => 'SignIn']
                ],
                [
                    'pattern' => 'api/log',
                    'route' => 'api/run',
                    'defaults' => ['action' => 'Log']
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
