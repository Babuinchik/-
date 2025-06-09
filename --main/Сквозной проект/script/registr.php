<?php
// Настройки базы данных
$servername = "localhost"; // Или ваш сервер
$username = "root";
$password = "";
$dbname = "rest";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из формы
$first_name = $_POST['Name'];
$last_name = $_POST['Last_name'];
$password_input = $_POST['Password'];
$phone = $_POST['Phone_number'];

// Хешируем пароль для безопасности
$hashed_password = password_hash($password_input, PASSWORD_DEFAULT);


// Создаем SQL-запрос
$sql = "INSERT INTO users (first_name, last_name, password, phone) VALUES (?, ?, ?, ?)";

// Подготовка и выполнение Prepared Statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $first_name, $last_name, $hashed_password, $phone);

if ($stmt->execute()) {
    echo "Регистрация успешно завершена!";
} else {
    echo "Ошибка: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>