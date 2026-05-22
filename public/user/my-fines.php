<?php
include '../../app/middleware/user.php';
include_once '../../app/controllers/user/FinesController.php';

$pageTitle = 'My Fines - Library System';
include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_user.php');

$authUser = $_SESSION['authUser'] ?? [];
$userId = (int) ($authUser['user_id'] ?? $_SESSION['user_id'] ?? 0);
$fines = userGetFines($conn, $userId);
?>
<div class="pagetitle">
  <h1>My Fines</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">My Fines</li>
    </ol>
  </nav>
</div>

<section class="section user-shell">
  <div class="user-hero user-hero-compact">
    <div class="row g-3 align-items-center">
      <div class="col-lg-8">
        <div class="user-kicker">Account balance</div>
        <h1 class="mb-2">Your fines</h1>
        <p>View fines on your account, including amount and status.</p>
      </div>
    </div>
  </div>

  <div class="card user-table-card">
    <div class="card-body">
      <?php if (!empty($fines)) { ?>
        <div class="table-responsive user-table-wrap">
          <table class="table align-middle">
            <thead>
              <tr>
                <th scope="col">Book</th>
                <th scope="col">Amount</th>
                <th scope="col">Created</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($fines as $fine) { ?>
                <?php $status = (string) ($fine['status'] ?? ''); ?>
                <tr>
                  <td>
                    <div class="user-row-title"><?php echo htmlspecialchars($fine['book_title'] ?? 'Untitled book'); ?></div>
                  </td>
                  <td class="user-muted"><?php echo htmlspecialchars(userFormatMoney($fine['amount'] ?? 0)); ?></td>
                  <td class="user-muted"><?php echo htmlspecialchars(userFormatDate($fine['created_at'] ?? null)); ?></td>
                  <td>
                    <span class="user-status <?php echo htmlspecialchars(userStatusClass($status)); ?>"><?php echo htmlspecialchars(userLabelFromStatus($status)); ?></span>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="user-empty-state">
          <h3>No fines recorded</h3>
          <p>You're all clear for now. Any future fines will appear here with the amount and status.</p>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php
include('../includes/footer.php');
?>