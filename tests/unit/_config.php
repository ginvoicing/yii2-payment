<?php
return [
    'id' => 'payment-tests',
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
            'dsn' => 'sqlite:' . codecept_output_dir('/db.sqlite')
        ],
        'payment' => [
            'class' => \yii\payment\Gateway::class,
            'logging' => [
                'connection' => 'db',
                'tableName' => 'ginni_payment_logs'
            ],
            'providers' => [
                'razorpay' => [
                    'class' => \yii\payment\provider\RazorPay::class,
                    'apiKey' => getenv('RAZORPAY_API_KEY'),
                    'apiSecret' => getenv('RAZORPAY_API_SECRET')
                ]
            ]
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
