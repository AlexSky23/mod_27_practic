<?php

define('URL', 'http://files:8080');
define('UPLOAD_MAX_SIZE', 5000000);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/jpg']);
define('UPLOAD_DIR', 'uploads');

// Соединяемся с БД

//Устанавливаем доступы к базе данных:
$host = '127.0.0.1:3305'; //имя хоста, на локальном компьютере это localhost
$user = 'root'; //имя пользователя, по умолчанию это root
$password = 'root'; //пароль, по умолчанию пустой
$db_name = 'autorising1'; //имя базы данных