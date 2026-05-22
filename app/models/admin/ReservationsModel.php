<?php

if (!function_exists('adminGetReservationForActionModel')) {
    function adminGetReservationForActionModel(mysqli $conn, int $reservationId): ?array
    {
        $resStmt = $conn->prepare('SELECT r.user_id, r.book_id, b.title, b.available_copies FROM reservations r INNER JOIN books b ON r.book_id = b.book_id WHERE r.reservation_id = ? AND r.status = "active" LIMIT 1 FOR UPDATE');
        $resStmt->bind_param('i', $reservationId);
        $resStmt->execute();
        $resResult = $resStmt->get_result();

        if ($resResult->num_rows === 0) {
            $resStmt->close();
            return null;
        }

        $reservation = $resResult->fetch_assoc();
        $resStmt->close();

        return $reservation;
    }
}

if (!function_exists('adminCreateBorrowRecordForReservationModel')) {
    function adminCreateBorrowRecordForReservationModel(mysqli $conn, int $userId, int $bookId): bool
    {
        $insertBorrowStmt = $conn->prepare('INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), "borrowed")');
        $insertBorrowStmt->bind_param('ii', $userId, $bookId);
        $result = $insertBorrowStmt->execute();

        if (!$result) {
            $insertBorrowStmt->close();
            return false;
        }

        $insertBorrowStmt->close();
        return true;
    }
}

if (!function_exists('adminDecreaseBookCopiesForReservationModel')) {
    function adminDecreaseBookCopiesForReservationModel(mysqli $conn, int $bookId): bool
    {
        $updateBookStmt = $conn->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ? AND available_copies > 0');
        $updateBookStmt->bind_param('i', $bookId);
        $result = $updateBookStmt->execute() && $updateBookStmt->affected_rows === 1;
        $updateBookStmt->close();

        return $result;
    }
}

if (!function_exists('adminFulfillReservationModel')) {
    function adminFulfillReservationModel(mysqli $conn, int $reservationId): bool
    {
        $stmt = $conn->prepare('UPDATE reservations SET status = "fulfilled" WHERE reservation_id = ? AND status = "active"');
        $stmt->bind_param('i', $reservationId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminCancelReservationModel')) {
    function adminCancelReservationModel(mysqli $conn, int $reservationId): bool
    {
        $stmt = $conn->prepare('UPDATE reservations SET status = "cancelled" WHERE reservation_id = ? AND status = "active"');
        $stmt->bind_param('i', $reservationId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminGetReservationsModel')) {
    function adminGetReservationsModel(mysqli $conn): array
    {
        return fetchAllRows($conn, '
            SELECT
                r.reservation_id,
                r.reservation_date,
                r.status,
                b.available_copies,
                CONCAT(u.first_name, " ", u.last_name) AS user_name,
                b.title AS book_title
            FROM reservations r
            INNER JOIN users u ON r.user_id = u.user_id
            INNER JOIN books b ON r.book_id = b.book_id
            ORDER BY r.reservation_date DESC, r.reservation_id DESC
        ');
    }
}