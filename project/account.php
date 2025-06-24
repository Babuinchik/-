<?php
include("config.php");
include("function.php");

// Проверка авторизации
session_start();

// Обработка выхода из аккаунта
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: enter.php');
    exit;
}

// Получаем данные пользователя из базы данных
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Получаем бронирования пользователя
$bookings_stmt = $pdo->prepare("SELECT * FROM `booking table` WHERE `Booking by who` = ? ORDER BY Date DESC, Time DESC");
$bookings_stmt->execute([$user_id]);
$bookings = $bookings_stmt->fetchAll();
?>

<?php include("header.php")?>

<div class="content">
    <img class="mainimg" src="Img/Main page.png">
    
    <div class="Data">
        <h2>Данные пользователя</h2>
        <div class="user-info">
            <p><strong>Имя:</strong> <?= htmlspecialchars($user['Name']) ?></p>
            <p><strong>Фамилия:</strong> <?= htmlspecialchars($user['Last_name']) ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($user['Phone_number']) ?></p>
        </div>
        <a href="account.php?logout=1" class="logout-btn">Выйти из аккаунта</a>
    </div>
    
    <div class="Bookingacc">
        <h2>Забронированные столы</h2>
        <?php if (count($bookings) > 0): ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['Date']) ?></td>
                            <td><?= htmlspecialchars(substr($booking['Time'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($booking['Status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>У вас нет активных бронирований.</p>
        <?php endif; ?>
    </div>
</div>

<?php include("footer.php")?>