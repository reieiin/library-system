<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/controllers/admin/BorrowRecordsController.php';

adminHandleBorrowRecordAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$records = adminGetBorrowRecords($conn);
?>
<div class="pagetitle">
    <h1>Borrow Records</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Borrow Records</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Transactions</div>
                    <h2 class="admin-hero-title">Manage Borrow Records</h2>
                    <p class="admin-hero-text">View and manage loan records.</p>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Total Records</div>
                            <div class="admin-stat-value"><?php echo number_format(count($records)); ?></div>
                            <div class="admin-stat-note">Loan entries tracked</div>
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
                    <h5 class="admin-panel-title">Borrow Records</h5>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Book</th>
                                    <th scope="col">Borrow Date</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Return Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($records)) : ?>
                                    <?php foreach ($records as $record) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($record['book_title']); ?></td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($record['borrow_date'])); ?></td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($record['due_date'])); ?></td>
                                            <td><?php echo $record['return_date'] ? htmlspecialchars(formatDisplayDate($record['return_date'])) : '<span class="text-muted">-</span>'; ?></td>
                                            <td><span class="badge <?php echo borrowStatusBadge($record['status']); ?>"><?php echo htmlspecialchars(ucfirst($record['status'])); ?></span></td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <?php if ($record['status'] !== 'returned') : ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Mark as Returned">
                                                            <input type="hidden" name="record_action" value="mark_returned">
                                                            <input type="hidden" name="borrow_id" value="<?php echo htmlspecialchars($record['borrow_id']); ?>">
                                                            <button type="submit" class="btn btn-outline-success action-btn">
                                                                <i class="bi bi-check2-circle"></i>
                                                            </button>
                                                        </form>

                                                        <?php if ($record['status'] !== 'overdue') : ?>
                                                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Set Overdue">
                                                                <input type="hidden" name="record_action" value="set_overdue">
                                                                <input type="hidden" name="borrow_id" value="<?php echo htmlspecialchars($record['borrow_id']); ?>">
                                                                <button type="submit" class="btn btn-outline-warning action-btn">
                                                                    <i class="bi bi-exclamation-circle"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    <?php elseif (!empty($record['can_unreturn'])) : ?>
                                                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" title="Mark as Unreturned">
                                                            <input type="hidden" name="record_action" value="mark_unreturned">
                                                            <input type="hidden" name="borrow_id" value="<?php echo htmlspecialchars($record['borrow_id']); ?>">
                                                            <button type="submit" class="btn btn-outline-secondary action-btn">
                                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No borrow records found.</td>
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