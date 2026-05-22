<?php
include '../../app/middleware/admin.php';
require_once __DIR__ . '/../../app/controllers/admin/CategoriesController.php';

adminHandleCategoryAction($conn);

include('../includes/header.php');
include('../includes/topbar.php');
include('../includes/sidebar_admin.php');

?>
<?php

$categories = adminGetCategories($conn);
$totalCategories = adminGetCategoryCount($conn);
$latestCategoryLabel = adminGetLatestCategoryName($conn) ?? 'No categories yet';
?>
<div class="pagetitle">
    <h1>Categories</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
</div>

<section class="section admin-shell">
    <div class="card admin-hero mb-1">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="admin-kicker">Library management</div>
                    <h2 class="admin-hero-title">Manage Categories</h2>
                    <p class="admin-hero-text">Manage categories used in the catalog.</p>
                    <div class="admin-hero-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-quick-actions">
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Total Categories</div>
                            <div class="admin-stat-value"><?php echo number_format($totalCategories); ?></div>
                            <div class="admin-stat-note">Category records in the catalog</div>
                        </div>
                        <div class="admin-stat-card" style="padding: 0.9rem 1rem;">
                            <div class="admin-stat-label">Latest Category</div>
                            <div class="admin-stat-value" style="font-size: clamp(1.05rem, 1.5vw, 1.35rem); line-height: 1.15;"><?php echo htmlspecialchars($latestCategoryLabel); ?></div>
                            <div class="admin-stat-note">Most recently added entry</div>
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
                    <h5 class="admin-panel-title">Category Directory</h5>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Category
                </button>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Category Name</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)) : ?>
                            <?php foreach ($categories as $category) : ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($category['category_name']); ?></div>
                                        <small class="text-muted">ID #<?php echo htmlspecialchars($category['category_id']); ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="action-controls">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary action-btn edit-category-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal"
                                                data-category-id="<?php echo htmlspecialchars($category['category_id']); ?>"
                                                data-category-name="<?php echo htmlspecialchars($category['category_name'], ENT_QUOTES); ?>"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('Delete this category?');" class="m-0">
                                                <input type="hidden" name="category_action" value="delete">
                                                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['category_id']); ?>">
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
                                <td colspan="2" class="text-center text-muted py-4">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="category_action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                <input type="hidden" name="category_action" value="update">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" id="edit_category_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-category-btn').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('edit_category_id').value = button.dataset.categoryId || '';
                document.getElementById('edit_category_name').value = button.dataset.categoryName || '';
            });
        });
    });
</script>


<?php
include('../includes/footer.php');
?>