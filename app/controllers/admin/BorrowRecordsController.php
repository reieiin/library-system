<?php

require_once __DIR__ . '/../../models/admin/BorrowRecordsModel.php';

if (!function_exists('adminHandleBorrowRecordAction')) {
    function adminHandleBorrowRecordAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['record_action'])) {
            return;
        }

        $action = $_POST['record_action'];
        $borrowId = (int) ($_POST['borrow_id'] ?? 0);

        if ($borrowId <= 0) {
            redirectWithFlash('error', 'Invalid borrow record selected.');
        }

        if ($action === 'mark_returned') {
            $conn->begin_transaction();

            try {
                $record = adminGetBorrowRecordForAction($conn, $borrowId);

                if ($record === null) {
                    throw new Exception('Borrow record not found.');
                }

                if ($record['status'] !== 'returned') {
                    adminMarkBorrowRecordReturned($conn, $borrowId);

                    $bookId = (int) $record['book_id'];
                    adminIncreaseBookCopies($conn, $bookId);
                }

                $conn->commit();

                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Marked borrow record as returned: ' . (string) ($record['book_title'] ?? 'Book #' . $borrowId));
                redirectWithFlash('success', 'Borrow record marked as returned.');
            } catch (Throwable $exception) {
                $conn->rollback();
                redirectWithFlash('error', 'Unable to mark record as returned.');
            }
        }

        if ($action === 'mark_unreturned') {
            $conn->begin_transaction();

            try {
                $record = adminGetBorrowRecordForAction($conn, $borrowId);

                if ($record === null) {
                    throw new Exception('Borrow record not found.');
                }

                if ($record['status'] !== 'returned') {
                    throw new Exception('Only returned books can be marked as unreturned.');
                }

                $userId = (int) ($record['user_id'] ?? 0);
                $bookId = (int) ($record['book_id'] ?? 0);

                if ($userId <= 0 || $bookId <= 0) {
                    throw new Exception('Invalid borrow record data.');
                }

                $latestStmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE user_id = ? AND book_id = ? ORDER BY borrow_id DESC LIMIT 1');
                $latestStmt->bind_param('ii', $userId, $bookId);
                $latestStmt->execute();
                $latestResult = $latestStmt->get_result();

                if ($latestResult->num_rows === 0) {
                    $latestStmt->close();
                    throw new Exception('Borrow record not found.');
                }

                $latestRow = $latestResult->fetch_assoc();
                $latestStmt->close();

                if ((int) ($latestRow['borrow_id'] ?? 0) !== $borrowId) {
                    throw new Exception('Only the latest returned borrow for this user and book can be marked as unreturned.');
                }

                if (adminUserHasActiveBorrowForBook($conn, $userId, $bookId, $borrowId)) {
                    throw new Exception('This user already has an active borrow for the same book.');
                }

                if (!adminMarkBorrowRecordUnreturned($conn, $borrowId)) {
                    throw new Exception('Unable to update borrow record.');
                }

                if (!adminDecreaseBookCopies($conn, $bookId)) {
                    throw new Exception('Unable to update book copies.');
                }

                $conn->commit();

                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Marked borrow record as unreturned: ' . (string) ($record['book_title'] ?? 'Book #' . $borrowId));
                redirectWithFlash('success', 'Borrow record marked as unreturned.');
            } catch (Throwable $exception) {
                $conn->rollback();
                redirectWithFlash('error', 'Unable to mark record as unreturned.');
            }
        }

        if ($action === 'set_overdue') {
            $conn->begin_transaction();

            try {
                $record = adminGetBorrowRecordForAction($conn, $borrowId);

                if ($record === null) {
                    throw new Exception('Borrow record not found.');
                }

                if ($record['status'] === 'returned') {
                    throw new Exception('Returned books cannot be marked overdue.');
                }

                if (!adminMarkBorrowRecordOverdue($conn, $borrowId)) {
                    throw new Exception('Unable to update borrow record.');
                }

                if (!adminBorrowFineExists($conn, $borrowId)) {
                    if (!adminCreateBorrowFine($conn, $borrowId)) {
                        throw new Exception('Unable to create fine.');
                    }
                }

                $conn->commit();

                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Marked borrow record as overdue: ' . (string) ($record['book_title'] ?? 'Book #' . $borrowId));
                redirectWithFlash('success', 'Borrow record marked as overdue.');
            } catch (Throwable $exception) {
                $conn->rollback();
                redirectWithFlash('error', 'Unable to mark record as overdue.');
            }
        }
    }
}