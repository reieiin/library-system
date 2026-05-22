<?php
include '../../app/middleware/user.php';
require_once __DIR__ . '/../../app/models/user/BrowseBooksModel.php';
include_once '../../app/controllers/user/BrowseBooksController.php';

$authUser = $_SESSION['authUser'] ?? [];
$userId = (int) ($authUser['user_id'] ?? $_SESSION['user_id'] ?? 0);
userHandleBrowseBookAction($conn, $userId);

$pageTitle = 'Browse Books - Library System';
include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_user.php');

$books = userGetBrowsableBooks($conn, $userId);
$browseStats = userGetBrowseStats($conn);
?>
<div class="pagetitle">
  <h1>Browse Books</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Browse Books</li>
    </ol>
  </nav>
</div>

<section class="section user-shell">
  <div class="user-hero user-hero-compact">
    <div class="row g-3 align-items-center">
      <div class="col-lg-7">
        <div class="user-kicker">Catalog</div>
        <h1 class="mb-2">Browse Books</h1>
        <p>Browse the catalog and check book availability.</p>
      </div>
      <div class="col-lg-5">
        <div class="user-browse-summary">
          <span class="user-browse-chip"><i class="bi bi-collection"></i> <?php echo (int) $browseStats['totalBooks']; ?> total</span>
          <span class="user-browse-chip"><i class="bi bi-check2-circle"></i> <?php echo (int) $browseStats['availableBooks']; ?> available</span>
          <span class="user-browse-chip"><i class="bi bi-slash-circle"></i> <?php echo (int) $browseStats['outOfStockBooks']; ?> unavailable</span>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($books)) { ?>
    <div class="user-book-grid">
      <?php foreach ($books as $book) { ?>
        <?php
        $availableCopies = (int) ($book['available_copies'] ?? 0);
        $totalCopies = (int) ($book['total_copies'] ?? 0);
        $hasActiveBorrow = !empty($book['has_active_borrow']);
        $hasActiveReservation = !empty($book['has_active_reservation']);
        $mainAction = $hasActiveBorrow ? '' : ($hasActiveReservation ? '' : ($availableCopies > 0 ? 'borrow' : 'reserve'));
        $mainActionLabel = $hasActiveBorrow ? 'Already Borrowed' : ($hasActiveReservation ? 'Already Reserved' : ($availableCopies > 0 ? 'Borrow now' : 'Reserve spot'));
        $availabilityLabel = $hasActiveBorrow ? 'Already borrowed' : ($hasActiveReservation ? 'Already reserved' : ($availableCopies > 0 ? $availableCopies . ' available' : 'Currently unavailable'));
        $availabilityClass = $hasActiveBorrow || $hasActiveReservation ? 'user-status-warning' : ($availableCopies > 0 ? 'user-status-success' : 'user-status-warning');
        $bookTitle = $book['title'] ?? 'Untitled book';
        $bookAuthors = trim((string) ($book['author_names'] ?? ''));
        $bookCategory = trim((string) ($book['category_name'] ?? ''));
        $coverLabel = userBookInitials($bookTitle);
        $coverStyle = userBookCoverStyle($bookTitle);
        ?>
        <article class="user-book-card">
          <div class="user-book-cover" style="<?php echo htmlspecialchars($coverStyle); ?>">
            <span><?php echo htmlspecialchars($coverLabel); ?></span>
          </div>
          <div class="user-book-body">
            <div>
              <h3 class="user-book-title"><?php echo htmlspecialchars($bookTitle); ?></h3>
              <div class="user-meta">By <?php echo htmlspecialchars($bookAuthors !== '' ? $bookAuthors : 'Library collection'); ?></div>
              <div class="user-meta"><?php echo htmlspecialchars($bookCategory !== '' ? $bookCategory : 'General reading'); ?></div>
            </div>

            <div class="user-book-footer">
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="user-status <?php echo $availabilityClass; ?>"><?php echo htmlspecialchars($availabilityLabel); ?></span>
                <span class="user-meta"><?php echo $totalCopies; ?> total copies</span>
              </div>

              <?php if ($hasActiveBorrow || $hasActiveReservation) : ?>
                <button type="button" class="btn btn-secondary w-100 user-action" disabled><?php echo htmlspecialchars($mainActionLabel); ?></button>
              <?php else : ?>
                <form method="post" class="m-0">
                  <input type="hidden" name="catalog_action" value="<?php echo htmlspecialchars($mainAction); ?>">
                  <input type="hidden" name="book_id" value="<?php echo (int) $book['book_id']; ?>">
                  <button type="submit" class="btn btn-primary w-100 user-action"><?php echo htmlspecialchars($mainActionLabel); ?></button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php } ?>
    </div>
    <?php } else { ?>
    <div class="user-empty-state">
      <h3>No books found</h3>
      <p>There are no items in the catalog right now. Please check back later or go to the dashboard.</p>
      <a href="index" class="btn btn-primary user-action">Back to Dashboard</a>
    </div>
  <?php } ?>
</section>

<?php
include('../includes/footer.php');
?>