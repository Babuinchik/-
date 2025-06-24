<?php
// Настройки базы данных
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "rest";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из формы
$phone = $_POST['Phone_number'];
$password_input = $_POST['Password'];

// Поиск пользователя по номеру телефона
$stmt = $conn->prepare("SELECT password FROM users WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    // Проверка правильности пароля
    if (password_verify($password_input, $row['password'])) {
        echo "Успешный вход!";
        // Тут можно установить сессию или куки
    } else {
        echo "Неверный пароль.";
    }
} else {
    echo "Пользователь с таким номером не найден.";
}

$stmt->close();
$conn->close();
?>