<?php
include '../../app/middleware/user.php';
require_once __DIR__ . '/../../app/models/user/DashboardModel.php';

$pageTitle = 'Dashboard - Library System';
include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_user.php');

$authUser = $_SESSION['authUser'] ?? [];
$displayName = trim($authUser['fullname'] ?? '') !== '' ? trim($authUser['fullname']) : 'Library member';
$userId = (int) ($authUser['user_id'] ?? $_SESSION['user_id'] ?? 0);
$dashboardSummary = userGetDashboardSummary($conn, $userId);

$summaryCards = [
    [
        'icon' => 'bi-book-half',
        'label' => 'Borrowed books',
        'value' => (int) $dashboardSummary['borrowedCount'],
        'note' => 'Books you currently have out',
    ],
    [
        'icon' => 'bi-calendar-check',
        'label' => 'Reservations',
        'value' => (int) $dashboardSummary['activeReservations'],
        'note' => 'Active holds waiting for you',
    ],
    [
        'icon' => 'bi-cash-coin',
        'label' => 'Pending fines',
        'value' => (int) $dashboardSummary['pendingFines'],
        'note' => userFormatMoney($dashboardSummary['pendingFineAmount']) . ' outstanding',
    ],
];
?>
<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section user-shell">
  <div class="user-hero">
    <div class="row g-4 align-items-end">
      <div class="col-lg-8">
        <div class="user-kicker">Welcome back</div>
        <h1>Hi, <?php echo htmlspecialchars($displayName); ?></h1>
        <p>Your personal library space is ready. Explore the catalog, and check here for what you have borrowed, reserved, or need to settle.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="browse-books" class="btn btn-primary user-action">Explore Books</a>
      </div>
    </div>
  </div>

  <div class="user-summary-grid">
    <?php foreach ($summaryCards as $card) { ?>
      <article class="user-summary-card">
        <div class="user-summary-icon">
          <i class="bi <?php echo htmlspecialchars($card['icon']); ?>"></i>
        </div>
        <div class="user-summary-label"><?php echo htmlspecialchars($card['label']); ?></div>
        <div class="user-summary-value"><?php echo htmlspecialchars((string) $card['value']); ?></div>
        <div class="user-summary-note"><?php echo htmlspecialchars($card['note']); ?></div>
      </article>
    <?php } ?>
  </div>

  <div class="user-section">
    <div class="user-section-head">
      <div>
        <h2 class="user-section-title">Start browsing</h2>
        <p class="user-section-text">Open the catalog to discover books and borrow or reserve them with a single click.</p>
      </div>
      <a href="browse-books" class="btn btn-outline-primary user-action">Go to Browse Books</a>
    </div>
  </div>
</section>

<?php
include('../includes/footer.php');
?>