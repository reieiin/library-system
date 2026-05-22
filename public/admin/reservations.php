<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/models/admin/ReservationsModel.php';
require_once __DIR__ . '/../../app/controllers/admin/ReservationsController.php';

adminHandleReservationAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$reservations = adminGetReservations($conn);
?>
<div class="pagetitle">
    <h1>Reservations</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Reservations</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Requests</div>
                    <h2 class="admin-hero-title">Manage Reservations</h2>
                    <p class="admin-hero-text">Process reservation requests.</p>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Total Reservations</div>
                            <div class="admin-stat-value"><?php echo number_format(count($reservations)); ?></div>
                            <div class="admin-stat-note">Requests in the system</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-panel admin-table-card">
        <div class="card-body">
            <div class="admin-panel-head mb-0">
                <div>
                    <h5 class="admin-panel-title">Reservations</h5>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Book</th>
                                    <th scope="col">Reservation Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reservations)) : ?>
                                    <?php foreach ($reservations as $reservation) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reservation['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['book_title']); ?></td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($reservation['reservation_date'])); ?></td>
                                            <td><span class="badge <?php echo reservationBadgeClass($reservation['status']); ?>"><?php echo htmlspecialchars(ucfirst($reservation['status'])); ?></span></td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <?php if ($reservation['status'] === 'active' && (int) $reservation['available_copies'] > 0) : ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Approve / Fulfill">
                                                            <input type="hidden" name="reservation_action" value="fulfill">
                                                            <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                                            <button type="submit" class="btn btn-outline-success action-btn">
                                                                <i class="bi bi-check2-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php elseif ($reservation['status'] === 'active') : ?>
                                                        <span class="text-muted small">No available copies</span>
                                                    <?php endif; ?>
                                                    <?php if ($reservation['status'] === 'active') : ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Cancel">
                                                            <input type="hidden" name="reservation_action" value="cancel">
                                                            <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                                            <button type="submit" class="btn btn-outline-danger action-btn">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php else : ?>
                                                        <span class="text-muted small">No actions</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No reservations found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
include('../includes/footer.php');
?>