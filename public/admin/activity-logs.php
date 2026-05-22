<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/controllers/admin/ActivityLogsController.php';

adminHandleActivityLogsAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

$logs = adminGetActivityLogs($conn);
?>
<div class="pagetitle">
    <h1>Activity Logs</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Activity Logs</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Audit</div>
                    <h2 class="admin-hero-title">Activity Logs</h2>
                    <p class="admin-hero-text">View recent administrative actions.</p>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Recent Actions</div>
                            <div class="admin-stat-value"><?php echo number_format(count($logs)); ?></div>
                            <div class="admin-stat-note">Latest audit entries</div>
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
                    <h5 class="admin-panel-title">Activity Logs</h5>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="m-0" onsubmit="return confirm('Clear all activity logs? This cannot be undone.');">
                    <input type="hidden" name="activity_logs_action" value="clear">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Clear Logs
                    </button>
                </form>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)) : ?>
                                    <?php foreach ($logs as $log) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td><?php echo htmlspecialchars(formatDisplayDate($log['log_date'], true)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No activity logs found.</td>
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