<?php
	// Страница, на которую попадает пользователь после удачного входа.
	// Демонстрирует, что сессионные переменные сохраняют свое значение и приводит немного информации.
	session_start();
    define("HOST", "localhost");
    define("USER", "root");
    define("PASSWORD", "");
    define("BASE", "testbase");
    $link = mysqli_connect(HOST, USER, PASSWORD, BASE);
	// Информация отображается только для пользователей, которые вошли в систему.
	if (isset($_SESSION['login']) && isset($_SESSION['password'])){
	    echo "Поздравляю, ".$_SESSION['login'].", ты вошел!</br>";
	    echo "Ты был зарегистрирован ".$_SESSION['id']."-м.</br>";
	    $query = "SELECT * FROM users";
	    $result = mysqli_query($link, $query);
	    echo "Всего зарегистрировано ".mysqli_num_rows($result).".</br>";
	} else {
		// В противном случае выдается предупреждение.
		echo "Данная страница только для заристрированных пользователей!</br>";
	}
	echo "<a href=\"index.php\">Назад</a>";
    
?>
