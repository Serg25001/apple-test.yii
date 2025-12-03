<?php
try {
    $pdo = new PDO(
        'mysql:host=MySQL-8.0;port=3306;dbname=apptest;charset=utf8',
        'root',
        '', // ← попробуйте с паролем, если есть
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Подключение успешно!\n";
    var_dump($pdo->query("SELECT 1")->fetch());
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}