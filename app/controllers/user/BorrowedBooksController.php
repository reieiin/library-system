<?php

require_once __DIR__ . '/../../models/user/BorrowedBooksModel.php';

if (!function_exists('userGetBorrowRecordForReturn')) {
    function userGetBorrowRecordForReturn(mysqli $conn, int $borrowId, int $userId): ?array
    {
        return userGetBorrowRecordForReturnModel($conn, $borrowId, $userId);
    }
}

if (!function_exists('userUpdateBorrowRecordReturned')) {
    function userUpdateBorrowRecordReturned(mysqli $conn, int $borrowId, int $userId): void
    {
        userUpdateBorrowRecordReturnedModel($conn, $borrowId, $userId);
    }
}

if (!function_exists('userUpdateBookCopiesAfterReturn')) {
    function userUpdateBookCopiesAfterReturn(mysqli $conn, int $bookId): void
    {
        userUpdateBookCopiesAfterReturnModel($conn, $bookId);
    }
}

if (!function_exists('userGetBorrowedBooks')) {
    function userGetBorrowedBooks(mysqli $conn, int $userId): array
    {
        return userGetBorrowedBooksModel($conn, $userId);
    }
}

if (!function_exists('userHandleBorrowedBookAction')) {
    function userHandleBorrowedBookAction(mysqli $conn, int $userId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['borrowed_action'])) {
            return;
        }

        $action = $_POST['borrowed_action'];
        $borrowId = (int) ($_POST['borrow_id'] ?? 0);

        if ($action !== 'return' || $borrowId <= 0) {
            userRedirectWithFlash('error', 'Invalid action for borrowed books.');
        }

        $conn->begin_transaction();

        try {
            $borrowRecord = userGetBorrowRecordForReturn($conn, $borrowId, $userId);

            if ($borrowRecord === null) {
                throw new Exception('Borrow record not found.');
            }

            if ($borrowRecord['status'] === 'returned') {
                throw new Exception('This book is already returned.');
            }

            userUpdateBorrowRecordReturned($conn, $borrowId, $userId);

            $bookId = (int) $borrowRecord['book_id'];
            userUpdateBookCopiesAfterReturn($conn, $bookId);

            logActivity($conn, (int) ($_SESSION['user_id'] ?? $userId), 'Returned book: ' . (string) ($borrowRecord['book_title'] ?? 'Book #' . $bookId));

            $conn->commit();
            userRedirectWithFlash('success', 'Book returned successfully.');
        } catch (Throwable $exception) {
            $conn->rollback();
            userRedirectWithFlash('error', $exception->getMessage());
        }
    }
}
