<?php 
require_once 'config.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$stats = getStatistics();

$page_title = "Админ-панель - Ресторан Кочевник";
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
                    <a href="profile.php">Личный кабинет</a>
                    <a href="admin.php" class="active">Админ-панель</a>
                    <a href="?logout=true" class="btn-logout">Выход</a>
                    <span class="user-greeting">Админ: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <section class="admin-section">
            <h2>Админ-панель ресторана "Кочевник"</h2>
            
            <!-- Статистика -->
            <div class="admin-stats">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['users_count']; ?></span>
                    <span class="stat-label">Пользователей</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['tables_count']; ?></span>
                    <span class="stat-label">Столов</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['dishes_count']; ?></span>
                    <span class="stat-label">Блюд</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['bookings_today']; ?></span>
                    <span class="stat-label">Бронирований сегодня</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['active_bookings']; ?></span>
                    <span class="stat-label">Активных бронирований</span>
                </div>
            </div>

            <!-- Навигация по разделам -->
            <div class="admin-nav">
                <a href="#users" class="admin-nav-btn active" data-tab="users">👥 Пользователи</a>
                <a href="#tables" class="admin-nav-btn" data-tab="tables">🪑 Столы</a>
                <a href="#dishes" class="admin-nav-btn" data-tab="dishes">🍽️ Меню</a>
                <a href="#bookings" class="admin-nav-btn" data-tab="bookings">📅 Бронирования</a>
                <a href="#categories" class="admin-nav-btn" data-tab="categories">📂 Категории</a>
            </div>

            <!-- Содержимое разделов -->
            <div class="admin-content">
                
                <!-- Раздел пользователей -->
                <div id="users-tab" class="admin-tab active">
                    <?php include 'admin_user.php'; ?>
                </div>

                <!-- Раздел столов -->
                <div id="tables-tab" class="admin-tab">
                    <?php include 'admin_tables.php'; ?>
                </div>

                <!-- Раздел меню -->
                <div id="dishes-tab" class="admin-tab">
                    <?php include 'admin_dishes.php'; ?>
                </div>

                <!-- Раздел бронирований -->
                <div id="bookings-tab" class="admin-tab">
                    <?php include 'admin_bookings.php'; ?>
                </div>

                <!-- Раздел категорий -->
                <div id="categories-tab" class="admin-tab">
                    <?php include 'admin_categories.php'; ?>
                </div>

            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p></p>
        </div>
    </footer>

    <script>
    // Переключение между вкладками
    document.addEventListener('DOMContentLoaded', function() {
        const navButtons = document.querySelectorAll('.admin-nav-btn');
        const tabs = document.querySelectorAll('.admin-tab');
        
        navButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Убираем активный класс у всех кнопок и вкладок
                navButtons.forEach(btn => btn.classList.remove('active'));
                tabs.forEach(tab => tab.classList.remove('active'));
                
                // Добавляем активный класс текущей кнопке и вкладке
                this.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
                
                // Обновляем URL
                history.pushState(null, null, '#' + tabId);
            });
        });
        
        // Обработка хэша в URL при загрузке
        const hash = window.location.hash.substring(1);
        if (hash && document.querySelector(`[data-tab="${hash}"]`)) {
            document.querySelector(`[data-tab="${hash}"]`).click();
        }
    });
    </script>
</body>
</html>