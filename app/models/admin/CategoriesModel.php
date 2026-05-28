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

if (!function_exists('adminCategoryNameExists')) {
    function adminCategoryNameExists(mysqli $conn, string $categoryName, ?int $ignoreCategoryId = null): bool
    {
        if ($ignoreCategoryId !== null && $ignoreCategoryId > 0) {
            $stmt = $conn->prepare('SELECT category_id FROM categories WHERE category_name = ? AND category_id <> ? LIMIT 1');
            $stmt->bind_param('si', $categoryName, $ignoreCategoryId);
        } else {
            $stmt = $conn->prepare('SELECT category_id FROM categories WHERE category_name = ? LIMIT 1');
            $stmt->bind_param('s', $categoryName);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }
}

if (!function_exists('adminAddCategory')) {
    function adminAddCategory(mysqli $conn, string $categoryName): bool
    {
        $stmt = $conn->prepare('INSERT INTO categories (category_name) VALUES (?)');
        $stmt->bind_param('s', $categoryName);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminUpdateCategory')) {
    function adminUpdateCategory(mysqli $conn, string $categoryName, int $categoryId): bool
    {
        $stmt = $conn->prepare('UPDATE categories SET category_name = ? WHERE category_id = ?');
        $stmt->bind_param('si', $categoryName, $categoryId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminDeleteCategory')) {
    function adminDeleteCategory(mysqli $conn, int $categoryId): bool
    {
        $stmt = $conn->prepare('DELETE FROM categories WHERE category_id = ?');
        $stmt->bind_param('i', $categoryId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}