<?php

if (!function_exists('logActivity')) {
    function logActivity(mysqli $conn, int $userId, string $action): void
    {
        if ($userId <= 0 || trim($action) === '') {
            return;
        }

        $stmt = $conn->prepare('INSERT INTO activity_logs (user_id, action) VALUES (?, ?)');

        if (!$stmt) {
            return;
        }

        $stmt->bind_param('is', $userId, $action);
        $stmt->execute();
        $stmt->close();
    }
}