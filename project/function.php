<?php
function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1;
}

function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>