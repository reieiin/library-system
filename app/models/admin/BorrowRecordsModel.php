<?php

if (!function_exists('adminGetBorrowRecordForAction')) {
    function adminGetBorrowRecordForAction(mysqli $conn, int $borrowId): ?array
    {
        $recordStmt = $conn->prepare('SELECT br.user_id, br.book_id, br.status, b.title AS book_title FROM borrow_records br INNER JOIN books b ON br.book_id = b.book_id WHERE br.borrow_id = ? LIMIT 1');
        $recordStmt->bind_param('i', $borrowId);
        $recordStmt->execute();
        $recordResult = $recordStmt->get_result();

        if ($recordResult->num_rows === 0) {
            $recordStmt->close();
            return null;
        }

        $record = $recordResult->fetch_assoc();
        $recordStmt->close();

        return $record;
    }
}

if (!function_exists('adminMarkBorrowRecordReturned')) {
    function adminMarkBorrowRecordReturned(mysqli $conn, int $borrowId): bool
    {
        $updateRecordStmt = $conn->prepare('UPDATE borrow_records SET status = "returned", return_date = CURDATE() WHERE borrow_id = ?');
        $updateRecordStmt->bind_param('i', $borrowId);
        $result = $updateRecordStmt->execute();
        $updateRecordStmt->close();

        return $result;
    }
}

if (!function_exists('adminBorrowRecordCanBeUnreturned')) {
    function adminBorrowRecordCanBeUnreturned(mysqli $conn, int $borrowId): bool
    {
        $recordStmt = $conn->prepare('SELECT user_id, book_id, status FROM borrow_records WHERE borrow_id = ? LIMIT 1');
        $recordStmt->bind_param('i', $borrowId);
        $recordStmt->execute();
        $recordResult = $recordStmt->get_result();

        if ($recordResult->num_rows === 0) {
            $recordStmt->close();
            return false;
        }

        $record = $recordResult->fetch_assoc();
        $recordStmt->close();

        if (($record['status'] ?? '') !== 'returned') {
            return false;
        }

        $userId = (int) ($record['user_id'] ?? 0);
        $bookId = (int) ($record['book_id'] ?? 0);

        if ($userId <= 0 || $bookId <= 0) {
            return false;
        }

        $latestStmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE user_id = ? AND book_id = ? ORDER BY borrow_id DESC LIMIT 1');
        $latestStmt->bind_param('ii', $userId, $bookId);
        $latestStmt->execute();
        $latestResult = $latestStmt->get_result();

        if ($latestResult->num_rows === 0) {
            $latestStmt->close();
            return false;
        }

        $latestRow = $latestResult->fetch_assoc();
        $latestStmt->close();

        if ((int) ($latestRow['borrow_id'] ?? 0) !== $borrowId) {
            return false;
        }

        return !adminUserHasActiveBorrowForBook($conn, $userId, $bookId, $borrowId);
    }
}

if (!function_exists('adminMarkBorrowRecordUnreturned')) {
    function adminMarkBorrowRecordUnreturned(mysqli $conn, int $borrowId): bool
    {
        $updateRecordStmt = $conn->prepare('UPDATE borrow_records SET status = "borrowed", return_date = NULL WHERE borrow_id = ? AND status = "returned"');
        $updateRecordStmt->bind_param('i', $borrowId);
        $result = $updateRecordStmt->execute();
        $updateRecordStmt->close();

        return $result;
    }
}

if (!function_exists('adminUserHasActiveBorrowForBook')) {
    function adminUserHasActiveBorrowForBook(mysqli $conn, int $userId, int $bookId, int $excludeBorrowId = 0): bool
    {
        if ($excludeBorrowId > 0) {
            $stmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE user_id = ? AND book_id = ? AND borrow_id <> ? AND status IN ("borrowed", "overdue") LIMIT 1');
            $stmt->bind_param('iii', $userId, $bookId, $excludeBorrowId);
        } else {
            $stmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE user_id = ? AND book_id = ? AND status IN ("borrowed", "overdue") LIMIT 1');
            $stmt->bind_param('ii', $userId, $bookId);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $hasActiveBorrow = $result->num_rows > 0;
        $stmt->close();

        return $hasActiveBorrow;
    }
}

if (!function_exists('adminIncreaseBookCopies')) {
    function adminIncreaseBookCopies(mysqli $conn, int $bookId): bool
    {
        $updateBookStmt = $conn->prepare('UPDATE books SET available_copies = LEAST(total_copies, available_copies + 1) WHERE book_id = ?');
        $updateBookStmt->bind_param('i', $bookId);
        $result = $updateBookStmt->execute();
        $updateBookStmt->close();

        return $result;
    }
}

if (!function_exists('adminDecreaseBookCopies')) {
    function adminDecreaseBookCopies(mysqli $conn, int $bookId): bool
    {
        $updateBookStmt = $conn->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ? AND available_copies > 0');
        $updateBookStmt->bind_param('i', $bookId);
        $result = $updateBookStmt->execute();
        $updateBookStmt->close();

        return $result;
    }
}

if (!function_exists('adminMarkBorrowRecordOverdue')) {
    function adminMarkBorrowRecordOverdue(mysqli $conn, int $borrowId): bool
    {
        $stmt = $conn->prepare('UPDATE borrow_records SET status = "overdue" WHERE borrow_id = ? AND status <> "returned"');
        $stmt->bind_param('i', $borrowId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminBorrowFineExists')) {
    function adminBorrowFineExists(mysqli $conn, int $borrowId): bool
    {
        $fineCheckStmt = $conn->prepare('SELECT fine_id FROM fines WHERE borrow_id = ? LIMIT 1');
        $fineCheckStmt->bind_param('i', $borrowId);
        $fineCheckStmt->execute();
        $fineCheckResult = $fineCheckStmt->get_result();
        $exists = $fineCheckResult->num_rows > 0;
        $fineCheckStmt->close();

        return $exists;
    }
}

if (!function_exists('adminCreateBorrowFine')) {
    function adminCreateBorrowFine(mysqli $conn, int $borrowId): bool
    {
        $fineStmt = $conn->prepare('INSERT INTO fines (borrow_id, amount, status, created_at) VALUES (?, 50.00, "unpaid", NOW())');
        $fineStmt->bind_param('i', $borrowId);
        $result = $fineStmt->execute();
        $fineStmt->close();

        return $result;
    }
}

if (!function_exists('adminGetBorrowRecords')) {
    function adminGetBorrowRecords(mysqli $conn): array
    {
        return fetchAllRows($conn, '
            SELECT
                br.borrow_id,
                br.user_id,
                br.book_id,
                br.borrow_date,
                br.due_date,
                br.return_date,
                br.status,
                CONCAT(u.first_name, " ", u.last_name) AS user_name,
                b.title AS book_title,
                CASE
                    WHEN br.status = "returned"
                         AND br.borrow_id = (
                             SELECT br2.borrow_id
                             FROM borrow_records br2
                             WHERE br2.user_id = br.user_id
                               AND br2.book_id = br.book_id
                             ORDER BY br2.borrow_id DESC
                             LIMIT 1
                         )
                         AND NOT EXISTS (
                             SELECT 1
                             FROM borrow_records br3
                             WHERE br3.user_id = br.user_id
                               AND br3.book_id = br.book_id
                               AND br3.borrow_id <> br.borrow_id
                               AND br3.status IN ("borrowed", "overdue")
                         )
                    THEN 1
                    ELSE 0
                END AS can_unreturn
            FROM borrow_records br
            INNER JOIN users u ON br.user_id = u.user_id
            INNER JOIN books b ON br.book_id = b.book_id
            ORDER BY br.borrow_date DESC, br.borrow_id DESC
        ');
    }
}