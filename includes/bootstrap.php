<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';

function fetch_all_assoc(mysqli_stmt $stmt): array
{
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function current_user_id(): ?int
{
    return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
}
