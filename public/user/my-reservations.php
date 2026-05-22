<?php
include '../../app/middleware/user.php';
include_once '../../app/controllers/user/ReservationsController.php';

$authUser = $_SESSION['authUser'] ?? [];
$userId = (int) ($authUser['user_id'] ?? $_SESSION['user_id'] ?? 0);
userHandleReservationAction($conn, $userId);

$pageTitle = 'My Reservations - Library System';
include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_user.php');

$reservations = userGetReservations($conn, $userId);
?>
<div class="pagetitle">
  <h1>My Reservations</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">My Reservations</li>
    </ol>
  </nav>
</div>

<section class="section user-shell">
  <div class="user-hero user-hero-compact">
    <div class="row g-3 align-items-center">
      <div class="col-lg-8">
        <div class="user-kicker">Reservation status</div>
        <h1 class="mb-2">Your reservations</h1>
        <p>View your reservations; cancel active holds if needed.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="browse-books" class="btn btn-outline-primary user-action">Browse Books</a>
      </div>
    </div>
  </div>

  <div class="card user-table-card">
    <div class="card-body">
      <?php if (!empty($reservations)) { ?>
        <div class="table-responsive user-table-wrap">
          <table class="table align-middle">
            <thead>
              <tr>
                <th scope="col">Book</th>
                <th scope="col">Reserved</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservations as $reservation) { ?>
                <?php
                $status = (string) ($reservation['status'] ?? '');
                $isActive = $status === 'active';
                ?>
                <tr>
                  <td>
                    <div class="user-row-title"><?php echo htmlspecialchars($reservation['book_title'] ?? 'Untitled book'); ?></div>
                  </td>
                  <td class="user-muted"><?php echo htmlspecialchars(userFormatDate($reservation['reservation_date'] ?? null)); ?></td>
                  <td>
                    <span class="user-status <?php echo htmlspecialchars(userStatusClass($status)); ?>"><?php echo htmlspecialchars(userLabelFromStatus($status)); ?></span>
                  </td>
                  <td class="text-end user-row-action">
                    <?php if ($isActive) { ?>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="reservation_action" value="cancel">
                        <input type="hidden" name="reservation_id" value="<?php echo (int) $reservation['reservation_id']; ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm user-action">Cancel</button>
                      </form>
                    <?php } else { ?>
                      <span class="user-muted">No action</span>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="user-empty-state">
          <h3>No reservations yet</h3>
          <p>Reserved books appear here with status and available actions.</p>
          <a href="browse-books" class="btn btn-primary user-action">Browse Books</a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php
include('../includes/footer.php');
?>