<?php 
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_table'])) {
    $table_id = $_POST['table_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $guests = $_POST['guests'];
    $special_requests = $_POST['special_requests'] ?? '';
    
    // Валидация данных
    if (empty($table_id) || empty($booking_date) || empty($booking_time) || empty($guests)) {
        $error = "Пожалуйста, заполните все обязательные поля";
    } elseif ($guests < 1 || $guests > 20) {
        $error = "Количество гостей должно быть от 1 до 20";
    } else {
        // Проверяем доступность стола
        $available_tables = getAvailableTables($booking_date, $booking_time, $guests);
        $table_available = false;
        
        foreach ($available_tables as $table) {
            if ($table['id'] == $table_id) {
                $table_available = true;
                break;
            }
        }
        
        if ($table_available) {
            if (createBooking($_SESSION['user_id'], $table_id, $booking_date, $booking_time, $guests, $special_requests)) {
                $success = "Стол успешно забронирован! Мы ждем вас " . date('d.m.Y', strtotime($booking_date)) . " в " . $booking_time;
            } else {
                $error = "Ошибка при бронировании стола. Пожалуйста, попробуйте еще раз.";
            }
        } else {
            $error = "Выбранный стол больше не доступен на указанное время. Пожалуйста, выберите другой стол или время.";
        }
    }
}

// Отмена бронирования
if (isset($_GET['cancel_booking'])) {
    $booking_id = $_GET['cancel_booking'];
    
    $stmt = $pdo->prepare("SELECT user_id, booking_date, booking_time FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    if ($booking && $booking['user_id'] == $_SESSION['user_id']) {
        // Проверяем, можно ли отменить бронирование (не позже чем за 2 часа)
        $booking_datetime = $booking['booking_date'] . ' ' . $booking['booking_time'];
        $current_datetime = date('Y-m-d H:i:s');
        $time_diff = (strtotime($booking_datetime) - strtotime($current_datetime)) / 3600;
        
        if ($time_diff < 2) {
            $error = "Отменить бронирование можно не позднее чем за 2 часа до назначенного времени.";
        } else {
            if (updateBookingStatus($booking_id, 'cancelled')) {
                $success = "Бронирование успешно отменено";
            } else {
                $error = "Ошибка при отмене бронирования";
            }
        }
    } else {
        $error = "Бронирование не найдено или у вас нет прав для его отмены";
    }
}

// Получаем бронирования пользователя
$user_bookings = getUserBookings($_SESSION['user_id']);

$page_title = "Бронирование - Ресторан Кочевник";
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
                    <a href="booking.php" class="active">Бронирование</a>
                    
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
        <section class="booking-section">
            <div class="booking-header">
                <h2>Бронирование стола</h2>
                <p class="booking-subtitle">Забронируйте стол онлайн и гарантируйте себе комфортный вечер</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <div class="success-actions">
                        <a href="booking.php" class="btn btn-primary">Забронировать еще</a>
                        <a href="profile.php" class="btn btn-secondary">В личный кабинет</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="booking-container">
                <!-- Форма бронирования -->
                <div class="booking-form-container">
                    <h3>🪑 Забронировать стол</h3>
                    <form method="POST" class="booking-form" id="bookingForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="booking_date">📅 Дата бронирования *</label>
                                <input type="date" id="booking_date" name="booking_date" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" 
                                       required>
                                <div class="form-hint">Бронирование доступно на 30 дней вперед</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="booking_time">🕒 Время *</label>
                                <select id="booking_time" name="booking_time" required>
                                    <option value="">-- Выберите время --</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                    <option value="21:00">21:00</option>
                                    <option value="22:00">22:00</option>
                                </select>
                                <div class="form-hint">Время работы: 12:00 - 23:00</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="guests">👥 Количество гостей *</label>
                                <select id="guests" name="guests" required>
                                    <option value="">-- Выберите количество --</option>
                                    <option value="1">1 человек</option>
                                    <option value="2" selected>2 человека</option>
                                    <option value="3">3 человека</option>
                                    <option value="4">4 человека</option>
                                    <option value="5">5 человек</option>
                                    <option value="6">6 человек</option>
                                    <option value="7">7 человек</option>
                                    <option value="8">8 человек</option>
                                    <option value="9">9 человек</option>
                                    <option value="10">10 человек</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="special_requests">💬 Особые пожелания</label>
                            <textarea id="special_requests" name="special_requests" 
                                      rows="4" 
                                      placeholder="Например: стол у окна, детский стул, аллергия на определенные продукты..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" id="checkAvailability" class="btn btn-secondary btn-large">
                                🔍 Проверить доступные столы
                            </button>
                        </div>
                        
                        <div id="tablesContainer" style="display: none;">
                            <div class="form-group">
                                <label class="tables-label">🎯 Выберите стол:</label>
                                <div id="availableTables" class="tables-grid"></div>
                            </div>
                            
                            <button type="submit" name="book_table" class="btn btn-primary btn-large">
                                ✅ Подтвердить бронирование
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Информация о бронировании -->
                <div class="booking-info">
                    <h3>ℹ️ Информация о бронировании</h3>
                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-icon">🕒</div>
                            <div class="info-content">
                                <h4>Время работы</h4>
                                <p>Ежедневно с 12:00 до 23:00</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">👥</div>
                            <div class="info-content">
                                <h4>Вместимость</h4>
                                <p>Столы от 2 до 10 человек</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">⏱️</div>
                            <div class="info-content">
                                <h4>Длительность</h4>
                                <p>Бронирование на 2 часа</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">📞</div>
                            <div class="info-content">
                                <h4>Контакты</h4>
                                <p>+7 (777) 123-45-67</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Список бронирований пользователя -->
            <div class="user-bookings">
                <div class="bookings-header">
                    <h3>📋 Ваши бронирования</h3>
                </div>
                
                <?php if (empty($user_bookings)): ?>
                    <div class="no-bookings">
                        <div class="no-bookings-icon">📅</div>
                        <h4>У вас пока нет бронирований</h4>
                        <p>Забронируйте свой первый стол и насладитесь атмосферой кочевых традиций</p>
                    </div>
                <?php else: ?>
                    <div class="bookings-list">
                        <?php foreach ($user_bookings as $booking): ?>
                            <div class="booking-card <?php echo $booking['status']; ?>">
                                <div class="booking-header">
                                    <div class="booking-title">
                                        <h4>Бронирование #<?php echo $booking['id']; ?></h4>
                                        <span class="booking-datetime">
                                            <?php echo date('d.m.Y', strtotime($booking['booking_date'])); ?> в <?php echo $booking['booking_time']; ?>
                                        </span>
                                    </div>
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php 
                                        $statuses = [
                                            'pending' => '⏳ Ожидание',
                                            'confirmed' => '✅ Подтверждено', 
                                            'cancelled' => '❌ Отменено',
                                            'completed' => '🏁 Завершено'
                                        ];
                                        echo $statuses[$booking['status']];
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="booking-details">
                                    <div class="detail">
                                        <strong>Стол:</strong> <?php echo htmlspecialchars($booking['table_number']); ?> (до <?php echo $booking['capacity']; ?> чел.)
                                    </div>
                                    <div class="detail">
                                        <strong>Гостей:</strong> <?php echo $booking['guests']; ?>
                                    </div>
                                    <?php if (!empty($booking['special_requests'])): ?>
                                        <div class="detail">
                                            <strong>Пожелания:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                    <div class="booking-actions">
                                        <?php 
                                        $booking_datetime = $booking['booking_date'] . ' ' . $booking['booking_time'];
                                        $current_datetime = date('Y-m-d H:i:s');
                                        $time_diff = (strtotime($booking_datetime) - strtotime($current_datetime)) / 3600;
                                        ?>
                                        
                                        <?php if ($time_diff >= 2): ?>
                                            <a href="?cancel_booking=<?php echo $booking['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Вы уверены, что хотите отменить бронирование?')">
                                                ❌ Отменить
                                            </a>
                                        <?php else: ?>
                                            <span class="cancel-disabled">Отмена невозможна (менее 2 часов до визита)</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
        const checkBtn = document.getElementById('checkAvailability');
        const tablesContainer = document.getElementById('tablesContainer');
        const availableTables = document.getElementById('availableTables');
        
        checkBtn.addEventListener('click', function() {
            const date = document.getElementById('booking_date').value;
            const time = document.getElementById('booking_time').value;
            const guests = document.getElementById('guests').value;
            
            if (!date || !time || !guests) {
                alert('Пожалуйста, заполните дату, время и количество гостей');
                return;
            }
            
            // Показываем загрузку
            checkBtn.innerHTML = '⏳ Поиск доступных столов...';
            checkBtn.disabled = true;
            
            // AJAX запрос для получения доступных столов
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_get_tables.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                checkBtn.innerHTML = '🔍 Проверить доступные столы';
                checkBtn.disabled = false;
                
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        availableTables.innerHTML = '';
                        
                        if (response.tables.length > 0) {
                            response.tables.forEach(function(table) {
                                const tableDiv = document.createElement('div');
                                const isOptimal = table.capacity >= guests && table.capacity <= parseInt(guests) + 2;
                                tableDiv.className = `table-option ${isOptimal ? 'optimal' : ''}`;
                                tableDiv.innerHTML = `
                                    <input type="radio" name="table_id" value="${table.id}" id="table_${table.id}" required>
                                    <label for="table_${table.id}">
                                        <div class="table-header">
                                            <strong>Стол ${table.table_number}</strong>
                                            <span class="table-capacity">👥 ${table.capacity} чел.</span>
                                        </div>
                                        <div class="table-description">${table.description}</div>
                                        ${isOptimal ? '<div class="optimal-badge">🎯 Оптимальный выбор</div>' : ''}
                                    </label>
                                `;
                                availableTables.appendChild(tableDiv);
                            });
                            
                            tablesContainer.style.display = 'block';
                            checkBtn.style.display = 'none';
                            
                            // Прокручиваем к выбору столов
                            tablesContainer.scrollIntoView({ behavior: 'smooth' });
                            
                        } else {
                            alert('❌ На выбранные дату и время нет доступных столов. Пожалуйста, выберите другое время или дату.');
                        }
                    } else {
                        alert('❌ Ошибка при проверке доступности столов');
                    }
                } else {
                    alert('❌ Ошибка соединения с сервером');
                }
            };
            
            xhr.onerror = function() {
                checkBtn.innerHTML = '🔍 Проверить доступные столы';
                checkBtn.disabled = false;
                alert('❌ Ошибка сети. Проверьте подключение к интернету.');
            };
            
            xhr.send('date=' + encodeURIComponent(date) + 
                    '&time=' + encodeURIComponent(time) + 
                    '&guests=' + encodeURIComponent(guests));
        });
        
        // Сброс формы при изменении даты/времени
        document.getElementById('booking_date').addEventListener('change', function() {
            resetTablesSelection();
        });
        
        document.getElementById('booking_time').addEventListener('change', function() {
            resetTablesSelection();
        });
        
        document.getElementById('guests').addEventListener('change', function() {
            resetTablesSelection();
        });
        
        function resetTablesSelection() {
            tablesContainer.style.display = 'none';
            checkBtn.style.display = 'block';
            availableTables.innerHTML = '';
        }
        
        // Автовыбор сегодняшней даты
        document.getElementById('booking_date').valueAsDate = new Date();
    });
    </script>
</body>
</html>