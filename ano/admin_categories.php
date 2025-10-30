<?php
// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$categories = getCategories();

// Добавление категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        if ($stmt->execute([$name, $description])) {
            echo '<div class="alert alert-success">Категория успешно добавлена!</div>';
            $categories = getCategories();
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-error">Ошибка: ' . $e->getMessage() . '</div>';
    }
}

// Редактирование категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    if ($stmt->execute([$name, $description, $category_id])) {
        echo '<div class="alert alert-success">Категория успешно обновлена!</div>';
        $categories = getCategories();
    }
}

// Удаление категории
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];
    
    // Проверяем, есть ли блюда в этой категории
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM dishes WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $dishes_count = $stmt->fetchColumn();
    
    if ($dishes_count > 0) {
        echo '<div class="alert alert-error">Нельзя удалить категорию, в которой есть блюда!</div>';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            echo '<div class="alert alert-success">Категория успешно удалена!</div>';
            $categories = getCategories();
        }
    }
}

$edit_category = null;
if (isset($_GET['edit_category'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit_category']]);
    $edit_category = $stmt->fetch();
}
?>

<div class="admin-tab-content">
    <h3>Управление категориями</h3>
    
    <div class="admin-grid">
        <!-- Форма добавления/редактирования -->
        <div class="admin-form-section">
            <h4><?php echo $edit_category ? 'Редактировать категорию' : 'Добавить новую категорию'; ?></h4>
            
            <form method="POST" class="admin-form">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Название категории:</label>
                    <input type="text" name="name" 
                           value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Описание:</label>
                    <textarea name="description" rows="3"><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                </div>
                
                <button type="submit" name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" 
                        class="btn btn-primary">
                    <?php echo $edit_category ? 'Обновить категорию' : 'Добавить категорию'; ?>
                </button>
                
                <?php if ($edit_category): ?>
                    <a href="admin.php#categories" class="btn btn-secondary">Отмена</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Список категорий -->
        <div class="admin-table-section">
            <h4>Список категорий</h4>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? '—'); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($category['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit_category=<?php echo $category['id']; ?>#categories" 
                                           class="btn btn-primary btn-sm">Редактировать</a>
                                        <a href="?delete_category=<?php echo $category['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Удалить категорию?')">Удалить</a>
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