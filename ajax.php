<?php
   // Набор констант для использования при работе с MySQLI. Расположение на верху позволяет
   // удобно редактировать их в случае смены базы. Так как тестировался код на XAMPP, то
   // и использован был стандартный набор: хост localhost, пользователь root и база testbase
   // Такой подход не является безопасным в реальном проекте, по причине неограниченных прав
   // пользователя root.   
   define("HOST", "localhost");
   define("USER", "root");
   define("PASSWORD", "");
   define("BASE", "testbase");
   $link = mysqli_connect(HOST, USER, PASSWORD, BASE);
   session_start();
   // Функция позволяет защитить данные от попытки провести инъекцию в SQL запрос,
   // а так же удаляет лишние пробелы, и нейтрализует некоторые специальные символы.
   function protectData(&$arg){
       $arg = stripslashes($arg);
	   $arg = htmlspecialchars($arg);
       $arg = trim($arg);
	   return $arg;
    }
	// Функция, позволяющая получить IP от глобальной переменной.
    function getIP()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }
	// Функция проверяющая, попадает ли дата регистрации в заданный диапазон.
    function compareDate($offset_d, $offset_m, $offset_y, $regDate){
		// Создаем новый объект класса DateTime, получая его сдвинув на столько лет назад, сколько нужно.
        $date = new DateTime('-'. $offset_y .' years');
		// Модифицируем объект класса DateTime, получая его сдвинув на столько месяцев и дней назад, сколько нужно.
	    $date->modify('-'. $offset_m .' months');
	    $date->modify('-'. $offset_d .' days');
		// Получаем ассоциативный массив путем парсинга даты, которую мы получили из базы. 
	    $arrTime = date_parse($regDate);
		// Создаем новый объект класса DateTime, получая его из элементов полученного массива.
	    $regDate = new DateTime($arrTime['year'].'-'.$arrTime['month'].'-'.$arrTime['day']);
		// Определяем, попала ли дата в промежуток. Если вернется +, то дата находится за промежутком
	    // Если вернулся -, значит дата в промежуток попала.
        $difference = $regDate->diff($date);
        if ($difference->format('%R') == '+'){
		    return false;
        } else {
		    return true;
	    }
    }
	// Получив объект data типа POST начинаем его обработку. Сначала рассматриваем, что вызвало переход в ajax.php
	// в $_POST['type'] могут быть только 3 значения - login, если мы запросили вход, registration, если мы запросили
	// регистрацию и exit, если мы покидаем сессию.
    switch ($_POST['type']) {
        case 'login':
		    // Обработка логина и пароля для избежания проникновения вредоносного кода
            $login = isset($_POST['login']) ? protectData($_POST['login']) : '';
            $password = isset($_POST['password']) ? protectData($_POST['password']) : '';
			// Формируем запрос, чтобы проверить, есть ли такой пользователь в базе данных.
            $query = 'SELECT * FROM users WHERE login = "'. $login .'"';
            $result = mysqli_query($link, $query);
			// Получаем ассоциативный массив результата запроса.
            $userRow = mysqli_fetch_array($result, MYSQLI_ASSOC);
			// Если запрос пришел "пустой", то возвращаем строку NO_SUCH_LOGIN, что свидетельствует
			// об отсутствии такого пользователя.
            if (empty($userRow['password'])) {
	            echo "NO_SUCH_LOGIN";
			// В противном случае сверяем пароль функцией password_verify, потому что пароли хранятся зашифрованными.
            } else {
			// Если пароли совпали, то передаем значения в глобальную переменную $_SESSION, удаляем полученные логин и пароль
			// и возвращаем строку SUCCESS.
	            if (password_verify($password, $userRow['password'])){
		            $_SESSION['login'] = $userRow['login']; 
		            $_SESSION['id'] = $userRow['id'];
				    $_SESSION['password'] = $userRow['password'];
					if ($_POST['save']) {
                        //Если пользователь хочет, чтобы его данные сохранились для    последующего входа, то сохраняем в куках его браузера
                        setcookie("login",    $_POST["login"], time()+9999999);
                        setcookie("password",    $_POST["password"], time()+9999999);
					}
				    unset($login);
				    unset($password);
		            echo "SUCCESS";
			    // В случае если пароли не совпали, то возвращаем строку WRONG_PASSWORD.
	            } else {
		            echo "WRONG_PASSWORD";
	            }
            }
	    break;
	    case 'registration':
		    // Обработка полученных данных для избежания проникновения вредоносного кода
	    	$login = protectData($_POST['login']);
		    $password = protectData($_POST['password']);
			// Пол не требует обработки, потому что получен от радиокнопки.
		    $sex = $_POST['sex'];
		    $email = protectData($_POST['email']);
		    $city = protectData($_POST['city']);
			// Получаем IP пользователя.
		    $ip = getIP();
			// Формируем запрос, чтобы проверить, есть ли такой пользователь в базе данных.
	        $query = 'SELECT * FROM users WHERE login = "'. $login .'"';
		    $result = mysqli_query($link, $query);
			$userRow = mysqli_fetch_array($result, MYSQLI_ASSOC);
			// Если массив пришел непустой, значит, что пользователь с таким логином уже есть,
			// а значит регистрация невозможна.
            if (!empty($userRow['password'])) {
				// Возвращаем строку LOGIN_TAKEN.
	            echo "LOGIN_TAKEN";
            } else {
				// Формируем запрос по поиску людей с таким IP.
			    $query = 'SELECT * FROM users WHERE ip = "'. $ip .'"';
		        $result = mysqli_query($link, $query);
			    $userRow = mysqli_fetch_array($result, MYSQLI_ASSOC);
				// Если такой IP найден в базе, и регистрация прошла менее чем 3 месяца назад
				// (3 месяца выбрано для демонстрации), то повторная регистрация запрещена.
			    if ($userRow['ip'] == $ip && compareDate(0,3,0, $userRow['dateOfRegister'])){
				    // Возвращаем строку DUPLICATE_IP
					echo "DUPLICATE_IP";
			    } else {
					// Если все прошло успешно, то формируем запрос на добавление данных нового пользователя.
				    $query = 'INSERT INTO users (`id`, `login`, `password`, `ip`, `dateOfRegister`, `email`, `sex`, `city`) VALUES (NULL, \''. $login .'\', \''. password_hash($password, PASSWORD_DEFAULT) .'\', \''. $ip . '\', CURRENT_TIMESTAMP, \''. $email .'\', \''. $sex .'\', \''. $city .'\')';
			        $result = mysqli_query($link, $query);
			        // Если ошибок не было, то возвращаем SUCCESS, в противном случае MYSQL_FAILURE
					if ($result){
				        echo "SUCCESS";
			        } else {
				        echo "MYSQL_FAILURE";
			        }
			    }
		    }
	    break;
		// В данный блок мы попадаем, если пользователь решил покинуть сессию.
	    case 'exit':
		    // Удаляем переменные и возвращаем сообщение об успешном завершении.
		    unset($_SESSION['login']);
		    unset($_SESSION['password']);
		    unset($_SESSION['id']);
		    echo ("SUCCESS");
	    break;
}
?>