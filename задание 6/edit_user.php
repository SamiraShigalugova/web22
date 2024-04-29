<?php
// Подключение к базе данных
$db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));

// Проверка, был ли передан параметр id через GET-запрос
if (!isset($_GET['id'])) {
    echo "Ошибка: ID пользователя не указан.";
    exit();
}

// Получение данных о пользователе по его ID
$stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
$stmt->execute([$_GET['id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Если пользователь с указанным ID не найден
if (!$userData) {
    echo "Пользователь с указанным ID не найден.";
    exit();
}

// Если форма была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка данных формы

    // Пример обновления данных пользователя в базе данных
    $stmt = $db->prepare("UPDATE application SET names = ?, phones = ?, email = ?, dates = ?, gender = ?, biography = ? WHERE id = ?");
    $stmt->execute([
        $_POST['names'],
        $_POST['phones'],
        $_POST['email'],
        $_POST['dates'],
        $_POST['gender'],
        $_POST['biography'],
        $_GET['id']
    ]);

    // Перенаправление на главную страницу после обновления
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
</head>
<body>
    <h1>Редактирование пользователя</h1>
    <form method="POST">
        <label for="names">Имя:</label><br>
        <input type="text" id="names" name="names" value="<?php echo $userData['names']; ?>"><br>
        <label for="phones">Телефон:</label><br>
        <input type="tel" id="phones" name="phones" value="<?php echo $userData['phones']; ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $userData['email']; ?>"><br>
        <label for="dates">Дата рождения:</label><br>
        <input type="date" id="dates" name="dates" value="<?php echo $userData['dates']; ?>"><br>
        <label for="gender">Пол:</label><br>
        <select id="gender" name="gender">
            <option value="M" <?php if ($userData['gender'] == 'M') echo 'selected'; ?>>Мужской</option>
            <option value="F" <?php if ($userData['gender'] == 'F') echo 'selected'; ?>>Женский</option>
        </select><br>
        <label for="biography">Биография:</label><br>
        <textarea id="biography" name="biography"><?php echo $userData['biography']; ?></textarea><br>
        <input type="submit" value="Сохранить изменения">
    </form>
</body>
</html>