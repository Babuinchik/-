<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (loginUser($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}

$page_title = "Вход - Ресторан Кочевник";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h1><a href="index.php">Кочевник</a></h1>
                </div>
                <div class="nav-menu">
                    <a href="index.php">Главная</a>
                    <a href="menu.php">Меню</a>
                    <a href="login.php" class="btn-login active">Вход</a>
                    <a href="register.php" class="btn-register">Регистрация</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="auth-container">
            <div class="auth-form">
                <h2>Вход в аккаунт</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Войти</button>
                </form>
                
                <div class="auth-footer">
                    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Ресторан "Кочевник"</h3>
                    <p>Вкус кочевых традиций в современной интерпретации</p>
                </div>
                <div class="footer-section">
                    <h4>Контакты</h4>
                    <p>📞 +7 (777) 123-45-67</p>
                    <p>📧 info@nomad.com</p>
                    <p>📍 ул. Степная, 15</p>
                </div>
                <div class="footer-section">
                    <h4>Время работы</h4>
                    <p>Пн-Чт: 12:00 - 23:00</p>
                    <p>Пт-Вс: 12:00 - 00:00</p>
                </div>
            </div>
            <p class="footer-bottom"></p>
        </div>
    </footer>
</body>
</html>