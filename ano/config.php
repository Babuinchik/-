<?php
session_start();

// Настройки базы данных
$host = 'localhost';
$dbname = 'nomad_restaurant';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Функции аутентификации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

function registerUser($name, $email, $password, $phone = '') {
    global $pdo;
    
    error_log("Начало регистрации пользователя: $email");
    
    // Проверяем, существует ли пользователь с таким email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        error_log("Пользователь с email $email уже существует");
        return false;
    }
    
    // Хэшируем пароль
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if (!$hashedPassword) {
        error_log("Ошибка хэширования пароля");
        return false;
    }
    
    // Подготавливаем телефон
    $clean_phone = preg_replace('/[^\d+]/', '', $phone);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$name, $email, $hashedPassword, $clean_phone]);
        
        if ($result) {
            $lastId = $pdo->lastInsertId();
            error_log("Пользователь успешно зарегистрирован. ID: $lastId, Email: $email");
            return true;
        } else {
            error_log("Ошибка выполнения INSERT запроса");
            $errorInfo = $stmt->errorInfo();
            error_log("Детали ошибки: " . print_r($errorInfo, true));
            return false;
        }
    } catch (PDOException $e) {
        error_log("PDO Exception при регистрации: " . $e->getMessage());
        error_log("Код ошибки: " . $e->getCode());
        return false;
    }
}
// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}


/**
 * Логирование действий (для админ-логов)
 */
function logAdminAction($action, $details = '') {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return; // Не логируем для неавторизованных пользователей
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (user_id, action, details, ip_address) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $details,
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (PDOException $e) {
        error_log("Logging error: " . $e->getMessage());
    }
}

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
// Добавляем эти функции в config.php после существующих функций

function getAvailableTables($date, $time, $guests) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT t.* 
        FROM tables t 
        WHERE t.capacity >= ? 
        AND t.status = 'available'
        AND t.id NOT IN (
            SELECT table_id 
            FROM bookings 
            WHERE booking_date = ? 
            AND booking_time = ? 
            AND status IN ('pending', 'confirmed')
        )
        ORDER BY t.capacity
    ");
    
    $stmt->execute([$guests, $date, $time]);
    return $stmt->fetchAll();
}

function createBooking($user_id, $table_id, $date, $time, $guests, $special_requests = '') {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO bookings (user_id, table_id, booking_date, booking_time, guests, special_requests) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([$user_id, $table_id, $date, $time, $guests, $special_requests]);
}

function getUserBookings($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT b.*, t.table_number, t.capacity 
        FROM bookings b 
        JOIN tables t ON b.table_id = t.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
    
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function updateBookingStatus($booking_id, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $booking_id]);
}

// Добавляем в config.php после существующих функций

/**
 * Получение меню с категориями
 */
function getMenuWithCategories() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT 
            d.id,
            d.name,
            d.description,
            d.price,
            d.category_id,
            d.image_url,
            d.available,
            d.cooking_time,
            c.name as category_name,
            c.description as category_description
        FROM dishes d 
        LEFT JOIN categories c ON d.category_id = c.id 
        WHERE d.available = TRUE 
        ORDER BY c.name, d.name
    ");
    return $stmt->fetchAll();
}

/**
 * Получение всех категорий
 */
function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Получение блюд по категории
 */
function getDishesByCategory($category_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM dishes 
        WHERE category_id = ? AND available = TRUE 
        ORDER BY name
    ");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

/**
 * Поиск блюд по названию
 */
function searchDishes($query) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT d.*, c.name as category_name 
        FROM dishes d 
        LEFT JOIN categories c ON d.category_id = c.id 
        WHERE d.available = TRUE 
        AND (d.name LIKE ? OR d.description LIKE ?)
        ORDER BY c.name, d.name
    ");
    $search_term = "%$query%";
    $stmt->execute([$search_term, $search_term]);
    return $stmt->fetchAll();
}



/**
 * Получение статистики для админ-панели
 */
function getStatistics() {
    global $pdo;
    
    return [
        'users_count' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'tables_count' => $pdo->query("SELECT COUNT(*) FROM tables")->fetchColumn(),
        'dishes_count' => $pdo->query("SELECT COUNT(*) FROM dishes")->fetchColumn(),
        'bookings_today' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE booking_date = CURDATE()")->fetchColumn(),
        'active_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE status IN ('pending', 'confirmed')")->fetchColumn()
    ];
}

/**
 * Получение всех пользователей
 */
function getAllUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Получение всех столов
 */
function getAllTables() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM tables ORDER BY table_number");
    return $stmt->fetchAll();
}

/**
 * Получение всех бронирований
 */
function getAllBookings() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT b.*, u.name as user_name, u.email as user_email, u.phone as user_phone, 
               t.table_number, t.capacity 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN tables t ON b.table_id = t.id 
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
    return $stmt->fetchAll();
}


?>