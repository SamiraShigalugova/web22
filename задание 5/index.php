<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['names'] = !empty($_COOKIE['names_error']);
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['date'] = !empty($_COOKIE['date_error']);
  $errors['gender'] = !empty($_COOKIE['gender_error']);
  $errors['languages'] = !empty($_COOKIE['languages_error']);
  $errors['biography'] = !empty($_COOKIE['biography_error']);
  $errors['agree'] = !empty($_COOKIE['agree_error']);



  // Выдаем сообщения об ошибках.
  if (!empty($errors['names'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('names_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
  if (!empty($errors['phone'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('phone_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните телефон.</div>';
  }
  if (!empty($errors['email'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('email_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните почту.</div>';
  }
  if (!empty($errors['date'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('date_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните год рождения.</div>';
  }
  if (!empty($errors['gender'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('gender_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Выберите пол.</div>';
  }
  if (!empty($errors['languages'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('languages_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Выберите язык.</div>';
  }
  if (!empty($errors['biography'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('biography_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните биографию.</div>';
  }
  if (!empty($errors['agree'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('agree_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните соглашение.</div>';
  }


  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['names'] = empty($_COOKIE['names_value']) ? '' : strip_tags($_COOKIE['names_value']);
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : strip_tags($_COOKIE['phone_value']);
  $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
  $values['data'] = empty($_COOKIE['data_value']) ? '' : strip_tags($_COOKIE['data_value']);
  $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
  $values['biography'] = empty($_COOKIE['biography_value']) ? '' : strip_tags($_COOKIE['biography_value']);
  $values['agree'] = empty($_COOKIE['agree_value']) ? '' : $_COOKIE['agree_value']; 
  if (empty($_COOKIE['language_value'])) {
    $values['language'] = array();
} else {
    $values['language'] = json_decode($_COOKIE['language_value'], true);  
}
  $language = isset($language) ? $language : array();

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
        $db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));
        
        $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
        $stmt->execute([$_SESSION['uid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $values['names'] = strip_tags($row['names']);
        $values['phone'] = strip_tags($row['phone']);
        $values['email'] = strip_tags($row['email']);
        $values['data'] = $row['data'];
        $values['gender'] = $row['gender'];
        $values['biography'] = strip_tags($row['biography']);
        $values['agree'] = true; 

        $stmt = $db->prepare("SELECT * FROM languages WHERE id = ?");
        $stmt->execute([$_SESSION['uid']]);
        $ability = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($language, strip_tags($row['name_of_language']));
        }
        $values['language'] = $language;
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['names'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('names_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
    // Сохраняем ранее введенное в форму значение на месяц.
setcookie('names_value', $_POST['names'], time() + 30 * 24 * 60 * 60);
if (empty($_POST['phone'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('phone_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);


if (empty($_POST['email'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('email_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);


if (empty($_POST['date'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('date_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('date_value', $_POST['date'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['gender'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('gender_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['languages'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('languages_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
$language_string = implode(",", $_POST['languages']);
setcookie('languages_value', $language_string, time() + 30 * 24 * 60 * 60);
$languages_array = explode(",", $_COOKIE['languages_value']);

if (empty($_POST['biography'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('biography_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['agree'])) {
  // Выдаем куку на день с флажком об ошибке в поле fio.
  setcookie('agree_error', '1', time() + 24 * 60 * 60);
  $errors = TRUE;
}
setcookie('agree_value', $_POST['agree'], time() + 30 * 24 * 60 * 60);

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('names_error', '', 100000);
    setcookie('phone_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('date_error', '', 100000);
    setcookie('gender_error', '', 100000);
    setcookie('languages_error', '', 100000);
    setcookie('biography_error', '', 100000);
    setcookie('agree_error', '', 100000);
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
        $db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));
        
        $stmt = $db->prepare("UPDATE application SET names = ?, phone = ?, email = ?, data = ?, gender = ?, biography = ? WHERE id = ?");
        $stmt->execute([$_POST['names'], $_POST['phone'], $_POST['email'], $_POST['data'], $_POST['gender'], $_POST['biography'], $_SESSION['uid']]);

        $stmt = $db->prepare("DELETE FROM languages WHERE id = ?");
        $stmt->execute([$_SESSION['uid']]);

        $ability = $_POST['language'];

        foreach ($language as $item) {
            $stmt = $db->prepare("INSERT INTO application_languages SET id = ?, name_of_language = ?");
            $stmt->execute([$_SESSION['uid'], $item]);
        }
  }
  else {
    // Генерируем уникальный логин и пароль.
    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    $max=rand(8,16);
    $size=StrLen($chars)-1;
    $pass=null;
    while($max--)
        $pass.=$chars[rand(0,$size)];
    $login = $chars[rand(0,25)] . strval(time());
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('pass', $pass);

    $db = new PDO('mysql:host=localhost;dbname=u67419', 'u67419', '8693464', array(PDO::ATTR_PERSISTENT => true));

        $stmt = $db->prepare("INSERT INTO application SET namess = ?, phones = ?, email = ?, dates = ?, gender = ?, biographys = ?");
        $stmt->execute([$_POST['names'], $_POST['phone'], $_POST['email'], $_POST['data'], $_POST['gender'], $_POST['biography']]);
        
        $res = $db->query("SELECT max(id) FROM application");
        $row = $res->fetch();
        $count = (int) $row[0];

        $ability = $_POST['language'];

        foreach ($language as $item) {
            $stmt = $db->prepare("INSERT INTO application_languages SET id = ?, name_of_language = ?");
            $stmt->execute([$count, $item]);
        }

        // Запись в таблицу login_pass
        $stmt = $db->prepare("INSERT INTO login_pass SET id = ?, login = ?, pass = ?");
        $stmt->execute([$count, $login, md5($pass)]);
    }

  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
?>
