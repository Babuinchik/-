<?php
session_start();
include("config.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM user WHERE Phone_number = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user'] = $user;
        header('Location: account.php');
        exit;
    } else {
        $error = "Неверный номер телефона или пароль";
    }
}
?>

<?php include ("header.php"); ?>

<div class="enterback">
    <div class="wallbooking">
        <div class="">
            <p class="Bookingtext">Вход в аккаунт</p>
            <?php if(isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="number" name="phone" placeholder="Номер телефона" required>
                <br>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" class="bookingbutton">Войти</button>
            </form>
            <br>
            <p class="anoent">Нет аккаунта? <a href="registr.php">Зарегистрируйтесь!</a></p>
        </div>
    </div>
</div>

