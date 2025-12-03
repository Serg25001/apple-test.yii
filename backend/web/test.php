<?php
// Включаем вывод всех ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Yii debug и dev mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// Подключаем автозагрузку Composer и Yii
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

// Подключаем конфигурации backend
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php',
    [
        'components' => [
            // Подключаем компонент db из common
            'db' => require __DIR__ . '/../../common/config/db.php',
        ],
    ]
);

// Создаём приложение
$application = new yii\web\Application($config);

// Теперь безопасно работать с ActiveRecord
use common\models\User;

// Пробуем найти пользователя admin
$user = User::findByUsername('admin');

echo "<pre>";
if ($user) {
    echo "Admin найден!\n";
    print_r([
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'status' => $user->status,
    ]);
} else {
    echo "Admin не найден\n";
}
echo "</pre>";
