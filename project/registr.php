<?php
session_start();
include("config.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if ($password !== $confirm_password) {
        $error = "Пароли не совпадают";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO user (Name, Last_name, Password, Phone_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $last_name, $hashed_password, $phone]);
            
            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'Name' => $name,
                'Last_name' => $last_name,
                'Phone_number' => $phone
            ];
            
            header('Location: account.php');
            exit;
        } catch (PDOException $e) {
            $error = "Ошибка регистрации: " . $e->getMessage();
        }
    }
}
?>

<?php include("header.php"); ?>

<div class="enterback">
    <div class="wallbooking">
        <div class="">
            <p class="Bookingtextreg">Регистрация</p>
            <?php if(isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="E-mail" required>
                <br>
                <input type="number" name="phone" placeholder="Номер телефона" required>
                <br>
                <input type="text" name="name" placeholder="Имя" required>
                <br>
                <input type="text" name="last_name" placeholder="Фамилия" required>
                <br>
                <input type="password" name="password" placeholder="Пароль" required>
                <br>
                <input type="password" name="confirm_password" placeholder="Подтверждение пароля" required>
                <button type="submit" class="bookingbutton">Регистрация</button>
            </form>
            <br>
            <p class="anoreg">Есть аккаунт? <a href="enter.php">Войдите!</a></p>
        </div>
    </div>
</div>

