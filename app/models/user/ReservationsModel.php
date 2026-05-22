<?php

if (!function_exists('userCancelReservationModel')) {
    function userCancelReservationModel(mysqli $conn, int $reservationId, int $userId): bool
    {
        $stmt = $conn->prepare('UPDATE reservations SET status = "cancelled" WHERE reservation_id = ? AND user_id = ? AND status = "active"');
        $stmt->bind_param('ii', $reservationId, $userId);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows > 0;
        $stmt->close();

        return $affectedRows;
    }
}

if (!function_exists('userGetReservationsModel')) {
    function userGetReservationsModel(mysqli $conn, int $userId): array
    {
        return userFetchAllRows($conn, '
            SELECT
                r.reservation_id,
                b.title AS book_title,
                r.reservation_date,
                r.status
            FROM reservations r
            INNER JOIN books b ON r.book_id = b.book_id
            WHERE r.user_id = ' . $userId . '
            ORDER BY r.reservation_date DESC, r.reservation_id DESC
        ');
    }
}