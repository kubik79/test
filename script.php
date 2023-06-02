<?php

declare(strict_types=1);

/**
 * Соединение с БД внутри foreach - открывается/закрывается при каждой итерации, запросы выполняются по одному - очень расточительно.
 *
 * $user_ids приходит из GET, никак не проверяется и без обработки, и проверки вставляется в sql запрос.
 * Кроме ошибок в скрипте (например, строковое значение выдаст ошибку) возможна SQL инъекция в базу данных.
 *
 * $_GET['user_ids'] может иметь пустое значение - требуется обработать.
 *
 * $user_id нужно проверять и привести к типу int - или  будет ошибка.
 */


define("DB", mysqli_connect('localhost', 'root', '123123', 'database'));

$userIds = userIdsHandler($_GET['user_ids']);
$users = getUsers($userIds);

foreach ($users as $user) {
    echo sprintf('<p><a href="/show_user.php?id=%s">%s</a></p>', $user['id'], $user['name']);
}

function userIdsHandler(string $userIdsString): string
{
    $string = preg_replace('/[^0-9,]/ui', ' ', $userIdsString);

    if (trim($string, ',') === '') {
        echo 'Отсутствуют id пользователей, которых нужно выбрать.';
        exit();
    }

    return $string;
}

function getUsers(string $userIds): array
{
    $userArray = [];

    try {
        $query = DB->query(<<<SQL
            SELECT id, name FROM users WHERE id IN ($userIds)
        SQL);
    } catch (Exception $exception) {
        throw new RuntimeException('Запрос в базу данных не выполнен.');
    }

    while ($user = $query->fetch_array()) {
        $userArray[$user['id']] = $user['name'];
    }

    DB->close();

    return $userArray;
}
