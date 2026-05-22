<?php

if (!function_exists('adminUserRoleExistsModel')) {
    function adminUserRoleExistsModel(mysqli $conn, int $roleId): bool
    {
        $roleCheck = $conn->prepare('SELECT role_id FROM roles WHERE role_id = ? LIMIT 1');
        $roleCheck->bind_param('i', $roleId);
        $roleCheck->execute();
        $roleResult = $roleCheck->get_result();
        $exists = $roleResult->num_rows > 0;
        $roleCheck->close();

        return $exists;
    }
}

if (!function_exists('adminGetUserDuplicateByEmailOrUsernameModel')) {
    function adminGetUserDuplicateByEmailOrUsernameModel(mysqli $conn, string $email, string $username): ?array
    {
        $duplicateCheck = $conn->prepare('SELECT email, username FROM users WHERE email = ? OR username = ? LIMIT 1');
        $duplicateCheck->bind_param('ss', $email, $username);
        $duplicateCheck->execute();
        $duplicateResult = $duplicateCheck->get_result();

        if ($duplicateResult->num_rows > 0) {
            $existing = $duplicateResult->fetch_assoc();
            $duplicateCheck->close();

            return $existing;
        }

        $duplicateCheck->close();

        return null;
    }
}

if (!function_exists('adminGetUserDuplicateByEmailOrUsernameExceptUuidModel')) {
    function adminGetUserDuplicateByEmailOrUsernameExceptUuidModel(mysqli $conn, string $email, string $username, string $userUuid): ?array
    {
        $duplicateCheck = $conn->prepare('SELECT email, username FROM users WHERE (email = ? OR username = ?) AND uuid <> ? LIMIT 1');
        $duplicateCheck->bind_param('sss', $email, $username, $userUuid);
        $duplicateCheck->execute();
        $duplicateResult = $duplicateCheck->get_result();

        if ($duplicateResult->num_rows > 0) {
            $existing = $duplicateResult->fetch_assoc();
            $duplicateCheck->close();

            return $existing;
        }

        $duplicateCheck->close();

        return null;
    }
}

if (!function_exists('adminAddUserModel')) {
    function adminAddUserModel(mysqli $conn, string $uuid, string $firstName, string $lastName, string $email, string $username, string $hashedPassword, string $street, string $barangay, string $city, int $roleId, string $createdAt): bool
    {
        $stmt = $conn->prepare('INSERT INTO users (uuid, first_name, last_name, email, username, password, street, barangay, city, role_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssis', $uuid, $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $createdAt);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminUpdateUserWithPasswordModel')) {
    function adminUpdateUserWithPasswordModel(mysqli $conn, string $firstName, string $lastName, string $email, string $username, string $hashedPassword, string $street, string $barangay, string $city, int $roleId, string $userUuid): bool
    {
        $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, username = ?, password = ?, street = ?, barangay = ?, city = ?, role_id = ? WHERE uuid = ?');
        $stmt->bind_param('ssssssssis', $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $userUuid);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminUpdateUserWithoutPasswordModel')) {
    function adminUpdateUserWithoutPasswordModel(mysqli $conn, string $firstName, string $lastName, string $email, string $username, string $street, string $barangay, string $city, int $roleId, string $userUuid): bool
    {
        $stmt = $conn->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, username = ?, street = ?, barangay = ?, city = ?, role_id = ? WHERE uuid = ?');
        $stmt->bind_param('sssssssis', $firstName, $lastName, $email, $username, $street, $barangay, $city, $roleId, $userUuid);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminGetUserIdByUuidModel')) {
    function adminGetUserIdByUuidModel(mysqli $conn, string $userUuid): int
    {
        $lookupStmt = $conn->prepare('SELECT user_id FROM users WHERE uuid = ? LIMIT 1');
        $lookupStmt->bind_param('s', $userUuid);
        $lookupStmt->execute();
        $lookupResult = $lookupStmt->get_result();

        if ($lookupResult->num_rows > 0) {
            $foundUser = $lookupResult->fetch_assoc();
            $lookupStmt->close();

            return (int) ($foundUser['user_id'] ?? 0);
        }

        $lookupStmt->close();

        return 0;
    }
}

if (!function_exists('adminDeleteUserByIdModel')) {
    function adminDeleteUserByIdModel(mysqli $conn, int $userId): bool
    {
        $stmt = $conn->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->bind_param('i', $userId);
        $result = $stmt->execute() && $stmt->affected_rows > 0;
        $stmt->close();

        return $result;
    }
}

if (!function_exists('adminGetUsersModel')) {
    function adminGetUsersModel(mysqli $conn): array
    {
        return fetchAllRows($conn, '
            SELECT
                u.user_id,
                u.uuid,
                u.first_name,
                u.last_name,
                u.email,
                u.username,
                u.street,
                u.barangay,
                u.city,
                u.role_id,
                r.role_name,
                u.created_at
            FROM users u
            INNER JOIN roles r ON u.role_id = r.role_id
            ORDER BY u.created_at DESC, u.user_id DESC
        ');
    }
}

if (!function_exists('adminGetUserCountModel')) {
    function adminGetUserCountModel(mysqli $conn): int
    {
        return fetchCount($conn, 'SELECT COUNT(*) AS total FROM users');
    }
}

if (!function_exists('adminGetRoleCountModel')) {
    function adminGetRoleCountModel(mysqli $conn): int
    {
        return fetchCount($conn, 'SELECT COUNT(*) AS total FROM roles');
    }
}

if (!function_exists('adminGetRolesModel')) {
    function adminGetRolesModel(mysqli $conn): array
    {
        return fetchAllRows($conn, 'SELECT role_id, role_name FROM roles ORDER BY role_name ASC');
    }
}

if (!function_exists('adminGetLatestSignupLabelModel')) {
    function adminGetLatestSignupLabelModel(mysqli $conn): string
    {
        $row = fetchFirstRow($conn, 'SELECT created_at FROM users ORDER BY created_at DESC, user_id DESC LIMIT 1');

        if (!$row) {
            return 'No users yet';
        }

        return date('M d, Y', strtotime($row['created_at']));
    }
}