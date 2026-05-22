<?php

if (!function_exists('userGetFines')) {
    function userGetFines(mysqli $conn, int $userId): array
    {
        return userFetchAllRows($conn, '
            SELECT
                f.fine_id,
                f.amount,
                f.status,
                f.created_at,
                b.title AS book_title
            FROM fines f
            INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id
            INNER JOIN books b ON br.book_id = b.book_id
            WHERE br.user_id = ' . $userId . '
            ORDER BY f.created_at DESC, f.fine_id DESC
        ');
    }
}
