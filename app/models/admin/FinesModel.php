<?php

if (!function_exists('adminGetFines')) {
    function adminGetFines(mysqli $conn): array
    {
        return fetchAllRows($conn, '
            SELECT
                f.fine_id,
                f.borrow_id,
                f.amount,
                f.status,
                f.created_at,
                CONCAT(u.first_name, " ", u.last_name) AS user_name,
                b.title AS book_title,
                br.status AS borrow_status
            FROM fines f
            INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id
            INNER JOIN users u ON br.user_id = u.user_id
            INNER JOIN books b ON br.book_id = b.book_id
            ORDER BY f.created_at DESC, f.fine_id DESC
        ');
    }
}

if (!function_exists('adminMarkFinePaid')) {
    function adminMarkFinePaid(mysqli $conn, int $fineId): bool
    {
        $stmt = $conn->prepare('UPDATE fines SET status = "paid" WHERE fine_id = ? AND status = "unpaid"');
        $stmt->bind_param('i', $fineId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}