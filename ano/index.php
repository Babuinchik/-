<?php 
require_once 'config.php';
$page_title = "Главная - Ресторан Кочевник";
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
                    <a href="index.php" class="active">Главная</a>
                    <a href="menu.php">Меню</a>
                    <a href="booking.php">Бронирование</a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="profile.php">Личный кабинет</a>
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
        <section class="hero">
            <div class="hero-content">
                <h2>Добро пожаловать в ресторан "Кочевник"</h2>
                <p>Вкус кочевых традиций в сердце города</p>
                <?php if (!isLoggedIn()): ?>
                    <div class="hero-buttons">
                        <a href="register.php" class="btn btn-primary">Зарегистрироваться</a>
                        <a href="login.php" class="btn btn-secondary">Войти</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="features">
            <div class="feature-grid">
                <div class="feature-card">
                    <h3>Аутентичная кухня</h3>
                    <p>Традиционные блюда кочевых народов с современной подачей</p>
                </div>
                <div class="feature-card">
                    <h3>Уютная атмосфера</h3>
                    <p>Интерьер в стиле кочевых жилищ с комфортом</p>
                </div>
                <div class="feature-card">
                    <h3>Бронирование онлайн</h3>
                    <p>Забронируйте стол в несколько кликов</p>
                </div>
            </div>
        </section>

        <section class="about">
            <div class="about-content">
                <h2>О нашем ресторане</h2>
                <p>Ресторан "Кочевник" - это уникальное место, где традиции кочевых народов встречаются с современными гастрономическими тенденциями. Мы предлагаем аутентичные блюда, приготовленные по старинным рецептам, в уютной атмосфере, напоминающей о бескрайних степях и гостеприимстве кочевников.</p>
                
                <div class="about-features">
                    <div class="about-feature">
                        <h4>🍽️ Свежие ингредиенты</h4>
                        <p>Используем только свежие и качественные продукты</p>
                    </div>
                    <div class="about-feature">
                        <h4>👨‍🍳 Опытные повара</h4>
                        <p>Наши шеф-повара знают секреты традиционной кухни</p>
                    </div>
                    <div class="about-feature">
                        <h4>🎉 Специальные мероприятия</h4>
                        <p>Организуем дни рождения, корпоративы и свадьбы</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="cta-content">
                <h2>Готовы попробовать?</h2>
                <p>Забронируйте стол онлайн и гарантируйте себе незабываемый вечер</p>
                <div class="cta-buttons">
                    <a href="booking.php" class="btn btn-primary">Забронировать стол</a>
                    <a href="menu.php" class="btn btn-secondary">Посмотреть меню</a>
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
</body>
</html>