<?php
include '../../app/middleware/user.php';
include_once '../../app/controllers/user/BorrowedBooksController.php';

$authUser = $_SESSION['authUser'] ?? [];
$userId = (int) ($authUser['user_id'] ?? $_SESSION['user_id'] ?? 0);
userHandleBorrowedBookAction($conn, $userId);

$pageTitle = 'My Borrowed Books - Library System';
include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_user.php');

$borrowedBooks = userGetBorrowedBooks($conn, $userId);
?>
<div class="pagetitle">
  <h1>My Borrowed Books</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">My Borrowed Books</li>
    </ol>
  </nav>
</div>

<section class="section user-shell">
  <div class="user-hero user-hero-compact">
    <div class="row g-3 align-items-center">
      <div class="col-lg-8">
        <div class="user-kicker">Current reads</div>
        <h1 class="mb-2">Books you currently have</h1>
        <p>See each book's due date, status, and a quick option to return it when ready.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="browse-books" class="btn btn-outline-primary user-action">Browse More Books</a>
      </div>
    </div>
  </div>

  <div class="card user-table-card">
    <div class="card-body">
      <?php if (!empty($borrowedBooks)) { ?>
        <div class="table-responsive user-table-wrap">
          <table class="table align-middle">
            <thead>
              <tr>
                <th scope="col">Book</th>
                <th scope="col">Borrowed</th>
                <th scope="col">Due</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($borrowedBooks as $borrowedBook) { ?>
                <?php
                $status = (string) ($borrowedBook['status'] ?? '');
                $isReturnable = in_array($status, ['borrowed', 'overdue'], true);
                ?>
                <tr>
                  <td>
                    <div class="user-row-title"><?php echo htmlspecialchars($borrowedBook['book_title'] ?? 'Untitled book'); ?></div>
                  </td>
                  <td class="user-muted"><?php echo htmlspecialchars(userFormatDate($borrowedBook['borrow_date'] ?? null)); ?></td>
                  <td class="user-muted"><?php echo htmlspecialchars(userFormatDate($borrowedBook['due_date'] ?? null)); ?></td>
                  <td>
                    <span class="user-status <?php echo htmlspecialchars(userStatusClass($status)); ?>"><?php echo htmlspecialchars(userLabelFromStatus($status)); ?></span>
                  </td>
                  <td class="text-end user-row-action">
                    <?php if ($isReturnable) { ?>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="borrowed_action" value="return">
                        <input type="hidden" name="borrow_id" value="<?php echo (int) $borrowedBook['borrow_id']; ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm user-action">Return Book</button>
                      </form>
                    <?php } else { ?>
                      <span class="user-muted">Completed</span>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="user-empty-state">
          <h3>No borrowed books yet</h3>
          <p>When you borrow a book, it will appear here with its due date and return option.</p>
          <a href="browse-books" class="btn btn-primary user-action">Browse Books</a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php
include('../includes/footer.php');
?>