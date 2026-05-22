<?php

if (!function_exists('adminGetBorrowRecordForAction')) {
    function adminGetBorrowRecordForAction(mysqli $conn, int $borrowId): ?array
    {
        $recordStmt = $conn->prepare('SELECT br.book_id, br.status, b.title AS book_title FROM borrow_records br INNER JOIN books b ON br.book_id = b.book_id WHERE br.borrow_id = ? LIMIT 1');
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
                br.borrow_date,
                br.due_date,
                br.return_date,
                br.status,
                CONCAT(u.first_name, " ", u.last_name) AS user_name,
                b.title AS book_title
            FROM borrow_records br
            INNER JOIN users u ON br.user_id = u.user_id
            INNER JOIN books b ON br.book_id = b.book_id
            ORDER BY br.borrow_date DESC, br.borrow_id DESC
        ');
    }
}