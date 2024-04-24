<?php
require_once 'vendor/autoload.php';
require_once 'src/Models/User.php';
require_once 'src/Helpers/RandomGenerator.php';

use src\Helpers\RandomGenerator;

// POSTリクエストからパラメータを取得
$count = $_POST['count'] ?? 5;
$format = $_POST['format'] ?? 'html';

// 検証
if (is_null($count) || is_null($format)) {
    echo 'Missing parameters.';
    exit('Missing parameters.');
}

if (!is_numeric($count) || $count < 1 || $count > 100) {
    exit('Invalid count. Must be a number between 1 and 100.');
}

$allowedFormats = ['json', 'txt', 'html', 'markdown'];
if (!in_array($format, $allowedFormats)) {
    exit('Invalid type. Must be one of: ' . implode(', ', $allowedFormats));
}

// パラメータが正しい形式であることを確認
$count = (int)$count;

// ユーザーを生成
$users = RandomGenerator::users($count, $count);

if ($format === 'markdown') {
    header('Content-Type: text/markdown');
    header('Content-Disposition: attachment; filename="users.md"');
    foreach ($users as $user) {
        echo $user->toMarkdown();
    }
} elseif ($format === 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="users.json"');
    $usersArray = array_map(fn($user) => $user->toArray(), $users);
    echo json_encode($usersArray);
} elseif ($format === 'txt') {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="users.txt"');
    foreach ($users as $user) {
        echo $user->toString();
    }
} else {
    // HTMLをデフォルトに
    header('Content-Type: text/html');
    foreach ($users as $user) {
        echo $user->toHTML();
    }
}