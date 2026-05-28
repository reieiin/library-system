<?php

require_once __DIR__ . '/../../models/user/BrowseBooksModel.php';

if (!function_exists('userGetBorrowBookForBrowse')) {
    function userGetBorrowBookForBrowse(mysqli $conn, int $bookId): ?array
    {
        return userGetBorrowBookForBrowseModel($conn, $bookId);
    }
}

if (!function_exists('userGetActiveReservationForBrowse')) {
    function userGetActiveReservationForBrowse(mysqli $conn, int $bookId): bool
    {
        return userGetActiveReservationForBrowseModel($conn, $bookId);
    }
}

if (!function_exists('userGetExistingBorrowForBrowse')) {
    function userGetExistingBorrowForBrowse(mysqli $conn, int $userId, int $bookId): bool
    {
        return userGetExistingBorrowForBrowseModel($conn, $userId, $bookId);
    }
}

if (!function_exists('userInsertBorrowRecordForBrowse')) {
    function userInsertBorrowRecordForBrowse(mysqli $conn, int $userId, int $bookId): void
    {
        userInsertBorrowRecordForBrowseModel($conn, $userId, $bookId);
    }
}

if (!function_exists('userUpdateBookCopiesForBrowse')) {
    function userUpdateBookCopiesForBrowse(mysqli $conn, int $bookId): void
    {
        userUpdateBookCopiesForBrowseModel($conn, $bookId);
    }
}

if (!function_exists('userFulfillReservationForBrowse')) {
    function userFulfillReservationForBrowse(mysqli $conn, int $userId, int $bookId): void
    {
        userFulfillReservationForBrowseModel($conn, $userId, $bookId);
    }
}

if (!function_exists('userGetBookAvailabilityForReserve')) {
    function userGetBookAvailabilityForReserve(mysqli $conn, int $bookId): ?array
    {
        return userGetBookAvailabilityForReserveModel($conn, $bookId);
    }
}

if (!function_exists('userGetExistingReservationForBrowse')) {
    function userGetExistingReservationForBrowse(mysqli $conn, int $userId, int $bookId): bool
    {
        return userGetExistingReservationForBrowseModel($conn, $userId, $bookId);
    }
}

if (!function_exists('userInsertReservationForBrowse')) {
    function userInsertReservationForBrowse(mysqli $conn, int $userId, int $bookId): void
    {
        userInsertReservationForBrowseModel($conn, $userId, $bookId);
    }
}

if (!function_exists('userGetBrowsableBooks')) {
    function userGetBrowsableBooks(mysqli $conn, int $userId, string $searchTerm = ''): array
    {
        return userGetBrowsableBooksModel($conn, $userId, $searchTerm);
    }
}

if (!function_exists('userGetBrowseStats')) {
    function userGetBrowseStats(mysqli $conn): array
    {
        return userGetBrowseStatsModel($conn);
    }
}

if (!function_exists('userHandleBrowseBookAction')) {
    function userHandleBrowseBookAction(mysqli $conn, int $userId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['catalog_action'])) {
            return;
        }

        $action = $_POST['catalog_action'];
        $bookId = (int) ($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            userRedirectWithFlash('error', 'Invalid book selected.');
        }

        if ($action === 'borrow') {
            $conn->begin_transaction();

            try {
                $book = userGetBorrowBookForBrowse($conn, $bookId);

                if ($book === null) {
                    throw new Exception('Book not found.');
                }

                if ((int) $book['available_copies'] <= 0) {
                    throw new Exception('No available copies for borrowing. You may place a reservation instead.');
                }

                if (userGetActiveReservationForBrowse($conn, $bookId)) {
                    throw new Exception('This book has an active reservation.');
                }

                if (userGetExistingBorrowForBrowse($conn, $userId, $bookId)) {
                    throw new Exception('You already have an active borrow record for this book.');
                }

                userInsertBorrowRecordForBrowse($conn, $userId, $bookId);
                userUpdateBookCopiesForBrowse($conn, $bookId);
                userFulfillReservationForBrowse($conn, $userId, $bookId);

                logActivity($conn, (int) ($_SESSION['user_id'] ?? $userId), 'Borrowed book: ' . (string) ($book['title'] ?? 'Book #' . $bookId));

                $conn->commit();
                userRedirectWithFlash('success', 'Book borrowed successfully.');
            } catch (Throwable $exception) {
                $conn->rollback();
                userRedirectWithFlash('error', $exception->getMessage());
            }
        }

        if ($action === 'reserve') {
            $conn->begin_transaction();

            try {
                $book = userGetBookAvailabilityForReserve($conn, $bookId);

                if ($book === null) {
                    throw new Exception('Book not found.');
                }

                if ((int) $book['available_copies'] > 0) {
                    throw new Exception('This book is available. You can borrow it directly.');
                }

                if (userGetExistingBorrowForBrowse($conn, $userId, $bookId)) {
                    throw new Exception('You already have this book borrowed.');
                }

                if (userGetExistingReservationForBrowse($conn, $userId, $bookId)) {
                    throw new Exception('You already have an active reservation for this book.');
                }

                userInsertReservationForBrowse($conn, $userId, $bookId);

                logActivity($conn, (int) ($_SESSION['user_id'] ?? $userId), 'Reserved book: ' . (string) ($book['title'] ?? 'Book #' . $bookId));

                $conn->commit();
                userRedirectWithFlash('success', 'Reservation placed successfully.');
            } catch (Throwable $exception) {
                $conn->rollback();
                userRedirectWithFlash('error', $exception->getMessage());
            }
        }
    }
}
