<?php

if (!function_exists('adminGetCategories')) {
    function adminGetCategories(mysqli $conn): array
    {
        return fetchAllRows($conn, 'SELECT category_id, category_name FROM categories ORDER BY category_name ASC');
    }
}

if (!function_exists('adminGetCategoryCount')) {
    function adminGetCategoryCount(mysqli $conn): int
    {
        return fetchCount($conn, 'SELECT COUNT(*) AS total FROM categories');
    }
}

if (!function_exists('adminGetLatestCategoryName')) {
    function adminGetLatestCategoryName(mysqli $conn): ?string
    {
        $row = fetchFirstRow($conn, 'SELECT category_name FROM categories ORDER BY category_id DESC LIMIT 1');

        return $row['category_name'] ?? null;
    }
}

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