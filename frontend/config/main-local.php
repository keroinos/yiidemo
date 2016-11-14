<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '6bkxo_kzitabd1yfmKstYX2hQiXtLbRF',
        ],
       /* 'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => '<lang:\w+>/test/ca',
                    'route' => 'test/ca',
                ]
            ],
        ],*/
        'db' => new yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => 'root123',
            'charset' => 'utf8',
        ]),
        'userinitdb' => new yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=userinitdb',
            'username' => 'root',
            'password' => 'root123',
            'charset' => 'utf8',
            'on afterOpen' => function($event) {
                // $event->sender refers to the DB connection
                //$event->sender->createCommand("SET time_zone = 'UTC'")->execute();
            }
        ]),
        'userdb' => new yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=userdb',
            'username' => 'root',
            'password' => 'root123',
            'charset' => 'utf8',
            'on afterOpen' => function($event) {
                // $event->sender refers to the DB connection
                //$event->sender->createCommand("SET time_zone = 'UTC'")->execute();
            }
        ]),

    ],
    'aliases' =>
        [
            '@uploadedfilesdir' => '@app/web/upload'
        ],
    'timeZone' => 'GMT',
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
