<?php
// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$tables = getAllTables();

// Добавление стола
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_table'])) {
    $table_number = $_POST['table_number'];
    $capacity = $_POST['capacity'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO tables (table_number, capacity, description, status) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$table_number, $capacity, $description, $status])) {
            echo '<div class="alert alert-success">Стол успешно добавлен!</div>';
            $tables = getAllTables();
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-error">Ошибка: ' . $e->getMessage() . '</div>';
    }
}

// Редактирование стола
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_table'])) {
    $table_id = $_POST['table_id'];
    $table_number = $_POST['table_number'];
    $capacity = $_POST['capacity'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE tables SET table_number = ?, capacity = ?, description = ?, status = ? WHERE id = ?");
    if ($stmt->execute([$table_number, $capacity, $description, $status, $table_id])) {
        echo '<div class="alert alert-success">Стол успешно обновлен!</div>';
        $tables = getAllTables();
    }
}

// Удаление стола
if (isset($_GET['delete_table'])) {
    $table_id = $_GET['delete_table'];
    
    // Проверяем активные бронирования
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE table_id = ? AND status IN ('pending', 'confirmed')");
    $stmt->execute([$table_id]);
    $active_bookings = $stmt->fetchColumn();
    
    if ($active_bookings > 0) {
        echo '<div class="alert alert-error">Нельзя удалить стол с активными бронированиями!</div>';
    } else {
        $stmt = $pdo->prepare("DELETE FROM tables WHERE id = ?");
        if ($stmt->execute([$table_id])) {
            echo '<div class="alert alert-success">Стол успешно удален!</div>';
            $tables = getAllTables();
        }
    }
}

$edit_table = null;
if (isset($_GET['edit_table'])) {
    $stmt = $pdo->prepare("SELECT * FROM tables WHERE id = ?");
    $stmt->execute([$_GET['edit_table']]);
    $edit_table = $stmt->fetch();
}
?>

<div class="admin-tab-content">
    <h3>Управление столами</h3>
    
    <div class="admin-grid">
        <!-- Форма добавления/редактирования -->
        <div class="admin-form-section">
            <h4><?php echo $edit_table ? 'Редактировать стол' : 'Добавить новый стол'; ?></h4>
            
            <form method="POST" class="admin-form">
                <?php if ($edit_table): ?>
                    <input type="hidden" name="table_id" value="<?php echo $edit_table['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Номер стола:</label>
                    <input type="text" name="table_number" 
                           value="<?php echo $edit_table ? htmlspecialchars($edit_table['table_number']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Вместимость:</label>
                    <input type="number" name="capacity" min="1" max="50"
                           value="<?php echo $edit_table ? $edit_table['capacity'] : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Описание:</label>
                    <textarea name="description" rows="3"><?php echo $edit_table ? htmlspecialchars($edit_table['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Статус:</label>
                    <select name="status" required>
                        <option value="available" <?php echo ($edit_table && $edit_table['status'] == 'available') ? 'selected' : ''; ?>>Доступен</option>
                        <option value="reserved" <?php echo ($edit_table && $edit_table['status'] == 'reserved') ? 'selected' : ''; ?>>Зарезервирован</option>
                        <option value="maintenance" <?php echo ($edit_table && $edit_table['status'] == 'maintenance') ? 'selected' : ''; ?>>На обслуживании</option>
                    </select>
                </div>
                
                <button type="submit" name="<?php echo $edit_table ? 'edit_table' : 'add_table'; ?>" 
                        class="btn btn-primary">
                    <?php echo $edit_table ? 'Обновить стол' : 'Добавить стол'; ?>
                </button>
                
                <?php if ($edit_table): ?>
                    <a href="admin.php#tables" class="btn btn-secondary">Отмена</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Список столов -->
        <div class="admin-table-section">
            <h4>Список столов</h4>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Номер</th>
                            <th>Вместимость</th>
                            <th>Описание</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $table): ?>
                            <tr>
                                <td><?php echo $table['id']; ?></td>
                                <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                                <td><?php echo $table['capacity']; ?> чел.</td>
                                <td><?php echo htmlspecialchars($table['description']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $table['status']; ?>">
                                        <?php 
                                        $statuses = [
                                            'available' => 'Доступен',
                                            'reserved' => 'Зарезервирован',
                                            'maintenance' => 'Обслуживание'
                                        ];
                                        echo $statuses[$table['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit_table=<?php echo $table['id']; ?>#tables" 
                                           class="btn btn-primary btn-sm">Редактировать</a>
                                        <a href="?delete_table=<?php echo $table['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Удалить стол?')">Удалить</a>
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