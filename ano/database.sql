-- Создание базы данных
CREATE DATABASE IF NOT EXISTS nomad_restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nomad_restaurant;

-- Таблица пользователей
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица столов
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    status ENUM('available', 'reserved', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица категорий блюд
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица блюд
CREATE TABLE dishes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image_url VARCHAR(255),
    available BOOLEAN DEFAULT TRUE,
    cooking_time INT DEFAULT 30, -- время приготовления в минутах
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Таблица бронирований
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    table_id INT,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    guests INT NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status)
);

-- Таблица логов администратора
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_created_at (created_at)
);

-- Таблица отзывов
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    booking_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- Вставка начальных данных

-- Добавляем администратора (пароль: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Администратор', 'admin@nomad.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Иван Петров', 'user@nomad.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Добавляем категории блюд
INSERT INTO categories (name, description) VALUES 
('Основные блюда', 'Сытные и традиционные блюда кочевых народов'),
('Закуски', 'Легкие закуски к основным блюдам'),
('Супы', 'Ароматные и наваристые супы'),
('Десерты', 'Сладкие завершения трапезы'),
('Напитки', 'Традиционные и современные напитки'),
('Гарниры', 'Дополнения к основным блюдам');

-- Добавляем столы
INSERT INTO tables (table_number, capacity, description) VALUES 
('T1', 2, 'Уютный столик у окна с видом на город'),
('T2', 4, 'Стандартный стол в центре зала'),
('T3', 6, 'Большой семейный стол'),
('T4', 2, 'Романтический столик в уединенном уголке'),
('T5', 8, 'Праздничный стол для больших компаний'),
('T6', 4, 'Стол в восточном стиле с мягкими подушками'),
('V1', 10, 'VIP-зал с отдельной комнатой');

-- Добавляем блюда
INSERT INTO dishes (name, description, price, category_id, cooking_time) VALUES 
-- Основные блюда
('Бешбармак', 'Традиционное блюдо из отварного мяса с лапшой и луковым соусом', 1200.00, 1, 45),
('Куырдак', 'Жаркое из бараньих субпродуктов с картофелем и луком', 950.00, 1, 35),
('Плов по-кочевничьи', 'Ароматный плов с бараниной, морковью и специями', 1100.00, 1, 50),
('Шашлык из баранины', 'Нежное мясо молодого барашка, маринованное в специях', 1400.00, 1, 30),
('Казы', 'Домашняя конская колбаса с традиционными специями', 1600.00, 1, 25),

-- Закуски
('Сало по-кочевничьи', 'Сало с чесноком и перцем, подается с ржаным хлебом', 450.00, 2, 15),
('Чак-чак', 'Восточная сладость из теста с медом', 350.00, 2, 20),
('Лепешки тандырные', 'Свежие лепешки из тандыра с травами', 250.00, 2, 10),
('Ассорти мясное', 'Набор из казы, шужука и карта', 1200.00, 2, 15),

-- Супы
('Шурпа', 'Наваристый суп из баранины с овощами', 650.00, 3, 60),
('Бограч', 'Венгерский суп с паприкой и мясом', 580.00, 3, 45),
('Лагман', 'Густой суп с лапшой и овощами', 720.00, 3, 40),

-- Десерты
('Баурсаки', 'Традиционные пончики, подаются с медом', 350.00, 4, 20),
('Хворост', 'Хрустящее печенье в сахарной пудре', 280.00, 4, 25),
('Чак-чак с орехами', 'Чак-чак с грецкими орехами и изюмом', 420.00, 4, 30),

-- Напитки
('Кумыс', 'Традиционный напиток из кобыльего молока', 450.00, 5, 5),
('Айран', 'Освежающий кисломолочный напиток', 220.00, 5, 5),
('Шалфейный чай', 'Ароматный чай с шалфеем и медом', 180.00, 5, 10),
('Компот из сухофруктов', 'Натуральный компот из сушеных фруктов', 150.00, 5, 15),

-- Гарниры
('Рис с овощами', 'Рис, обжаренный с морковью и луком', 320.00, 6, 20),
('Картофель по-деревенски', 'Запеченный картофель с травами', 280.00, 6, 25),
('Овощи гриль', 'Сезонные овощи, приготовленные на гриле', 380.00, 6, 15);

-- Добавляем тестовые бронирования
INSERT INTO bookings (user_id, table_id, booking_date, booking_time, guests, status) VALUES 
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00:00', 2, 'confirmed'),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '20:00:00', 5, 'pending');

-- Добавляем тестовые отзывы
INSERT INTO reviews (user_id, booking_id, rating, comment, status) VALUES 
(2, 1, 5, 'Отличный ресторан! Бешбармак просто восхитительный!', 'approved');

-- Создаем индексы для улучшения производительности
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_dishes_category ON dishes(category_id);
CREATE INDEX idx_dishes_available ON dishes(available);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_table ON bookings(table_id);
CREATE INDEX idx_bookings_datetime ON bookings(booking_date, booking_time);

-- Создаем представления для удобства

-- Представление для меню с категориями
CREATE VIEW menu_view AS
SELECT 
    d.id,
    d.name,
    d.description,
    d.price,
    d.available,
    d.cooking_time,
    c.name as category_name,
    c.description as category_description
FROM dishes d
LEFT JOIN categories c ON d.category_id = c.id
WHERE d.available = TRUE
ORDER BY c.name, d.name;

-- Представление для бронирований с информацией о пользователях и столах
CREATE VIEW booking_details AS
SELECT 
    b.id,
    b.booking_date,
    b.booking_time,
    b.guests,
    b.status,
    b.special_requests,
    b.created_at,
    u.name as user_name,
    u.email as user_email,
    u.phone as user_phone,
    t.table_number,
    t.capacity,
    t.description as table_description
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN tables t ON b.table_id = t.id;

-- Создаем пользователя для приложения (опциональ