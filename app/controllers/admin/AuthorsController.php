<?php

require_once __DIR__ . '/../../models/admin/AuthorsModel.php';

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
                if (adminAuthorNameExists($conn, $authorName)) {
                    redirectWithFlash('error', 'Author name already exists.');
                }

                if (adminAddAuthor($conn, $authorName)) {
                    redirectWithFlash('success', 'Author added successfully.');
                }

                $errorMessage = $conn->errno === 1062 ? 'Author name already exists.' : 'Unable to add author right now.';
                redirectWithFlash('error', $errorMessage);
            }

            if ($authorId <= 0) {
                redirectWithFlash('error', 'Invalid author selected for update.');
            }

            if (adminAuthorNameExists($conn, $authorName, $authorId)) {
                redirectWithFlash('error', 'Author name already exists.');
            }

            if (adminUpdateAuthor($conn, $authorName, $authorId)) {
                redirectWithFlash('success', 'Author updated successfully.');
            }

            $errorMessage = $conn->errno === 1062 ? 'Author name already exists.' : 'Unable to update author right now.';
            redirectWithFlash('error', $errorMessage);
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
