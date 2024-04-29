<?php

// Проверка HTTP-авторизации
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
}

// Подключение к базе данных и выполнение запросов
$db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));

// Извлечение данных пользователей
$stmt = $db->query("SELECT * FROM application");
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Вывод данных в виде таблицы
echo '<table border="1">';
echo '<tr><th>Имя</th><th>Телефон</th><th>Email</th><th>Год рождения</th><th>Пол</th><th>Биография</th><th>Языки программирования</th><th>Действия</th></tr>';
foreach ($usersData as $userData) {
    // Вывод данных пользователя в ячейки таблицы
    echo '<tr>';
    echo '<td>' . $userData['names'] . '</td>';
    echo '<td>' . $userData['phones'] . '</td>';
    echo '<td>' . $userData['email'] . '</td>';
    echo '<td>' . $userData['dates'] . '</td>';
    echo '<td>' . $userData['gender'] . '</td>';
    echo '<td>' . $userData['biography'] . '</td>';

    // Извлечение языков программирования для данного пользователя
    $stmt = $db->prepare("SELECT title FROM application_languages WHERE id = ?");
    $stmt->execute([$userData['id']]);
    $userLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<td>' . implode(', ', $userLanguages) . '</td>';

    // Действия: редактирование и удаление
    echo '<td><a href="edit_user.php?id=' . $userData['id'] . '">Редактировать</a> | <a href="delete_user.php?id=' . $userData['id'] . '">Удалить</a></td>';
    echo '</tr>';
}
echo '</table>';

// Вывод статистики по языкам программирования
$stmt = $db->query("SELECT name_of_language, COUNT(*) AS count FROM application_languages GROUP BY name_of_language");
$languagesStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Статистика по языкам программирования</h2>';
echo '<ul>';
foreach ($languagesStats as $languageStat) {
    echo '<li>' . $languageStat['name_of_language'] . ': ' . $languageStat['count'] . ' пользователей</li>';
}
echo '</ul>';

?>
