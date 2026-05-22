<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/models/admin/FinesModel.php';
require_once __DIR__ . '/../../app/controllers/admin/FinesController.php';

adminHandleFineAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$fines = adminGetFines($conn);
?>
<div class="pagetitle">
    <h1>Fines</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Fines</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Payments</div>
                    <h2 class="admin-hero-title">Manage Fines</h2>
                    <p class="admin-hero-text">Track unpaid balances and mark payments.</p>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Total Fines</div>
                            <div class="admin-stat-value"><?php echo number_format(count($fines)); ?></div>
                            <div class="admin-stat-note">Outstanding records</div>
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
                    <h5 class="admin-panel-title">Fines</h5>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Book</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($fines)) : ?>
                                    <?php foreach ($fines as $fine) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($fine['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($fine['book_title']); ?></td>
                                            <td><?php echo htmlspecialchars(number_format((float) $fine['amount'], 2)); ?></td>
                                            <td><span class="badge <?php echo fineBadgeClass($fine['status']); ?>"><?php echo htmlspecialchars(ucfirst($fine['status'])); ?></span></td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($fine['created_at'], true)); ?></td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <?php if ($fine['status'] === 'unpaid') : ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Mark as Paid">
                                                            <input type="hidden" name="fine_action" value="mark_paid">
                                                            <input type="hidden" name="fine_id" value="<?php echo htmlspecialchars($fine['fine_id']); ?>">
                                                            <button type="submit" class="btn btn-outline-success action-btn">
                                                                <i class="bi bi-cash-coin"></i>
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
                                        <td colspan="6" class="text-center text-muted py-4">No fines found.</td>
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