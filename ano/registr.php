<?php 
require_once 'config.php';

$errors = [];
$success = '';

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $phone = trim($_POST['phone']);
    
    // Валидация данных
    if (empty($name)) {
        $errors[] = "Имя обязательно для заполнения";
    } elseif (strlen($name) < 2) {
        $errors[] = "Имя должно содержать минимум 2 символа";
    }
    
    if (empty($email)) {
        $errors[] = "Email обязателен для заполнения";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат email";
    }
    
    if (empty($password)) {
        $errors[] = "Пароль обязателен для заполнения";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать минимум 6 символов";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Если ошибок нет, регистрируем пользователя
    if (empty($errors)) {
        if (registerUser($name, $email, $password, $phone)) {
            $success = "Регистрация прошла успешно! Теперь вы можете войти в систему.";
        } else {
            $errors[] = "Ошибка регистрации. Возможно, email уже используется.";
        }
    }
}

$page_title = "Регистрация - Ресторан Кочевник";
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
                    <a href="login.php" class="btn-login">Вход</a>
                    <a href="register.php" class="btn-register active">Регистрация</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="auth-container">
            <div class="auth-form">
                <h2>Регистрация в ресторане "Кочевник"</h2>
                <p class="auth-subtitle">Создайте аккаунт для бронирования столов</p>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <div class="success-actions">
                            <a href="login.php" class="btn btn-primary">Войти в аккаунт</a>
                            <a href="index.php" class="btn btn-secondary">На главную</a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <h4>Ошибки при регистрации:</h4>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!$success): ?>
                    <form method="POST" class="auth-form-content">
                        <div class="form-group">
                            <label for="name">Имя и фамилия *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                   required
                                   placeholder="Введите ваше имя">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                   required
                                   placeholder="example@mail.ru">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" 
                                   placeholder="+7 (900) 123-45-67">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Пароль *</label>
                                <input type="password" id="password" name="password" 
                                       required
                                       placeholder="Не менее 6 символов"
                                       minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirm">Подтверждение пароля *</label>
                                <input type="password" id="password_confirm" name="password_confirm" 
                                       required
                                       placeholder="Повторите пароль">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            Зарегистрироваться
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
                    </div>
                <?php endif; ?>
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

    <script>
    // Маска для телефона
    document.getElementById('phone')?.addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
        e.target.value = '+7' + (x[2] ? ' (' + x[2] : '') + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });

    // Валидация паролей
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    function validatePassword() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Пароли не совпадают');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }
    
    password?.addEventListener('change', validatePassword);
    passwordConfirm?.addEventListener('keyup', validatePassword);
    </script>
</body>
</html>