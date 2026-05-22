<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/controllers/admin/BooksController.php';

adminHandleBookAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$categories = adminGetCategories($conn);
$authors = adminGetAuthors($conn);
$books = adminGetBooks($conn);
$totalBooks = adminGetBookCount($conn);
$totalCategories = adminGetCategoryCount($conn);
$totalAuthors = adminGetAuthorCount($conn);
$latestBookLabel = adminGetLatestBookLabel($conn);
?>
<div class="pagetitle">
    <h1>Books</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Books</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Library management</div>
                    <h2 class="admin-hero-title">Manage Books</h2>
                    <p class="admin-hero-text">Manage the catalog and stock.</p>
                    <div class="admin-hero-actions">
                        <button type="button" class="btn btn-primary" id="addBookBtnTrigger">Add Book</button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card">
                            <div class="admin-stat-label">Total Books</div>
                            <div class="admin-stat-value"><?php echo number_format($totalBooks); ?></div>
                            <div class="admin-stat-note">Catalogued titles</div>
                        </div>
                        <div class="admin-stat-card">
                            <div class="admin-stat-label">Categories</div>
                            <div class="admin-stat-value"><?php echo number_format($totalCategories); ?></div>
                            <div class="admin-stat-note">Available categories</div>
                        </div>
                        <div class="admin-stat-card">
                            <div class="admin-stat-label">Authors</div>
                            <div class="admin-stat-value"><?php echo number_format($totalAuthors); ?></div>
                            <div class="admin-stat-note">Registered authors</div>
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
                    <h5 class="admin-panel-title">Book Directory</h5>
                </div>
                <button type="button" class="btn btn-primary" id="addBookBtnTriggerTop">
                    <i class="bi bi-book-half me-1"></i>Add Book
                </button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">ISBN</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Authors</th>
                                    <th scope="col">Total Copies</th>
                                    <th scope="col">Available Copies</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($books)) : ?>
                                    <?php foreach ($books as $book) : ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($book['title']); ?></div>
                                                <small class="text-muted">Added <?php echo htmlspecialchars(formatDisplayDate($book['created_at'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                            <td><span class="badge admin-badge-category"><?php echo htmlspecialchars($book['category_name']); ?></span></td>
                                            <td><?php echo htmlspecialchars(selectedAuthorBadge($book['author_names'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($book['total_copies']); ?></td>
                                            <td><?php echo htmlspecialchars($book['available_copies']); ?></td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary action-btn edit-book-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editBookModal"
                                                        data-book-id="<?php echo htmlspecialchars($book['book_id']); ?>"
                                                        data-title="<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>"
                                                        data-isbn="<?php echo htmlspecialchars($book['isbn'], ENT_QUOTES); ?>"
                                                        data-category-id="<?php echo htmlspecialchars($book['category_id']); ?>"
                                                        data-total-copies="<?php echo htmlspecialchars($book['total_copies']); ?>"
                                                        data-available-copies="<?php echo htmlspecialchars($book['available_copies']); ?>"
                                                        data-author-ids="<?php echo htmlspecialchars(implode(',', $book['author_id_list']), ENT_QUOTES); ?>"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('Delete this book?');" class="m-0">
                                                        <input type="hidden" name="book_action" value="delete">
                                                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                                        <button type="submit" class="btn btn-outline-danger action-btn">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No books found.</td>
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

<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="book_action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo htmlspecialchars($category['category_id']); ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Authors</label>
                            <select name="author_ids[]" class="form-select" multiple size="6">
                                <option value="0">No Author</option>
                                <?php foreach ($authors as $author) : ?>
                                    <option value="<?php echo htmlspecialchars($author['author_id']); ?>"><?php echo htmlspecialchars($author['author_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold Ctrl or Cmd to select multiple authors.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Copies</label>
                            <input type="number" name="total_copies" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Available Copies</label>
                            <input type="number" name="available_copies" class="form-control" min="0" value="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="book_action" value="update">
                <input type="hidden" name="book_id" id="edit_book_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" id="edit_isbn" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo htmlspecialchars($category['category_id']); ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Authors</label>
                            <select name="author_ids[]" id="edit_author_ids" class="form-select" multiple size="6">
                                <option value="0">No Author</option>
                                <?php foreach ($authors as $author) : ?>
                                    <option value="<?php echo htmlspecialchars($author['author_id']); ?>"><?php echo htmlspecialchars($author['author_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold Ctrl or Cmd to select multiple authors.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Copies</label>
                            <input type="number" name="total_copies" id="edit_total_copies" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Available Copies</label>
                            <input type="number" name="available_copies" id="edit_available_copies" class="form-control" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addBookBtns = document.querySelectorAll('#addBookBtnTrigger, #addBookBtnTriggerTop');
        const categorySelect = document.querySelector('#addBookModal select[name="category_id"]');
        const addAuthorSelect = document.querySelector('#addBookModal select[name="author_ids[]"]');
        const editButtons = document.querySelectorAll('.edit-book-btn');
        const editAuthorSelect = document.getElementById('edit_author_ids');

        // Check if categories exist
        const hasCategoriesOptions = categorySelect.querySelectorAll('option:not([value=""])').length > 0;

        addBookBtns.forEach((addBookBtn) => {
            addBookBtn.addEventListener('click', () => {
                if (!hasCategoriesOptions) {
                    alert('Please create at least one category before adding books.');
                    return;
                }

                const modal = new bootstrap.Modal(document.getElementById('addBookModal'));
                modal.show();
            });
        });

        // Make "No Author" (value 0) exclusive in add select
        if (addAuthorSelect) {
            addAuthorSelect.addEventListener('change', () => {
                const noAuthorOpt = addAuthorSelect.querySelector('option[value="0"]');
                if (noAuthorOpt && noAuthorOpt.selected) {
                    Array.from(addAuthorSelect.options).forEach(opt => { if (opt.value !== '0') opt.selected = false; });
                } else if (noAuthorOpt) {
                    noAuthorOpt.selected = false;
                }
            });
        }

        editButtons.forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('edit_book_id').value = button.dataset.bookId || '';
                document.getElementById('edit_title').value = button.dataset.title || '';
                document.getElementById('edit_isbn').value = button.dataset.isbn || '';
                document.getElementById('edit_category_id').value = button.dataset.categoryId || '';
                document.getElementById('edit_total_copies').value = button.dataset.totalCopies || 1;
                document.getElementById('edit_available_copies').value = button.dataset.availableCopies || 0;

                const selectedAuthorIds = (button.dataset.authorIds || '').split(',').filter(Boolean);
                Array.from(editAuthorSelect.options).forEach((option) => {
                    option.selected = selectedAuthorIds.includes(option.value);
                });
                // If no authors selected, mark "No Author" option
                const noAuthorOpt = editAuthorSelect.querySelector('option[value="0"]');
                if (noAuthorOpt) {
                    if (selectedAuthorIds.length === 0) {
                        noAuthorOpt.selected = true;
                    } else {
                        noAuthorOpt.selected = false;
                    }
                }
            });
        });

        // Make "No Author" (value 0) exclusive in edit select as well
        if (editAuthorSelect) {
            editAuthorSelect.addEventListener('change', () => {
                const noAuthorOpt = editAuthorSelect.querySelector('option[value="0"]');
                if (noAuthorOpt && noAuthorOpt.selected) {
                    Array.from(editAuthorSelect.options).forEach(opt => { if (opt.value !== '0') opt.selected = false; });
                } else if (noAuthorOpt) {
                    noAuthorOpt.selected = false;
                }
            });
        }
    });
</script>


<?php
include('../includes/footer.php');
?>