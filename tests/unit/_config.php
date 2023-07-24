<?php
return [
    'id' => 'swift-currency-tests',
    'class' => \yii\console\Application::class,
    'basePath' => \Yii::getAlias('@tests'),
    'runtimePath' => \Yii::getAlias('@tests/_output'),
    'bootstrap' => ['log'],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => [
                'yii\payment\migrations',
            ],
        ],
    ],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=127.0.0.1;port:3306;dbname=currencydb',
            'username' => 'currencyuser',
            'password' => 'password',
            'charset' => 'utf8'
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget'
                ],
            ],
        ],
    ]
];