<?php

if (!function_exists('userGetBorrowRecordForReturnModel')) {
    function userGetBorrowRecordForReturnModel(mysqli $conn, int $borrowId, int $userId): ?array
    {
        $borrowStmt = $conn->prepare('SELECT book_id, status FROM borrow_records WHERE borrow_id = ? AND user_id = ? LIMIT 1 FOR UPDATE');
        $borrowStmt->bind_param('ii', $borrowId, $userId);
        $borrowStmt->execute();
        $borrowResult = $borrowStmt->get_result();

        if ($borrowResult->num_rows === 0) {
            $borrowStmt->close();
            return null;
        }

        $borrowRecord = $borrowResult->fetch_assoc();
        $borrowStmt->close();

        return $borrowRecord;
    }
}

if (!function_exists('userUpdateBorrowRecordReturnedModel')) {
    function userUpdateBorrowRecordReturnedModel(mysqli $conn, int $borrowId, int $userId): void
    {
        $updateBorrowStmt = $conn->prepare('UPDATE borrow_records SET status = "returned", return_date = CURDATE() WHERE borrow_id = ? AND user_id = ?');
        $updateBorrowStmt->bind_param('ii', $borrowId, $userId);
        $updateBorrowStmt->execute();
        $updateBorrowStmt->close();
    }
}

if (!function_exists('userUpdateBookCopiesAfterReturnModel')) {
    function userUpdateBookCopiesAfterReturnModel(mysqli $conn, int $bookId): void
    {
        $updateBookStmt = $conn->prepare('UPDATE books SET available_copies = LEAST(total_copies, available_copies + 1) WHERE book_id = ?');
        $updateBookStmt->bind_param('i', $bookId);
        $updateBookStmt->execute();
        $updateBookStmt->close();
    }
}

if (!function_exists('userGetBorrowedBooksModel')) {
    function userGetBorrowedBooksModel(mysqli $conn, int $userId): array
    {
        return userFetchAllRows($conn, '
            SELECT
                br.borrow_id,
                b.title AS book_title,
                br.borrow_date,
                br.due_date,
                br.return_date,
                br.status
            FROM borrow_records br
            INNER JOIN books b ON br.book_id = b.book_id
            WHERE br.user_id = ' . $userId . '
            ORDER BY br.borrow_date DESC, br.borrow_id DESC
        ');
    }
}