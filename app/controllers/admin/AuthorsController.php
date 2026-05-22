<?php

require_once __DIR__ . '/../../models/admin/AuthorsModel.php';

if (!function_exists('adminAuthorHasBooks')) {
    function adminAuthorHasBooks(mysqli $conn, int $authorId): bool
    {
        $stmt = $conn->prepare('SELECT book_id FROM book_authors WHERE author_id = ? LIMIT 1');
        $stmt->bind_param('i', $authorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasBooks = $result->num_rows > 0;
        $stmt->close();

        return $hasBooks;
    }
}

if (!function_exists('adminHandleAuthorAction')) {
    function adminHandleAuthorAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['author_action'])) {
            return;
        }

        $action = $_POST['author_action'];

        if ($action === 'add' || $action === 'update') {
            $authorId = (int) ($_POST['author_id'] ?? 0);
            $authorName = trim($_POST['author_name'] ?? '');

            if ($authorName === '') {
                redirectWithFlash('error', 'Author name is required.');
            }

            if ($action === 'add') {
                if (adminAddAuthor($conn, $authorName)) {
                    redirectWithFlash('success', 'Author added successfully.');
                }

                redirectWithFlash('error', 'Unable to add author right now.');
            }

            if ($authorId <= 0) {
                redirectWithFlash('error', 'Invalid author selected for update.');
            }

            if (adminUpdateAuthor($conn, $authorName, $authorId)) {
                redirectWithFlash('success', 'Author updated successfully.');
            }

            redirectWithFlash('error', 'Unable to update author right now.');
        }

        if ($action === 'delete') {
            $authorId = (int) ($_POST['author_id'] ?? 0);

            if ($authorId <= 0) {
                redirectWithFlash('error', 'Invalid author selected for deletion.');
            }

            if (adminAuthorHasBooks($conn, $authorId)) {
                redirectWithFlash('warning', 'This author cannot be deleted because they are assigned to one or more books.');
            }

            if (adminDeleteAuthor($conn, $authorId)) {
                redirectWithFlash('success', 'Author deleted successfully.');
            }

            redirectWithFlash('error', 'Unable to delete author right now.');
        }
    }
}

if (!function_exists('adminGetAuthors')) {
    function adminGetAuthors(mysqli $conn): array
    {
        return fetchAllRows($conn, 'SELECT author_id, author_name FROM authors ORDER BY author_name ASC');
    }
}

if (!function_exists('adminGetAuthorCount')) {
    function adminGetAuthorCount(mysqli $conn): int
    {
        return fetchCount($conn, 'SELECT COUNT(*) AS total FROM authors');
    }
}