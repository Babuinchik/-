<?php
// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$dishes = $pdo->query("
    SELECT d.*, c.name as category_name 
    FROM dishes d 
    LEFT JOIN categories c ON d.category_id = c.id 
    ORDER BY c.name, d.name
")->fetchAll();

$categories = getCategories();

// Добавление блюда
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dish'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $cooking_time = $_POST['cooking_time'];
    $available = isset($_POST['available']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO dishes (name, description, price, category_id, cooking_time, available) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $category_id, $cooking_time, $available])) {
            echo '<div class="alert alert-success">Блюдо успешно добавлено!</div>';
            $dishes = $pdo->query("SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id = c.id ORDER BY c.name, d.name")->fetchAll();
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-error">Ошибка: ' . $e->getMessage() . '</div>';
    }
}

// Редактирование блюда
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_dish'])) {
    $dish_id = $_POST['dish_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $cooking_time = $_POST['cooking_time'];
    $available = isset($_POST['available']) ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE dishes SET name = ?, description = ?, price = ?, category_id = ?, cooking_time = ?, available = ? WHERE id = ?");
    if ($stmt->execute([$name, $description, $price, $category_id, $cooking_time, $available, $dish_id])) {
        echo '<div class="alert alert-success">Блюдо успешно обновлено!</div>';
        $dishes = $pdo->query("SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id = c.id ORDER BY c.name, d.name")->fetchAll();
    }
}

// Удаление блюда
if (isset($_GET['delete_dish'])) {
    $dish_id = $_GET['delete_dish'];
    
    $stmt = $pdo->prepare("DELETE FROM dishes WHERE id = ?");
    if ($stmt->execute([$dish_id])) {
        echo '<div class="alert alert-success">Блюдо успешно удалено!</div>';
        $dishes = $pdo->query("SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id = c.id ORDER BY c.name, d.name")->fetchAll();
    }
}

$edit_dish = null;
if (isset($_GET['edit_dish'])) {
    $stmt = $pdo->prepare("SELECT * FROM dishes WHERE id = ?");
    $stmt->execute([$_GET['edit_dish']]);
    $edit_dish = $stmt->fetch();
}
?>

<div class="admin-tab-content">
    <h3>Управление меню</h3>
    
    <div class="admin-grid">
        <!-- Форма добавления/редактирования -->
        <div class="admin-form-section">
            <h4><?php echo $edit_dish ? 'Редактировать блюдо' : 'Добавить новое блюдо'; ?></h4>
            
            <form method="POST" class="admin-form">
                <?php if ($edit_dish): ?>
                    <input type="hidden" name="dish_id" value="<?php echo $edit_dish['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Название блюда:</label>
                    <input type="text" name="name" 
                           value="<?php echo $edit_dish ? htmlspecialchars($edit_dish['name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Описание:</label>
                    <textarea name="description" rows="3" required><?php echo $edit_dish ? htmlspecialchars($edit_dish['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Цена (₽):</label>
                    <input type="number" name="price" min="0" step="0.01"
                           value="<?php echo $edit_dish ? $edit_dish['price'] : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Категория:</label>
                    <select name="category_id" required>
                        <option value="">-- Выберите категорию --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($edit_dish && $edit_dish['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Время приготовления (мин):</label>
                    <input type="number" name="cooking_time" min="1" max="180"
                           value="<?php echo $edit_dish ? $edit_dish['cooking_time'] : '30'; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="available" value="1" 
                            <?php echo ($edit_dish && $edit_dish['available']) || !$edit_dish ? 'checked' : ''; ?>>
                        Доступно для заказа
                    </label>
                </div>
                
                <button type="submit" name="<?php echo $edit_dish ? 'edit_dish' : 'add_dish'; ?>" 
                        class="btn btn-primary">
                    <?php echo $edit_dish ? 'Обновить блюдо' : 'Добавить блюдо'; ?>
                </button>
                
                <?php if ($edit_dish): ?>
                    <a href="admin.php#dishes" class="btn btn-secondary">Отмена</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Список блюд -->
        <div class="admin-table-section">
            <h4>Список блюд</h4>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Категория</th>
                            <th>Время</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dishes as $dish): ?>
                            <tr>
                                <td><?php echo $dish['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($dish['name']); ?></strong>
                                    <br><small><?php echo htmlspecialchars($dish['description']); ?></small>
                                </td>
                                <td><?php echo number_format($dish['price'], 0, ',', ' '); ?> ₽</td>
                                <td><?php echo htmlspecialchars($dish['category_name'] ?? 'Без категории'); ?></td>
                                <td><?php echo $dish['cooking_time']; ?> мин</td>
                                <td>
                                    <span class="status-badge status-<?php echo $dish['available'] ? 'available' : 'unavailable'; ?>">
                                        <?php echo $dish['available'] ? 'Доступно' : 'Недоступно'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit_dish=<?php echo $dish['id']; ?>#dishes" 
                                           class="btn btn-primary btn-sm">Редактировать</a>
                                        <a href="?delete_dish=<?php echo $dish['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Удалить блюдо?')">Удалить</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>