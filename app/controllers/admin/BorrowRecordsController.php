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