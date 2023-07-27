<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', __DIR__);
Yii::setAlias('@data', __DIR__ . DIRECTORY_SEPARATOR . '_data');
Yii::setAlias('@yii/payment/migrations', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'migrations');
