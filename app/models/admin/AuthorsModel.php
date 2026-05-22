<?php

if (!function_exists('adminAddAuthor')) {
    function adminAddAuthor(mysqli $conn, string $authorName): bool
    {
        $stmt = $conn->prepare('INSERT INTO authors (author_name) VALUES (?)');
        $stmt->bind_param('s', $authorName);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminUpdateAuthor')) {
    function adminUpdateAuthor(mysqli $conn, string $authorName, int $authorId): bool
    {
        $stmt = $conn->prepare('UPDATE authors SET author_name = ? WHERE author_id = ?');
        $stmt->bind_param('si', $authorName, $authorId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminDeleteAuthor')) {
    function adminDeleteAuthor(mysqli $conn, int $authorId): bool
    {
        $stmt = $conn->prepare('DELETE FROM authors WHERE author_id = ?');
        $stmt->bind_param('i', $authorId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

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