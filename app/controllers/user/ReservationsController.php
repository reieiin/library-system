<?php

require_once __DIR__ . '/../../models/user/ReservationsModel.php';

if (!function_exists('userCancelReservation')) {
    function userCancelReservation(mysqli $conn, int $reservationId, int $userId): bool
    {
        return userCancelReservationModel($conn, $reservationId, $userId);
    }
}

if (!function_exists('userGetReservations')) {
    function userGetReservations(mysqli $conn, int $userId): array
    {
        return userGetReservationsModel($conn, $userId);
    }
}

if (!function_exists('userHandleReservationAction')) {
    function userHandleReservationAction(mysqli $conn, int $userId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reservation_action'])) {
            return;
        }

        $action = $_POST['reservation_action'];
        $reservationId = (int) ($_POST['reservation_id'] ?? 0);

        if ($action !== 'cancel' || $reservationId <= 0) {
            userRedirectWithFlash('error', 'Invalid reservation action.');
        }

        if (userCancelReservation($conn, $reservationId, $userId)) {
            logActivity($conn, (int) ($_SESSION['user_id'] ?? $userId), 'Cancelled reservation #' . $reservationId);
            userRedirectWithFlash('success', 'Reservation cancelled successfully.');
        }

        userRedirectWithFlash('error', 'Unable to cancel reservation.');
    }
}
