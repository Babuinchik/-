<?php
// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Получаем всех пользователей
$users = getAllUsers();

// Удаление пользователя
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        if ($stmt->execute([$user_id])) {
            echo '<div class="alert alert-success">Пользователь успешно удален!</div>';
            $users = getAllUsers();
        } else {
            echo '<div class="alert alert-error">Ошибка при удалении пользователя!</div>';
        }
    } else {
        echo '<div class="alert alert-error">Нельзя удалить собственный аккаунт!</div>';
    }
}

// Изменение роли пользователя
if (isset($_GET['toggle_role'])) {
    $user_id = $_GET['toggle_role'];
    
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $new_role = $user['role'] == 'admin' ? 'user' : 'admin';
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            if ($stmt->execute([$new_role, $user_id])) {
                echo '<div class="alert alert-success">Роль пользователя изменена!</div>';
                $users = getAllUsers();
            }
        }
    } else {
        echo '<div class="alert alert-error">Нельзя изменить роль собственного аккаунта!</div>';
    }
}
?>

<div class="admin-tab-content">
    <h3>Управление пользователями</h3>
    
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? 'Не указан'); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo $user['role'] == 'admin' ? 'Админ' : 'Пользователь'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?toggle_role=<?php echo $user['id']; ?>" 
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('Изменить роль пользователя?')">
                                        <?php echo $user['role'] == 'admin' ? 'Сделать пользователем' : 'Сделать админом'; ?>
                                    </a>
                                    <a href="?delete_user=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Удалить пользователя? Это действие нельзя отменить!')">
                                        Удалить
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Текущий пользователь</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>  