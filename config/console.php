<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'timeZone' => 'Asia/Kolkata',
    'controllerNamespace' => 'app\commands',
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
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
			'useFileTransport' => false,
            'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'smtp.cloudzimail.com',//'smtp.gmail.com',
            'username' => 'audit.corp@hotelgreenpark.com',//'audit.corp@hotelgreenpark.com',
            'password' => 'Greenpark@1',//'Greenpark2017',
            'port' => '587',     
			'encryption' => 'TLS'
        ],
		],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
