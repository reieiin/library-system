<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/models/admin/DashboardModel.php';

$dashboardSummary = adminGetDashboardSummary($conn);
$totalUsers = $dashboardSummary['totalUsers'];
$totalBooks = $dashboardSummary['totalBooks'];
$borrowedBooks = $dashboardSummary['borrowedBooks'];
$overdueBooks = $dashboardSummary['overdueBooks'];
$returnedBooks = $dashboardSummary['returnedBooks'];
$activeLoans = $dashboardSummary['activeLoans'];
$overdueRate = $dashboardSummary['overdueRate'];
$recentActivities = $dashboardSummary['recentActivities'];

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');
?>
<div class="pagetitle">
  <h1>Admin Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section admin-shell">
  <div class="card admin-hero mb-0" style="margin-bottom: 0;">
    <div class="card-body p-4 p-lg-5">
      <div class="row g-4 align-items-center">
        <div class="col-lg-7">
          <div class="admin-kicker">Operations desk</div>
          <h2 class="admin-hero-title">Admin Overview</h2>
          <p class="admin-hero-text">Welcome, <?php echo htmlspecialchars(trim($_SESSION['authUser']['fullname'] ?? 'Administrator')); ?></p>
          <div class="admin-hero-actions">
          </div>
        </div>
        <div class="col-lg-5">
          <div class="admin-quick-actions">
            <a class="admin-quick-action" href="users"><span>Members</span><i class="bi bi-arrow-right"></i></a>
            <a class="admin-quick-action" href="books"><span>Catalog</span><i class="bi bi-arrow-right"></i></a>
            <a class="admin-quick-action" href="borrow-records"><span>Loans</span><i class="bi bi-arrow-right"></i></a>
            <a class="admin-quick-action" href="reservations"><span>Reservations</span><i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="admin-stat-grid">
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Users</div>
      <div class="admin-stat-value"><?php echo number_format($totalUsers); ?></div>
      <div class="admin-stat-note">Registered accounts in the system</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Total Books</div>
      <div class="admin-stat-value"><?php echo number_format($totalBooks); ?></div>
      <div class="admin-stat-note">Titles currently catalogued</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Active Loans</div>
      <div class="admin-stat-value"><?php echo number_format($activeLoans); ?></div>
      <div class="admin-stat-note">Books currently out</div>
    </div>
    <div class="admin-stat-card">
      <div class="admin-stat-label">Overdue Rate</div>
      <div class="admin-stat-value"><?php echo htmlspecialchars(number_format($overdueRate, 1)); ?>%</div>
      <div class="admin-stat-note">Items needing attention</div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xxl-8">
      <div class="card admin-panel admin-table-card">
        <div class="card-body">
          <div class="admin-panel-head">
            <div>
              <h5 class="admin-panel-title">Recent Activity</h5>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col">User</th>
                  <th scope="col">Action</th>
                  <th scope="col">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentActivities)) : ?>
                  <?php foreach ($recentActivities as $activity) : ?>
                    <tr>
                      <td><?php echo htmlspecialchars($activity['user_name']); ?></td>
                      <td><?php echo htmlspecialchars($activity['action']); ?></td>
                      <td><?php echo htmlspecialchars(formatDisplayDate($activity['log_date'], true)); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">No recent activity found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-4">
      <div class="card admin-panel admin-side-card">
        <div class="card-body">
          <div class="admin-panel-head">
            <div>
              <h5 class="admin-panel-title">Circulation Snapshot</h5>
            </div>
          </div>

          <div class="admin-side-list">
            <div class="admin-side-item">
              <div>
                <strong>Borrowed</strong><br>
                <span><?php echo number_format($borrowedBooks); ?> items</span>
              </div>
              <div class="progress flex-grow-1 ms-3" style="height: 8px; max-width: 120px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $activeLoans > 0 ? (int) round(($borrowedBooks / $activeLoans) * 100) : 0; ?>%"></div>
              </div>
            </div>

            <div class="admin-side-item">
              <div>
                <strong>Overdue</strong><br>
                <span><?php echo number_format($overdueBooks); ?> items</span>
              </div>
              <div class="progress flex-grow-1 ms-3" style="height: 8px; max-width: 120px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $activeLoans > 0 ? (int) round(($overdueBooks / $activeLoans) * 100) : 0; ?>%"></div>
              </div>
            </div>

            <div class="admin-side-item">
              <div>
                <strong>Returned</strong><br>
                <span><?php echo number_format($returnedBooks); ?> items</span>
              </div>
              <div class="progress flex-grow-1 ms-3" style="height: 8px; max-width: 120px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($returnedBooks + $activeLoans) > 0 ? (int) round(($returnedBooks / ($returnedBooks + $activeLoans)) * 100) : 0; ?>%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
include('../includes/footer.php');
?>