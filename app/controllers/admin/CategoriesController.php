<?php

require_once __DIR__ . '/../../models/admin/CategoriesModel.php';

if (!function_exists('adminCategoryHasBooks')) {
    function adminCategoryHasBooks(mysqli $conn, int $categoryId): bool
    {
        $stmt = $conn->prepare('SELECT book_id FROM books WHERE category_id = ? LIMIT 1');
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasBooks = $result->num_rows > 0;
        $stmt->close();

        return $hasBooks;
    }
}

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
                $stmt = $conn->prepare('INSERT INTO categories (category_name) VALUES (?)');
                $stmt->bind_param('s', $categoryName);

                if ($stmt->execute()) {
                    $stmt->close();
                    redirectWithFlash('success', 'Category added successfully.');
                }

                $errorMessage = $conn->errno === 1062 ? 'Category name already exists.' : 'Unable to add category right now.';
                $stmt->close();
                redirectWithFlash('error', $errorMessage);
            }

            if ($categoryId <= 0) {
                redirectWithFlash('error', 'Invalid category selected for update.');
            }

            $stmt = $conn->prepare('UPDATE categories SET category_name = ? WHERE category_id = ?');
            $stmt->bind_param('si', $categoryName, $categoryId);

            if ($stmt->execute()) {
                $stmt->close();
                redirectWithFlash('success', 'Category updated successfully.');
            }

            $errorMessage = $conn->errno === 1062 ? 'Category name already exists.' : 'Unable to update category right now.';
            $stmt->close();
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

            $stmt = $conn->prepare('DELETE FROM categories WHERE category_id = ?');
            $stmt->bind_param('i', $categoryId);

            if ($stmt->execute()) {
                $stmt->close();
                redirectWithFlash('success', 'Category deleted successfully.');
            }

            $errorMessage = $conn->errno === 1451 ? 'This category is in use by books.' : 'Unable to delete category right now.';
            $stmt->close();
            redirectWithFlash('error', $errorMessage);
        }
    }
}
