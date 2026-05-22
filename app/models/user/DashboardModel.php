<?php

if (!function_exists('userGetDashboardSummary')) {
    function userGetDashboardSummary(mysqli $conn, int $userId): array
    {
        $borrowedCount = userFetchCount($conn, "SELECT COUNT(*) AS total FROM borrow_records WHERE user_id = {$userId} AND status IN ('borrowed', 'overdue')");
        $activeReservations = userFetchCount($conn, "SELECT COUNT(*) AS total FROM reservations WHERE user_id = {$userId} AND status = 'active'");
        $pendingFines = userFetchCount($conn, "
            SELECT COUNT(*) AS total
            FROM fines f
            INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id
            WHERE br.user_id = {$userId} AND f.status = 'unpaid'
        ");

        $pendingFineAmountRow = userFetchFirstRow($conn, "
            SELECT COALESCE(SUM(f.amount), 0) AS total_amount
            FROM fines f
            INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id
            WHERE br.user_id = {$userId} AND f.status = 'unpaid'
        ");

        return [
            'borrowedCount' => $borrowedCount,
            'activeReservations' => $activeReservations,
            'pendingFines' => $pendingFines,
            'pendingFineAmount' => (float) ($pendingFineAmountRow['total_amount'] ?? 0),
        ];
    }
}