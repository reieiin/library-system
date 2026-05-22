<?php

include_once(__DIR__ . '/logger.php');
include_once(__DIR__ . '/ui.php');

if (!function_exists('redirectWithFlash')) {
    function redirectWithFlash(string $icon, string $message): void
    {
        $_SESSION['code'] = $icon;
        $_SESSION['message'] = $message;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (!function_exists('generateUuidV4')) {
    function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('fetchAllRows')) {
    function fetchAllRows(mysqli $conn, string $query): array
    {
        $result = $conn->query($query);
        $rows = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}

if (!function_exists('fetchFirstRow')) {
    function fetchFirstRow(mysqli $conn, string $query): ?array
    {
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }
}

if (!function_exists('fetchCount')) {
    function fetchCount(mysqli $conn, string $query): int
    {
        $row = fetchFirstRow($conn, $query);

        return $row ? (int) ($row['total'] ?? 0) : 0;
    }
}

if (!function_exists('authSessionUserExistsWithRole')) {
    function authSessionUserExistsWithRole(mysqli $conn, int $userId, int $roleId): bool
    {
        if ($userId <= 0 || $roleId <= 0) {
            return false;
        }

        $stmt = $conn->prepare('SELECT user_id FROM users WHERE user_id = ? AND role_id = ? LIMIT 1');

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ii', $userId, $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }
}