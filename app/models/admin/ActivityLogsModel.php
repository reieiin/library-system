<?php

if (!function_exists('adminGetActivityLogs')) {
    function adminGetActivityLogs(mysqli $conn): array
    {
        return fetchAllRows($conn, '
            SELECT
                al.log_id,
                al.action,
                al.log_date,
                CONCAT(u.first_name, " ", u.last_name) AS user_name
            FROM activity_logs al
            INNER JOIN users u ON al.user_id = u.user_id
            ORDER BY al.log_date DESC, al.log_id DESC
        ');
    }
}

if (!function_exists('adminClearActivityLogs')) {
    function adminClearActivityLogs(mysqli $conn): bool
    {
        return $conn->query('TRUNCATE TABLE activity_logs') === true;
    }
}