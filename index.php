<?php
	// Начало сессии.
	session_start();
?>
<script type="text/javascript" src="./js/jquery-3.1.1.js"></script>
<script type="text/javascript">
    // Функция по проверке введенных пользователем данных. 
	// Охватывает основные правила заполнения формы - логин и пароль достаточной длины,
    // пароли совпадают, емейл правильного формата, поля не пустые и радиокнопки выбраны	
	function checkData(){
		if ($("#newLoginField").val().length < 3) return "Длина логина не может быть меньше 3-х символов";
	    if ($("#newPasswordField").val().length < 6) return "Длина пароля не может быть меньше 6-и символов";
		if ($("#newPasswordField").val() != $("#newPasswordFieldDup").val()) return "Введенные пароли не совпадают";
		// Для определения правильного email'а используются регулярные выражения. данная конструкция позволяет
		// "поймать" обычные емейлы, но для серьезного проекта ее недостаточно.
		if ($("#newEmail").val().match(/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/ig) == null) return "eMail имеет неверный формат";
		if (!$("#newCity").val()) return "\"Город\" является обязательным для заполнения полем";
		if (!$("#newSexM").is(':checked') && !$("#newSexF").is(':checked')) return "\"Пол\" является обязательным для заполнения полем";
		return "";
	}
	// Основной элемент страницы регистрации. Позволяет проводить все операции не
	// заставляя пользователей переходить по множественным ссылкам.
	$( document ).ready(function() {
		// Событие, вызываемое при нажатии на клавишу входа.
		// Информация отсылается к скрипту, который обрабатывает ее в базе данных.
		// и выдает ответ. 
		$("#submitButton").on("click", function(){
			var save = $("#agree").attr("checked") == 'checked' ? true : false;
		    $.ajax({
		    	method: "POST",
		    	url: "ajax.php",
				// Описание использования параметра data в файле ajax.php.
		    	data: {type: "login", login: $("#loginField").val(), password: $("#passwordField").val(), save: save},
		    	success: function(returnVal){
					$("#resultSpan").text("");
					switch(returnVal){
						// Если пришло "SUCCESS", то значит пользователь есть в базе данных
						// и его перенаправит на страницу для зарегистрированных пользователей.
					    case "SUCCESS":
						    window.location.href = "hidden.php";
					    break;
						// Если пришло "NO_SUCH_LOGIN", то значит пользователя нет в базе данных.
						case "NO_SUCH_LOGIN":
						    $("#resultSpan").text("Пользователя с предоставленным вами логином не существует.");
					    break;
						// Если пришло "WRONG_PASSWORD", то значит пользователя есть в базе данных,
						// но с другим паролем.
					    case "WRONG_PASSWORD":
						    $("#resultSpan").text("Введенный вами пароль неверен.");
					    break;
					}
				}
		    });
	    });
		// При нажатии на кнопку регистрации скрываем форму входа и показываем форму регистрации.
		$("#registrationButton").on("click", function(){
			// В span для описания ошибок выводим пустую строку, чтобы не возникало путаницы.
			$("#resultSpan").text("");
			$(".formTitle h2").text("Регистрация");
			// Удаляем класс hidden у формы регистрации, и добавляем его к форме входа
			// Данная процедура позволяет обрабатывать регистрацию и ввод на одной странице.
		    $("#signUpForm").addClass('hidden');
            $('#registrationForm').removeClass('hidden');
		});
		// Событие, вызываемое нажатием клавиши, подтверждающей регистрацию.
		$("#submitRegButton").on("click", function(){
			// Проверяем введенную пользователем информацию.
			var checkResult = checkData();
			// Если пришла пустая строка, то все введено верно, и мы отправляем данные.
			if (!checkResult){
				$.ajax({
		    	method: "POST",
		    	url: "ajax.php",
				// Описание использования параметра data в файле ajax.php.
		    	data: {type: "registration", login: $("#newLoginField").val(), password: $("#newPasswordField").val(), email: $("#newEmail").val(), city: $("#newCity").val(), sex: $('#registrationForm input[name=sexRadio]:checked').val()},
		    	success: function(returnVal){
					// Удаляем любой текст из элемента с информацией по ошибкам.
					$("#resultSpan").text("");
					switch(returnVal){
						// Если вернулся SUCCESS, то регистрация пройдена, возвращаемся к форме входа.
					    case "SUCCESS":
						    $(".formTitle  h2").text("Вход");
							$("#registrationForm").addClass('hidden');
							$("#signUpForm").removeClass('hidden');
							$("#resultSpan").text("Поздравляем с регистрацией! Теперь вы можете войти.")
					    break;
						// Если вернулся LOGIN_TAKEN, то регистрация невозможна, потому что такой пользователь уже есть.
						case "LOGIN_TAKEN":
						    $("#resultSpan").text("Пользователь с предоставленным вами логином уже существует.");
					    break;
						// Если вернулся DUPLICATE_IP, то регистрация невозможна, потому что пользователи с одинаковым IP за указанный период времени запрещены.
					    case "DUPLICATE_IP":
						    $("#resultSpan").text("Вы пытаетесь произвести регистрацию с IP, зарегистрированного в системе. Множественные аккаунты недопустимы.");
					    break;
						// Если вернулся MYSQL_FAILURE, то регистрация невозможна, потому что возникли проблемы с базой данных.
						case "MYSQL_FAILURE":
						    $("#resultSpan").text("Возникла ошибка при добавлении новой записи в базу данных. Повторите запрос позднее.");
					    break;
					}
				}
		    });
			// Если из функции checkData вернулась строка с ошибкой, то выводим ошибку на экран.
			} else {
				$("#resultSpan").text(checkResult);
			}
		});
		// Событие, обеспечивающее выход из сессии.
		$(".exitLink").click(function(){
		   $.ajax({
		    	method: "POST",
				// Описание использования параметра data в файле ajax.php.
		    	url: "ajax.php",
		    	data: {type: "exit"},
				// При получении SUCCESS перезагружаем страницу.
		    	success: function(returnVal){
				    if (returnVal == "SUCCESS"){
						location.reload();
					}
				}
		    })
	    });
		// Если есть необходимость вернутся к форме входа, то нажатие этой кнопки
		// скроет форму регистрации и покажет форму входа.
		$("#backToLogin").on("click", function(){
		    $(".formTitle  h2").text("Вход");
			$("#registrationForm").addClass('hidden');
			$("#signUpForm").removeClass('hidden');
			$("#resultSpan").text("")
	    });
	});
    
</script>
<html>
    <head>
		<link rel="stylesheet" media="screen" type="text/css" href="style.css">
	    <title>Простая регистрация</title>
	</head>
	<body>
	    <!-- Span для идентификации формы -->
		<span class="formTitle">
		    <h2>Вход</h2>
		</span>
		<!-- Форма входа на сайт -->
        <div id="signUpForm" class="logForm">
		<ul>
		    <li>
			    <!-- Если пользователь уже вошел, то приветствуем его на сайте. -->
			    <?php if (isset($_SESSION['login']) && isset($_SESSION['id'])) echo "<label for=\"greet\">С возвращением, ".$_SESSION['login']."!</label><a class = \"exitLink\" href=\"#\">Выйти?</a>";?>
			</li>
			<li>
			    <!-- Если пользователь уже вошел, то приветствуем его на сайте. -->
				<label for="login">Логин: </label>
		        <input id="loginField" name="login" type = "text" size="20" maxlength="20">
			</li>
			<li>
				<label for="login">Пароль: </label>
				<input id="passwordField" name="password" type = "password" size="20" maxlength="20">
            </li>
			<li>			
			    <input id="rememberMe" name="remember" type = "checkbox" value = "1" checked>Запомнить меня
			</li>
            <li>			
			    <input id="submitButton" name="submit" type = "button" value = "Войти">
				<input id="registrationButton" name="registration" type = "button" value = "Зарегистрироваться">
			</li>
		</ul>
		</div>
		<!-- Форма регистрации на сайте -->
		<!-- Поля c параметром required обязательны для заполнения -->
		<div id="registrationForm" class="hidden regForm">
		<ul>
		    <li>
		        <span class="requiredNote">Поля помеченные '*' обязательны для заполнения.</span>
		    </li>
			<li>
			    <!-- Поля c параметром pattern сравнивают введенные данные на соответствие RegExp шаблону -->
				<!-- данный механизм позволяет управлять подсказкой -->
			    <label for="login">Введите логин: </label>
			    <input id="newLoginField" name="newLogin" type = "text" size="20" maxlength="20" placeholder="JaneDoe" pattern = "\w{3,20}" required />
				<span class="formHint">Логин должен содержать от 3 до 20 символов</span>
			</li>
			<li>
			    <label for="password">Введите пароль: </label>
			    <input id="newPasswordField" name="newPassword" type = "password" size="20" maxlength="20" pattern = "\w{6,20}" required />
				<span class="formHint">Пароль должен содержать от 6 до 20 символов</span>
			</li>
			<li>
			    <label for="duppassword">Подтвердите пароль: </label>
			    <input id="newPasswordFieldDup" name="newPasswordDup" type = "password" size="20" maxlength="20" required />
			</li>
			<li>
			    <label for="email">Введите eMail: </label>
			    <input id="newEmail" name="nEmail" type = "text" size="40" maxlength="40" placeholder="janedoe@example.com" pattern = "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required />
			    <span class="formHint">Правильный формат: correct@email.com</span>
			</li>
			<li>
			    <label for="sex">Выберите пол: </label>
			    <input id="newSexM" type="radio" name="sexRadio" value="Male">Мужской
			    <input id="newSexF" type="radio" name="sexRadio" value="Female" >Женский<img src="images/required.png">
			</li>
			<li>
			    <label for="city">Введите город: </label>
			    <input id="newCity" name="nCity" type = "text" size="40" maxlength="40" placeholder="Санкт-Петербург" required />
			</li>
			<li>
			    <input id="submitRegButton" name="submit" type = "button" value = "Зарегистрироваться">
				<input id="backToLogin" name="backToL" type = "button" value = "Назад">
		    </li>
		</div>
		<!-- Поля в которое выводится информация по ошибкам -->
		<span id="resultSpan"></span>
		
</html>
