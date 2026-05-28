<?php

require_once __DIR__ . '/../../models/admin/CategoriesModel.php';

if (!function_exists('adminHandleCategoryAction')) {
    function adminHandleCategoryAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['category_action'])) {
            return;
        }

        $action = $_POST['category_action'];

        if ($action === 'add' || $action === 'update') {
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $categoryName = trim($_POST['category_name'] ?? '');

            if ($categoryName === '') {
                redirectWithFlash('error', 'Category name is required.');
            }

            if ($action === 'add') {
                if (adminCategoryNameExists($conn, $categoryName)) {
                    redirectWithFlash('error', 'Category name already exists.');
                }

                if (adminAddCategory($conn, $categoryName)) {
                    redirectWithFlash('success', 'Category added successfully.');
                }

                $errorMessage = $conn->errno === 1062 ? 'Category name already exists.' : 'Unable to add category right now.';
                redirectWithFlash('error', $errorMessage);
            }

            if ($categoryId <= 0) {
                redirectWithFlash('error', 'Invalid category selected for update.');
            }

            if (adminCategoryNameExists($conn, $categoryName, $categoryId)) {
                redirectWithFlash('error', 'Category name already exists.');
            }

            if (adminUpdateCategory($conn, $categoryName, $categoryId)) {
                redirectWithFlash('success', 'Category updated successfully.');
            }

            $errorMessage = $conn->errno === 1062 ? 'Category name already exists.' : 'Unable to update category right now.';
            redirectWithFlash('error', $errorMessage);
        }

        if ($action === 'delete') {
            $categoryId = (int) ($_POST['category_id'] ?? 0);

            if ($categoryId <= 0) {
                redirectWithFlash('error', 'Invalid category selected for deletion.');
            }

            if (adminCategoryHasBooks($conn, $categoryId)) {
                redirectWithFlash('warning', 'This category cannot be deleted because it is being used by one or more books.');
            }

            if (adminDeleteCategory($conn, $categoryId)) {
                redirectWithFlash('success', 'Category deleted successfully.');
            }

            $errorMessage = $conn->errno === 1451 ? 'This category is in use by books.' : 'Unable to delete category right now.';
            redirectWithFlash('error', $errorMessage);
        }
    }
}
