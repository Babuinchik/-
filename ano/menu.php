<?php 
require_once 'config.php';

// Получаем категории и блюда из базы данных
$categories = getCategories();
$menu_items = getMenuWithCategories();

$page_title = "Меню - Ресторан Кочевник";
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
                    <a href="menu.php" class="active">Меню</a>
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
        <section class="menu-section">
            <div class="menu-header">
                <h2>Наше меню</h2>
                <p class="menu-subtitle">Традиционные блюда кочевых народов с современной подачей</p>
            </div>
            
            <?php if (empty($categories)): ?>
                <div class="no-menu">
                    <h3>Меню временно недоступно</h3>
                    <p>Приносим извинения, в данный момент мы обновляем наше меню.</p>
                </div>
            <?php else: ?>
                <div class="menu-categories">
                    <!-- Навигация по категориям -->
                    <div class="category-nav">
                        <?php foreach ($categories as $category): ?>
                            <?php 
                            $category_dishes = array_filter($menu_items, function($dish) use ($category) {
                                return $dish['category_id'] == $category['id'] && $dish['available'] == 1;
                            });
                            
                            if (!empty($category_dishes)): ?>
                                <a href="#category-<?php echo $category['id']; ?>" class="category-nav-link">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Список категорий с блюдами -->
                    <?php foreach ($categories as $category): ?>
                        <?php 
                        // Фильтруем блюда по категории и доступности
                        $category_dishes = array_filter($menu_items, function($dish) use ($category) {
                            return $dish['category_id'] == $category['id'] && $dish['available'] == 1;
                        });
                        
                        if (!empty($category_dishes)): ?>
                            <div class="category" id="category-<?php echo $category['id']; ?>">
                                <div class="category-header">
                                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                    <?php if (!empty($category['description'])): ?>
                                        <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dishes-grid">
                                    <?php foreach ($category_dishes as $dish): ?>
                                        <div class="dish-card">
                                            <div class="dish-image">
                                                <?php if (!empty($dish['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($dish['image_url']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>">
                                                <?php else: ?>
                                                    <div class="dish-image-placeholder">
                                                        🍽️
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="dish-info">
                                                <div class="dish-header">
                                                    <h4><?php echo htmlspecialchars($dish['name']); ?></h4>
                                                    <span class="dish-price"><?php echo number_format($dish['price'], 0, ',', ' '); ?> ₽</span>
                                                </div>
                                                
                                                <p class="dish-description"><?php echo htmlspecialchars($dish['description']); ?></p>
                                                
                                                <div class="dish-details">
                                                    <?php if ($dish['cooking_time'] && $dish['cooking_time'] > 0): ?>
                                                        <span class="cooking-time">⏱️ <?php echo $dish['cooking_time']; ?> мин</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
    // Плавная прокрутка к категориям
    document.addEventListener('DOMContentLoaded', function() {
        const categoryLinks = document.querySelectorAll('.category-nav-link');
        
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    </script>
</body>
</html>