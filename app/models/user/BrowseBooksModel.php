<?php

if (!function_exists('userGetBorrowBookForBrowseModel')) {
    function userGetBorrowBookForBrowseModel(mysqli $conn, int $bookId): ?array
    {
        $bookStmt = $conn->prepare('SELECT title, available_copies FROM books WHERE book_id = ? LIMIT 1 FOR UPDATE');
        $bookStmt->bind_param('i', $bookId);
        $bookStmt->execute();
        $bookResult = $bookStmt->get_result();

        if ($bookResult->num_rows === 0) {
            $bookStmt->close();
            return null;
        }

        $book = $bookResult->fetch_assoc();
        $bookStmt->close();

        return $book;
    }
}

if (!function_exists('userGetActiveReservationForBrowseModel')) {
    function userGetActiveReservationForBrowseModel(mysqli $conn, int $bookId): bool
    {
        $activeReservationStmt = $conn->prepare('SELECT reservation_id FROM reservations WHERE book_id = ? AND status = "active" ORDER BY reservation_date ASC, reservation_id ASC LIMIT 1');
        $activeReservationStmt->bind_param('i', $bookId);
        $activeReservationStmt->execute();
        $activeReservationResult = $activeReservationStmt->get_result();
        $hasActiveReservation = $activeReservationResult->num_rows > 0;
        $activeReservationStmt->close();

        return $hasActiveReservation;
    }
}

if (!function_exists('userGetExistingBorrowForBrowseModel')) {
    function userGetExistingBorrowForBrowseModel(mysqli $conn, int $userId, int $bookId): bool
    {
        $existingBorrowStmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE user_id = ? AND book_id = ? AND status IN ("borrowed", "overdue") LIMIT 1');
        $existingBorrowStmt->bind_param('ii', $userId, $bookId);
        $existingBorrowStmt->execute();
        $existingBorrowResult = $existingBorrowStmt->get_result();
        $hasBorrow = $existingBorrowResult->num_rows > 0;
        $existingBorrowStmt->close();

        return $hasBorrow;
    }
}

if (!function_exists('userInsertBorrowRecordForBrowseModel')) {
    function userInsertBorrowRecordForBrowseModel(mysqli $conn, int $userId, int $bookId): void
    {
        $insertBorrowStmt = $conn->prepare('INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), "borrowed")');
        $insertBorrowStmt->bind_param('ii', $userId, $bookId);
        $insertBorrowStmt->execute();
        $insertBorrowStmt->close();
    }
}

if (!function_exists('userUpdateBookCopiesForBrowseModel')) {
    function userUpdateBookCopiesForBrowseModel(mysqli $conn, int $bookId): void
    {
        $updateBookStmt = $conn->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ? AND available_copies > 0');
        $updateBookStmt->bind_param('i', $bookId);
        $updateBookStmt->execute();
        $updateBookStmt->close();
    }
}

if (!function_exists('userFulfillReservationForBrowseModel')) {
    function userFulfillReservationForBrowseModel(mysqli $conn, int $userId, int $bookId): void
    {
        $fulfillReservationStmt = $conn->prepare('UPDATE reservations SET status = "fulfilled" WHERE user_id = ? AND book_id = ? AND status = "active"');
        $fulfillReservationStmt->bind_param('ii', $userId, $bookId);
        $fulfillReservationStmt->execute();
        $fulfillReservationStmt->close();
    }
}

if (!function_exists('userGetBookAvailabilityForReserveModel')) {
    function userGetBookAvailabilityForReserveModel(mysqli $conn, int $bookId): ?array
    {
        $bookStmt = $conn->prepare('SELECT available_copies FROM books WHERE book_id = ? LIMIT 1 FOR UPDATE');
        $bookStmt->bind_param('i', $bookId);
        $bookStmt->execute();
        $bookResult = $bookStmt->get_result();

        if ($bookResult->num_rows === 0) {
            $bookStmt->close();
            return null;
        }

        $book = $bookResult->fetch_assoc();
        $bookStmt->close();

        return $book;
    }
}

if (!function_exists('userGetExistingReservationForBrowseModel')) {
    function userGetExistingReservationForBrowseModel(mysqli $conn, int $userId, int $bookId): bool
    {
        $existingReservationStmt = $conn->prepare('SELECT reservation_id FROM reservations WHERE user_id = ? AND book_id = ? AND status = "active" LIMIT 1');
        $existingReservationStmt->bind_param('ii', $userId, $bookId);
        $existingReservationStmt->execute();
        $existingReservationResult = $existingReservationStmt->get_result();
        $hasReservation = $existingReservationResult->num_rows > 0;
        $existingReservationStmt->close();

        return $hasReservation;
    }
}

if (!function_exists('userInsertReservationForBrowseModel')) {
    function userInsertReservationForBrowseModel(mysqli $conn, int $userId, int $bookId): void
    {
        $insertReservationStmt = $conn->prepare('INSERT INTO reservations (user_id, book_id, reservation_date, status) VALUES (?, ?, CURDATE(), "active")');
        $insertReservationStmt->bind_param('ii', $userId, $bookId);
        $insertReservationStmt->execute();
        $insertReservationStmt->close();
    }
}

if (!function_exists('userGetBrowsableBooksModel')) {
    function userGetBrowsableBooksModel(mysqli $conn, int $userId): array
    {
        return userFetchAllRows($conn, '
            SELECT
                b.book_id,
                b.title,
                c.category_name,
                EXISTS(
                    SELECT 1
                    FROM borrow_records br
                    WHERE br.user_id = ' . $userId . '
                      AND br.book_id = b.book_id
                      AND br.status IN ("borrowed", "overdue")
                    LIMIT 1
                ) AS has_active_borrow,
                EXISTS(
                    SELECT 1
                    FROM reservations r
                    WHERE r.user_id = ' . $userId . '
                      AND r.book_id = b.book_id
                      AND r.status = "active"
                    LIMIT 1
                ) AS has_active_reservation,
                GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ", ") AS author_names,
                b.available_copies,
                b.total_copies
            FROM books b
            INNER JOIN categories c ON b.category_id = c.category_id
            LEFT JOIN book_authors ba ON b.book_id = ba.book_id
            LEFT JOIN authors a ON ba.author_id = a.author_id
            GROUP BY b.book_id, b.title, c.category_name, b.available_copies, b.total_copies
            ORDER BY b.title ASC
        ');
    }
}

if (!function_exists('userGetBrowseStatsModel')) {
    function userGetBrowseStatsModel(mysqli $conn): array
    {
        return [
            'totalBooks' => userFetchCount($conn, 'SELECT COUNT(*) AS total FROM books'),
            'availableBooks' => userFetchCount($conn, 'SELECT COUNT(*) AS total FROM books WHERE available_copies > 0'),
            'outOfStockBooks' => userFetchCount($conn, 'SELECT COUNT(*) AS total FROM books WHERE available_copies = 0'),
        ];
    }
}