<?php

// Проверка наличия идентификатора пользователя в запросе
if (!isset($_POST['id'])) {
    header("Location: admin.php");
    exit();
}
$user_id = $_POST['id'];

// Подключение к базе данных
try {
    $db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    exit();
}

// Удаляем пользователя из базы данных
try {
    $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: admin.php");
    exit();
} catch (PDOException $e) {
    echo "Ошибка удаления пользователя: " . $e->getMessage();
    exit();
}

?>
