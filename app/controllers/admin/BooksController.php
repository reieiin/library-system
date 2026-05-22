<?php

include_once __DIR__ . '/AuthorsController.php';
include_once __DIR__ . '/CategoriesController.php';
include_once __DIR__ . '/../../models/admin/BookModel.php';

if (!function_exists('adminHandleBookAction')) {
    function adminHandleBookAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_action'])) {
            return;
        }

        $action = $_POST['book_action'];

        if ($action === 'add' || $action === 'update') {
            if (!adminHasCategories($conn)) {
                redirectWithFlash('error', 'Please create at least one category before adding books.');
            }

            $bookId = (int) ($_POST['book_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $isbn = trim($_POST['isbn'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $totalCopies = (int) ($_POST['total_copies'] ?? 0);
            $availableCopies = (int) ($_POST['available_copies'] ?? 0);
            $authorIds = isset($_POST['author_ids']) && is_array($_POST['author_ids']) ? array_values(array_unique(array_filter(array_map('intval', $_POST['author_ids'])))) : [];

            if ($title === '' || $isbn === '' || $categoryId <= 0 || $totalCopies <= 0 || $availableCopies < 0) {
                redirectWithFlash('error', 'Please fill in all required book fields.');
            }

            if ($availableCopies > $totalCopies) {
                redirectWithFlash('error', 'Available copies cannot exceed total copies.');
            }

            if (!adminCategoryExists($conn, $categoryId)) {
                redirectWithFlash('error', 'Selected category does not exist.');
            }

            if (!adminAuthorsExist($conn, $authorIds)) {
                redirectWithFlash('error', 'One or more selected authors do not exist.');
            }

            if ($action === 'add') {
                $newBookId = adminAddBook($conn, $title, $isbn, $categoryId, $totalCopies, $availableCopies);

                if ($newBookId !== null) {
                    adminReplaceBookAuthors($conn, $newBookId, $authorIds);

                    logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Added new book: ' . $title);

                    redirectWithFlash('success', 'Book added successfully.');
                }

                $errorMessage = $conn->errno === 1062 ? 'ISBN already exists.' : 'Unable to add book right now.';
                redirectWithFlash('error', $errorMessage);
            }

            if ($bookId <= 0) {
                redirectWithFlash('error', 'Invalid book selected for update.');
            }

            if (adminUpdateBook($conn, $title, $isbn, $categoryId, $totalCopies, $availableCopies, $bookId)) {
                adminReplaceBookAuthors($conn, $bookId, $authorIds);

                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Updated book: ' . $title);

                redirectWithFlash('success', 'Book updated successfully.');
            }

            $errorMessage = $conn->errno === 1062 ? 'ISBN already exists.' : 'Unable to update book right now.';
            redirectWithFlash('error', $errorMessage);
        }

        if ($action === 'delete') {
            $bookId = (int) ($_POST['book_id'] ?? 0);

            if ($bookId <= 0) {
                redirectWithFlash('error', 'Invalid book selected for deletion.');
            }

            $blockMessage = adminGetBookDeletionBlockMessage($conn, $bookId);

            if ($blockMessage !== null) {
                redirectWithFlash('warning', $blockMessage);
            }

            $bookTitle = adminGetBookTitleById($conn, $bookId);

            if (adminDeleteBook($conn, $bookId)) {
                logActivity($conn, (int) ($_SESSION['user_id'] ?? 0), 'Deleted book: ' . (string) ($bookTitle ?? 'Unknown title'));
                redirectWithFlash('success', 'Book deleted successfully.');
            }

            redirectWithFlash('error', 'Unable to delete book right now.');
        }
    }
}
