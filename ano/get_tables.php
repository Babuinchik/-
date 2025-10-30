<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$guests = intval($_POST['guests'] ?? 0);

if (empty($date) || empty($time) || $guests < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $available_tables = getAvailableTables($date, $time, $guests);
    echo json_encode(['success' => true, 'tables' => $available_tables]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>