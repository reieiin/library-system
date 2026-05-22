<?php

require_once __DIR__ . '/../../models/admin/ReservationsModel.php';

if (!function_exists('adminGetReservationForAction')) {
    function adminGetReservationForAction(mysqli $conn, int $reservationId): ?array
    {
        return adminGetReservationForActionModel($conn, $reservationId);
    }
}

if (!function_exists('adminCreateBorrowRecordForReservation')) {
    function adminCreateBorrowRecordForReservation(mysqli $conn, int $userId, int $bookId): bool
    {
        return adminCreateBorrowRecordForReservationModel($conn, $userId, $bookId);
    }
}

if (!function_exists('adminDecreaseBookCopiesForReservation')) {
    function adminDecreaseBookCopiesForReservation(mysqli $conn, int $bookId): bool
    {
        return adminDecreaseBookCopiesForReservationModel($conn, $bookId);
    }
}

if (!function_exists('adminFulfillReservation')) {
    function adminFulfillReservation(mysqli $conn, int $reservationId): bool
    {
        return adminFulfillReservationModel($conn, $reservationId);
    }
}

if (!function_exists('adminCancelReservation')) {
    function adminCancelReservation(mysqli $conn, int $reservationId): bool
    {
        return adminCancelReservationModel($conn, $reservationId);
    }
}

if (!function_exists('adminGetReservations')) {
    function adminGetReservations(mysqli $conn): array
    {
        return adminGetReservationsModel($conn);
    }
}

if (!function_exists('adminHandleReservationAction')) {
    function adminHandleReservationAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reservation_action'])) {
            return;
        }

        $action = $_POST['reservation_action'];
        $reservationId = (int) ($_POST['reservation_id'] ?? 0);

        if ($reservationId <= 0) {
            redirectWithFlash('error', 'Invalid reservation selected.');
        }

        if ($action === 'fulfill') {
            $conn->begin_transaction();

            try {
                $reservation = adminGetReservationForAction($conn, $reservationId);

                if ($reservation === null) {
                    throw new Exception('Reservation not found or already processed.');
                }

                $userId = (int) ($reservation['user_id'] ?? 0);
                $bookId = (int) ($reservation['book_id'] ?? 0);

                if ((int) ($reservation['available_copies'] ?? 0) <= 0) {
                    throw new Exception('No available copies to fulfill this reservation.');
                }

                if (!adminCreateBorrowRecordForReservation($conn, $userId, $bookId)) {
                    throw new Exception('Unable to create borrow record.');
                }

                if (!adminDecreaseBookCopiesForReservation($conn, $bookId)) {
                    throw new Exception('Unable to update book availability.');
                }

                if (!adminFulfillReservation($conn, $reservationId)) {
                    throw new Exception('Unable to fulfill reservation.');
                }

                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Borrowed book: ' . (string) ($reservation['title'] ?? 'Book #' . $bookId));

                $conn->commit();
                redirectWithFlash('success', 'Reservation fulfilled successfully.');
            } catch (Throwable $exception) {
                $conn->rollback();
                redirectWithFlash('error', $exception->getMessage());
            }
        }

        if ($action === 'cancel') {
            if (adminCancelReservation($conn, $reservationId)) {
                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Cancelled reservation #' . $reservationId);
                redirectWithFlash('success', 'Reservation cancelled successfully.');
            }

            redirectWithFlash('error', 'Unable to cancel reservation.');
        }
    }
}