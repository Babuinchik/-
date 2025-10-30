<?php
// Проверка прав администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$bookings = getAllBookings();

// Изменение статуса бронирования
if (isset($_GET['update_booking_status'])) {
    $booking_id = $_GET['update_booking_status'];
    $status = $_GET['status'];
    
    if (updateBookingStatus($booking_id, $status)) {
        echo '<div class="alert alert-success">Статус бронирования обновлен!</div>';
        $bookings = getAllBookings();
    } else {
        echo '<div class="alert alert-error">Ошибка при обновлении статуса!</div>';
    }
}

// Удаление бронирования
if (isset($_GET['delete_booking'])) {
    $booking_id = $_GET['delete_booking'];
    
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    if ($stmt->execute([$booking_id])) {
        echo '<div class="alert alert-success">Бронирование удалено!</div>';
        $bookings = getAllBookings();
    }
}
?>

<div class="admin-tab-content">
    <h3>Управление бронированиями</h3>
    
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Стол</th>
                    <th>Дата и время</th>
                    <th>Гостей</th>
                    <th>Статус</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong>
                            <br><small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                            <?php if ($booking['user_phone']): ?>
                                <br><small>📞 <?php echo htmlspecialchars($booking['user_phone']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($booking['table_number']); ?>
                            <br><small>(до <?php echo $booking['capacity']; ?> чел.)</small>
                        </td>
                        <td>
                            <?php echo date('d.m.Y', strtotime($booking['booking_date'])); ?>
                            <br><strong><?php echo $booking['booking_time']; ?></strong>
                        </td>
                        <td><?php echo $booking['guests']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php 
                                $statuses = [
                                    'pending' => 'Ожидание',
                                    'confirmed' => 'Подтверждено',
                                    'cancelled' => 'Отменено',
                                    'completed' => 'Завершено'
                                ];
                                echo $statuses[$booking['status']];
                                ?>
                            </span>
                        </td>
                        <td><?php echo date('d.m.Y H:i', strtotime($booking['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($booking['status'] != 'confirmed'): ?>
                                    <a href="?update_booking_status=<?php echo $booking['id']; ?>&status=confirmed#bookings" 
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Подтвердить бронирование?')">Подтвердить</a>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] != 'cancelled'): ?>
                                    <a href="?update_booking_status=<?php echo $booking['id']; ?>&status=cancelled#bookings" 
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('Отменить бронирование?')">Отменить</a>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] == 'confirmed'): ?>
                                    <a href="?update_booking_status=<?php echo $booking['id']; ?>&status=completed#bookings" 
                                       class="btn btn-info btn-sm"
                                       onclick="return confirm('Отметить как завершенное?')">Завершить</a>
                                <?php endif; ?>
                                
                                <a href="?delete_booking=<?php echo $booking['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Удалить бронирование?')">Удалить</a>
                            </div>
                            
                            <?php if (!empty($booking['special_requests'])): ?>
                                <div class="special-requests">
                                    <small><strong>Пожелания:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?></small>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>