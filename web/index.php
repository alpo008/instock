<?php

ini_set('memory_limit', '256M');

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

function dump($var) {
    \yii\helpers\VarDumper::dump($var, 10, true);
}

(new yii\web\Application($config))->run();
