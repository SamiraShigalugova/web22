<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
  }
  // Включаем содержимое файла form.php.
  include('form.php');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.

// Проверяем ошибки.
$errors = FALSE;
if (empty($_POST['fio'])) {
  print('Заполните имя.<br/>');
  $errors = TRUE;
}
if (empty($_POST['tel'])) {
  print('Заполните телефон.<br/>');
  $errors = TRUE;
}
if (empty($_POST['email'])) {
  print('Заполните почту.<br/>');
  $errors = TRUE;
}
if (empty($_POST['gender'])) {
  print('Выберите пол.<br/>');
  $errors = TRUE;
}
if (empty($_POST['year'])) {
  print('Выберите год.<br/>');
  $errors = TRUE;
}

// Проверяем выбор языков программирования
if (empty($_POST['choosing']) || !is_array($_POST['choosing'])) {
  print('Выберите хотя бы один язык программирования.<br/>');
  $errors = TRUE;
}
if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  exit();
}

// Сохранение в базу данных.

$user = 'u67419'; // Заменить на ваш логин uXXXXX
$pass = '8693464'; // Заменить на пароль, такой же, как от SSH
$db = new PDO('mysql:host=localhost;dbname=u67419', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

// Подготовленный запрос. Не именованные метки.
try {
  $stmt = $db->prepare("INSERT INTO form (fio, tel, email, gender, bio, contract, year) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$_POST['fio'], $_POST['tel'], $_POST['email'], $_POST['gender'], $_POST['bio'], isset($_POST['contract']) ? 1 : 0, $_POST['year']]);
} catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}
// Получаем ID последней вставленной записи
$form_id = $db->lastInsertId();

// Вставляем выбранные языки программирования в таблицу programming_languages
try {
  $stmt = $db->prepare("INSERT INTO programming_languages (form_id, language) VALUES (?, ?)");
  foreach ($_POST['choosing'] as $language) {
    $stmt->execute([$form_id, $language]);
  }
} catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}


//  stmt - это "дескриптор состояния".
 
//  Именованные метки.
//$stmt = $db->prepare("INSERT INTO test (label,color) VALUES (:label,:color)");
//$stmt -> execute(['label'=>'perfect', 'color'=>'green']);
 
//Еще вариант
/*$stmt = $db->prepare("INSERT INTO users (firstname, lastname, email) VALUES (:firstname, :lastname, :email)");
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$firstname = "John";
$lastname = "Smith";
$email = "john@test.com";
$stmt->execute();
*/

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');
