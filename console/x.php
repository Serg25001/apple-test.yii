<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV')   or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// важно!
require __DIR__ . '/../common/config/bootstrap.php';
require __DIR__ . '/config/bootstrap.php';


$config = require __DIR__ . '/config/main.php';

$app = new yii\console\Application($config);

$user = new \common\models\User();
$user->username = 'admin';
$user->email = 'admin@example.com';
$user->setPassword('123456');
$user->generateAuthKey();

if ($user->save()) {
    echo "Admin created successfully\n";
} else {
    print_r($user->errors);
}
