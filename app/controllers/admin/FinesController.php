<?php

require_once __DIR__ . '/../../models/admin/FinesModel.php';

if (!function_exists('adminHandleFineAction')) {
    function adminHandleFineAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['fine_action'])) {
            return;
        }

        $action = $_POST['fine_action'];
        $fineId = (int) ($_POST['fine_id'] ?? 0);

        if ($fineId <= 0) {
            redirectWithFlash('error', 'Invalid fine selected.');
        }

        if ($action === 'mark_paid') {
            $stmt = $conn->prepare('UPDATE fines SET status = "paid" WHERE fine_id = ? AND status = "unpaid"');
            $stmt->bind_param('i', $fineId);

            if ($stmt->execute()) {
                $stmt->close();
                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Marked fine as paid #' . $fineId);
                redirectWithFlash('success', 'Fine marked as paid.');
            }

            $stmt->close();
            redirectWithFlash('error', 'Unable to mark fine as paid.');
        }
    }
}
