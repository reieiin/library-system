<?php

if (!function_exists('adminGetDashboardSummary')) {
    function adminGetDashboardSummary(mysqli $conn): array
    {
        $borrowedBooks = fetchCount($conn, "SELECT COUNT(*) AS total FROM borrow_records WHERE status = 'borrowed'");
        $overdueBooks = fetchCount($conn, "SELECT COUNT(*) AS total FROM borrow_records WHERE status = 'overdue'");
        $activeLoans = $borrowedBooks + $overdueBooks;

        return [
            'totalUsers' => fetchCount($conn, 'SELECT COUNT(*) AS total FROM users'),
            'totalBooks' => fetchCount($conn, 'SELECT COUNT(*) AS total FROM books'),
            'borrowedBooks' => $borrowedBooks,
            'overdueBooks' => $overdueBooks,
            'returnedBooks' => fetchCount($conn, "SELECT COUNT(*) AS total FROM borrow_records WHERE status = 'returned'"),
            'activeLoans' => $activeLoans,
            'overdueRate' => $activeLoans > 0 ? round(($overdueBooks / $activeLoans) * 100, 1) : 0,
            'recentActivities' => fetchAllRows($conn, '
                SELECT
                    al.action,
                    al.log_date,
                    CONCAT(u.first_name, " ", u.last_name) AS user_name
                FROM activity_logs al
                INNER JOIN users u ON al.user_id = u.user_id
                ORDER BY al.log_date DESC, al.log_id DESC
                LIMIT 8
            '),
        ];
    }
}