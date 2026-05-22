<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/controllers/admin/AuthorsController.php';

adminHandleAuthorAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$authors = adminGetAuthors($conn);
$totalAuthors = adminGetAuthorCount($conn);
?>
<div class="pagetitle">
    <h1>Authors</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Authors</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Library management</div>
                    <h2 class="admin-hero-title">Manage Authors</h2>
                    <p class="admin-hero-text">Manage authors in the catalog.</p>
                    <div class="admin-hero-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuthorModal">Add Author</button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Total Authors</div>
                            <div class="admin-stat-value"><?php echo number_format($totalAuthors); ?></div>
                            <div class="admin-stat-note">Registered author records</div>
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
                    <h5 class="admin-panel-title">Author Directory</h5>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Author
                </button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Author Name</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($authors)) : ?>
                                    <?php foreach ($authors as $author) : ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($author['author_name']); ?></div>
                                            </td>
                                            <td class="text-end">
                                                <div class="action-controls">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary action-btn edit-author-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editAuthorModal"
                                                        data-author-id="<?php echo htmlspecialchars($author['author_id']); ?>"
                                                        data-author-name="<?php echo htmlspecialchars($author['author_name'], ENT_QUOTES); ?>"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('Delete this author?');" class="m-0">
                                                        <input type="hidden" name="author_action" value="delete">
                                                        <input type="hidden" name="author_id" value="<?php echo htmlspecialchars($author['author_id']); ?>">
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
                                        <td colspan="2" class="text-center text-muted py-4">No authors found.</td>
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

<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="author_action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAuthorModalLabel">Add Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Author Name</label>
                    <input type="text" name="author_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Author</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="author_action" value="update">
                <input type="hidden" name="author_id" id="edit_author_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAuthorModalLabel">Edit Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Author Name</label>
                    <input type="text" name="author_name" id="edit_author_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Author</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-author-btn').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('edit_author_id').value = button.dataset.authorId || '';
                document.getElementById('edit_author_name').value = button.dataset.authorName || '';
            });
        });
    });
</script>


<?php
include('../includes/footer.php');
?>