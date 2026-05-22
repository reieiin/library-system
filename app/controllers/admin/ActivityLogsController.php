<?php

require_once __DIR__ . '/../../models/admin/ActivityLogsModel.php';

if (!function_exists('adminHandleActivityLogsAction')) {
    function adminHandleActivityLogsAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['activity_logs_action'] ?? '') !== 'clear') {
            return;
        }

        if (adminClearActivityLogs($conn)) {
            redirectWithFlash('success', 'Activity logs cleared successfully.');
        }

        redirectWithFlash('error', 'Unable to clear activity logs right now.');
    }
}