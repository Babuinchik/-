<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Получаем данные текущего пользователя
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Обновление данных пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Валидация основных данных
    if (empty($name)) {
        $errors[] = "Имя обязательно для заполнения";
    }
    
    if (empty($email)) {
        $errors[] = "Email обязателен для заполнения";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат email";
    }
    
    // Проверяем, не занят ли email другим пользователем
    if ($email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Этот email уже используется другим пользователем";
        }
    }
    
    // Валидация пароля
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Для смены пароля введите текущий пароль";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Текущий пароль указан неверно";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "Новый пароль должен содержать минимум 6 символов";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Новый пароль и подтверждение не совпадают";
        }
    }
    
    if (empty($errors)) {
        try {
            // Подготавливаем данные для обновления
            $update_data = [
                'name' => $name,
                'email' => $email,
                'phone' => preg_replace('/[^\d+]/', '', $phone)
            ];
            
            // Если меняется пароль
            if (!empty($new_password)) {
                $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            // Формируем SQL запрос
            $sql = "UPDATE users SET name = :name, email = :email, phone = :phone";
            if (isset($update_data['password'])) {
                $sql .= ", password = :password";
            }
            $sql .= " WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $update_data['id'] = $user_id;
            
            if ($stmt->execute($update_data)) {
                // Обновляем данные в сессии
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                
                $success = "Данные успешно обновлены!";
                
                // Обновляем данные пользователя
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $error = "Ошибка при обновлении данных";
            }
        } catch (PDOException $e) {
            $error = "Ошибка базы данных: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}



// Получаем последние бронирования пользователя
$user_bookings = getUserBookings($user_id, );

$page_title = "Личный кабинет - Ресторан Кочевник";
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
                    <a href="booking.php">Бронирование</a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="profile.php" class="active">Личный кабинет</a>
                        <?php if (isAdmin()): ?>
                            <a href="admin.php">Админ-панель</a>
                        <?php endif; ?>
                        <a href="?logout=true" class="btn-logout">Выход</a>
                        <span class="user-greeting">Привет, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">Вход</a>
                        <a href="register.php" class="btn-register">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <section class="profile-section">
            <div class="profile-header">
                <h2>Личный кабинет</h2>
                <p>Управление вашими данными и бронированиями</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="profile-content">

                <div class="profile-main">
                    <!-- Форма редактирования профиля -->
                    <div class="profile-form-section">
                        <h3>Редактирование профиля</h3>
                        
                        <form method="POST" class="profile-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Имя и фамилия *</label>
                                    <input type="text" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" 
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="+7 (900) 123-45-67">
                            </div>
                            
                            <div class="form-section">
                                <h4>Смена пароля</h4>
                                <p class="form-hint">Оставьте эти поля пустыми, если не хотите менять пароль</p>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="current_password">Текущий пароль</label>
                                        <input type="password" id="current_password" name="current_password">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="new_password">Новый пароль</label>
                                        <input type="password" id="new_password" name="new_password" 
                                               minlength="6">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="confirm_password">Подтверждение пароля</label>
                                        <input type="password" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    Сохранить изменения
                                </button>
                                <a href="booking.php" class="btn btn-secondary">Забронировать стол</a>
                            </div>
                        </form>
                    </div>

                    <!-- Последние бронирования -->
                    <div class="profile-bookings">
                        <div class="bookings-header">
                            <h3>Последние бронирования</h3>
                            <a href="booking.php" class="btn btn-sm btn-outline">Все бронирования</a>
                        </div>
                        
                        <?php if (empty($user_bookings)): ?>
                            <div class="no-bookings">
                                <p>У вас пока нет бронирований</p>
                                <a href="booking.php" class="btn btn-primary">Забронировать стол</a>
                            </div>
                        <?php else: ?>
                            <div class="bookings-list">
                                <?php foreach ($user_bookings as $booking): ?>
                                    <div class="booking-item <?php echo $booking['status']; ?>">
                                        <div class="booking-info">
                                            <div class="booking-main">
                                                <h4>Стол <?php echo htmlspecialchars($booking['table_number']); ?></h4>
                                                <span class="booking-date">
                                                    <?php echo date('d.m.Y', strtotime($booking['booking_date'])); ?> в <?php echo $booking['booking_time']; ?>
                                                </span>
                                            </div>
                                            <div class="booking-details">
                                                <span class="guests">👥 <?php echo $booking['guests']; ?> чел.</span>
                                                <span class="status-badge status-<?php echo $booking['status']; ?>">
                                                    <?php 
                                                    $statuses = [
                                                        'pending' => 'Ожидание',
                                                        'confirmed' => 'Подтверждено',
                                                        'cancelled' => 'Отменено',
                                                        'completed' => 'Завершено'
                                                    ];
                                                    echo $statuses[$booking['status']];
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="booking-actions">
                                            <?php if ($booking['status'] == 'pending'): ?>
                                                <a href="booking.php?cancel_booking=<?php echo $booking['id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Отменить бронирование?')">
                                                    Отменить
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
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
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Пароли не совпадают');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword?.addEventListener('change', validatePassword);
    confirmPassword?.addEventListener('keyup', validatePassword);
    </script>
</body>
</html>