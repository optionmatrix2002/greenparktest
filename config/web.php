<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'timeZone' => 'Asia/Kolkata',
    'components' => [
		'events' => [
            
            'class' => 'app\components\EventsComponent'
        
        ],
        'scheduler' => [
            
            'class' => 'app\components\SchedulerComponent'
        
        ],
        'authManager' => [
            'class' => 'app\components\AuthManager'
        ],
        'utils'=>[
            'class'=>'app\components\UtilsComponent'
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'tMDA3TQA0UVPjoZ62_2uFPqU_1tFMQKS',
			 'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => true,
            'authTimeout' => 1800, //30 minutes
            'identityCookie' => ['name' => '_identity'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,			
		'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.cloudzimail.com',
            'username' => 'audit.corp@hotelgreenpark.com',
            'password' => 'Greenpark@1',
            'port' => '587',     
			'encryption' => 'TLS'
        ],
		
        ],
        /*'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],*/
		'session' => [
            'class' => 'yii\web\Session',
            'name' => 'advanced-frontend',
            'timeout' => 60,
        ],
		'log' => [
            'flushInterval' => 100,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => require (__DIR__ . '/log_targets.php')
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                'site/login'=>'site',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'defaultRoute' => '/site/index',
				[
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user'
                ]
            ],
        ],
    ],
	'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module'
        ],
		'gridview' => [
            'class' => '\kartik\grid\Module',
            'downloadAction' => 'gridview/export/download'
        ]
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
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
