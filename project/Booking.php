<?php
session_start();
include("config.php");
include("function.php");

if (!isset($_SESSION['user'])) {
    header('Location: enter.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $_SESSION['user']['id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO `booking table` (Status, `Booking by who`, Time, Date) VALUES (?, ?, ?, ?)");
        $stmt->execute(['active', $user_id, $time, $date]);
        
        header('Location: suc booking.php');
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка бронирования: " . $e->getMessage();
    }
}
?>

<?php include ("header.php"); ?>

<div class="content">
    <img class="mainimg" src="img/booking.png">
    <div class="imgslider">
        <div class="wallbooking">
            <div class="">
                <p class="Bookingtext">Бронирование стола</p>
                <?php if(isset($error)): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="text" name="name" placeholder="Имя" value="<?= $_SESSION['user']['Name'] ?? '' ?>" required>
                    <br>
                    <input type="text" name="last_name" placeholder="Фамилия" value="<?= $_SESSION['user']['Last_name'] ?? '' ?>" required>
                    <br>
                    <input type="datetime-local" name="datetime" required>
                    <button type="submit" class="bookingbutton">Забронировать</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include ("footer.php"); ?>