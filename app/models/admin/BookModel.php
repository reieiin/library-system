<?php

if (!function_exists('adminHasCategories')) {
    function adminHasCategories(mysqli $conn): bool
    {
        $categoryCountCheck = $conn->query('SELECT COUNT(*) as count FROM categories');
        $categoryCountResult = $categoryCountCheck->fetch_assoc();
        $hasCategories = (int) $categoryCountResult['count'] > 0;
        $categoryCountCheck->free();

        return $hasCategories;
    }
}

if (!function_exists('adminCategoryExists')) {
    function adminCategoryExists(mysqli $conn, int $categoryId): bool
    {
        $categoryCheck = $conn->prepare('SELECT category_id FROM categories WHERE category_id = ? LIMIT 1');
        $categoryCheck->bind_param('i', $categoryId);
        $categoryCheck->execute();
        $categoryResult = $categoryCheck->get_result();
        $exists = $categoryResult->num_rows > 0;
        $categoryCheck->close();

        return $exists;
    }
}

if (!function_exists('adminAuthorsExist')) {
    function adminAuthorsExist(mysqli $conn, array $authorIds): bool
    {
        if (empty($authorIds)) {
            return true;
        }

        $authorIdList = implode(',', $authorIds);
        $authorCheck = $conn->query("SELECT author_id FROM authors WHERE author_id IN ($authorIdList)");

        if (!$authorCheck || $authorCheck->num_rows !== count($authorIds)) {
            if ($authorCheck instanceof mysqli_result) {
                $authorCheck->free();
            }

            return false;
        }

        $authorCheck->free();

        return true;
    }
}

if (!function_exists('adminAddBook')) {
    function adminAddBook(mysqli $conn, string $title, string $isbn, int $categoryId, int $totalCopies, int $availableCopies): ?int
    {
        $stmt = $conn->prepare('INSERT INTO books (title, isbn, category_id, total_copies, available_copies, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssiii', $title, $isbn, $categoryId, $totalCopies, $availableCopies);

        if ($stmt->execute()) {
            $newBookId = $stmt->insert_id;
            $stmt->close();

            return $newBookId;
        }

        $stmt->close();

        return null;
    }
}

if (!function_exists('adminUpdateBook')) {
    function adminUpdateBook(mysqli $conn, string $title, string $isbn, int $categoryId, int $totalCopies, int $availableCopies, int $bookId): bool
    {
        $stmt = $conn->prepare('UPDATE books SET title = ?, isbn = ?, category_id = ?, total_copies = ?, available_copies = ? WHERE book_id = ?');
        $stmt->bind_param('ssiiii', $title, $isbn, $categoryId, $totalCopies, $availableCopies, $bookId);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminDeleteBook')) {
    function adminDeleteBook(mysqli $conn, int $bookId): bool
    {
        try {
            $conn->begin_transaction();

            $deleteFineStmt = $conn->prepare('
                DELETE f
                FROM fines f
                INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id
                WHERE br.book_id = ?
            ');
            $deleteFineStmt->bind_param('i', $bookId);
            if (!$deleteFineStmt->execute()) {
                $deleteFineStmt->close();
                $conn->rollback();

                return false;
            }
            $deleteFineStmt->close();

            $deleteBorrowStmt = $conn->prepare('DELETE FROM borrow_records WHERE book_id = ?');
            $deleteBorrowStmt->bind_param('i', $bookId);
            if (!$deleteBorrowStmt->execute()) {
                $deleteBorrowStmt->close();
                $conn->rollback();

                return false;
            }
            $deleteBorrowStmt->close();

            $deleteReservationStmt = $conn->prepare('DELETE FROM reservations WHERE book_id = ?');
            $deleteReservationStmt->bind_param('i', $bookId);
            if (!$deleteReservationStmt->execute()) {
                $deleteReservationStmt->close();
                $conn->rollback();

                return false;
            }
            $deleteReservationStmt->close();

            $deleteBookAuthorsStmt = $conn->prepare('DELETE FROM book_authors WHERE book_id = ?');
            $deleteBookAuthorsStmt->bind_param('i', $bookId);
            if (!$deleteBookAuthorsStmt->execute()) {
                $deleteBookAuthorsStmt->close();
                $conn->rollback();

                return false;
            }
            $deleteBookAuthorsStmt->close();

            $stmt = $conn->prepare('DELETE FROM books WHERE book_id = ?');
            $stmt->bind_param('i', $bookId);

            $result = $stmt->execute();
            $stmt->close();

            if (!$result) {
                $conn->rollback();

                return false;
            }

            $conn->commit();

            return $result;
        } catch (mysqli_sql_exception $exception) {
            if ($conn->errno === 0) {
                // No-op: rollback is safe even if a statement failed before execution.
            }

            $conn->rollback();

            return false;
        }
    }
}

if (!function_exists('adminBookIsBorrowed')) {
    function adminBookIsBorrowed(mysqli $conn, int $bookId): bool
    {
        $stmt = $conn->prepare('SELECT borrow_id FROM borrow_records WHERE book_id = ? AND status IN ("borrowed", "overdue") LIMIT 1');
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasRecord = $result->num_rows > 0;
        $stmt->close();

        return $hasRecord;
    }
}

if (!function_exists('adminBookHasActiveReservation')) {
    function adminBookHasActiveReservation(mysqli $conn, int $bookId): bool
    {
        $stmt = $conn->prepare('SELECT reservation_id FROM reservations WHERE book_id = ? AND status = "active" LIMIT 1');
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasReservation = $result->num_rows > 0;
        $stmt->close();

        return $hasReservation;
    }
}

if (!function_exists('adminBookHasUnsettledFine')) {
    function adminBookHasUnsettledFine(mysqli $conn, int $bookId): bool
    {
        $stmt = $conn->prepare('SELECT 1 FROM fines f INNER JOIN borrow_records br ON f.borrow_id = br.borrow_id WHERE br.book_id = ? AND f.status = "unpaid" LIMIT 1');
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasFine = $result->num_rows > 0;
        $stmt->close();

        return $hasFine;
    }
}

if (!function_exists('adminGetBookDeletionBlockMessage')) {
    function adminGetBookDeletionBlockMessage(mysqli $conn, int $bookId): ?string
    {
        if (adminBookIsBorrowed($conn, $bookId)) {
            return 'This book cannot be deleted because it is currently borrowed.';
        }

        if (adminBookHasActiveReservation($conn, $bookId)) {
            return 'This book cannot be deleted because it has an active reservation.';
        }

        if (adminBookHasUnsettledFine($conn, $bookId)) {
            return 'This book cannot be deleted because it has an unpaid fine.';
        }

        return null;
    }
}

if (!function_exists('adminGetBookTitleById')) {
    function adminGetBookTitleById(mysqli $conn, int $bookId): ?string
    {
        $bookRowStmt = $conn->prepare('SELECT title FROM books WHERE book_id = ? LIMIT 1');
        $bookRowStmt->bind_param('i', $bookId);
        $bookRowStmt->execute();
        $bookRowResult = $bookRowStmt->get_result();
        $bookRow = $bookRowResult ? $bookRowResult->fetch_assoc() : null;
        $bookRowStmt->close();

        return $bookRow['title'] ?? null;
    }
}

if (!function_exists('adminReplaceBookAuthors')) {
    function adminReplaceBookAuthors(mysqli $conn, int $bookId, array $authorIds): void
    {
        $deleteAuthorsStmt = $conn->prepare('DELETE FROM book_authors WHERE book_id = ?');
        $deleteAuthorsStmt->bind_param('i', $bookId);
        $deleteAuthorsStmt->execute();
        $deleteAuthorsStmt->close();

        $bookAuthorStmt = $conn->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)');
        foreach ($authorIds as $authorId) {
            $bookAuthorStmt->bind_param('ii', $bookId, $authorId);
            $bookAuthorStmt->execute();
        }
        $bookAuthorStmt->close();
    }
}

if (!function_exists('adminGetBooks')) {
    function adminGetBooks(mysqli $conn): array
    {
        $rows = fetchAllRows($conn, '
            SELECT
                b.book_id,
                b.title,
                b.isbn,
                b.category_id,
                c.category_name,
                b.total_copies,
                b.available_copies,
                b.created_at,
                GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ", ") AS author_names,
                GROUP_CONCAT(DISTINCT a.author_id ORDER BY a.author_name SEPARATOR ",") AS author_id_list
            FROM books b
            INNER JOIN categories c ON b.category_id = c.category_id
            LEFT JOIN book_authors ba ON b.book_id = ba.book_id
            LEFT JOIN authors a ON ba.author_id = a.author_id
            GROUP BY b.book_id, b.title, b.isbn, b.category_id, c.category_name, b.total_copies, b.available_copies, b.created_at
            ORDER BY b.created_at DESC, b.book_id DESC
        ');

        foreach ($rows as &$row) {
            $row['author_id_list'] = !empty($row['author_id_list']) ? array_map('intval', explode(',', $row['author_id_list'])) : [];
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('adminGetBookCount')) {
    function adminGetBookCount(mysqli $conn): int
    {
        return fetchCount($conn, 'SELECT COUNT(*) AS total FROM books');
    }
}

if (!function_exists('adminGetLatestBookLabel')) {
    function adminGetLatestBookLabel(mysqli $conn): ?string
    {
        $row = fetchFirstRow($conn, 'SELECT title FROM books ORDER BY book_id DESC LIMIT 1');

        return $row['title'] ?? null;
    }
}