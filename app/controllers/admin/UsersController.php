<?php

require_once __DIR__ . '/../../models/admin/UsersModel.php';

if (!function_exists('adminUserRoleExists')) {
    function adminUserRoleExists(mysqli $conn, int $roleId): bool
    {
        return adminUserRoleExistsModel($conn, $roleId);
    }
}

if (!function_exists('adminGetUserDuplicateByEmailOrUsername')) {
    function adminGetUserDuplicateByEmailOrUsername(mysqli $conn, string $email, string $username): ?array
    {
        return adminGetUserDuplicateByEmailOrUsernameModel($conn, $email, $username);
    }
}

if (!function_exists('adminGetUserDuplicateByEmailOrUsernameExceptUuid')) {
    function adminGetUserDuplicateByEmailOrUsernameExceptUuid(mysqli $conn, string $email, string $username, string $userUuid): ?array
    {
        return adminGetUserDuplicateByEmailOrUsernameExceptUuidModel($conn, $email, $username, $userUuid);
    }
}

if (!function_exists('adminAddUser')) {
    function adminAddUser(mysqli $conn, string $uuid, string $firstName, string $lastName, string $email, string $username, string $hashedPassword, string $street, string $barangay, string $city, int $roleId, string $createdAt): bool
    {
        return adminAddUserModel($conn, $uuid, $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $createdAt);
    }
}

if (!function_exists('adminUpdateUserWithPassword')) {
    function adminUpdateUserWithPassword(mysqli $conn, string $firstName, string $lastName, string $email, string $username, string $hashedPassword, string $street, string $barangay, string $city, int $roleId, string $userUuid): bool
    {
        return adminUpdateUserWithPasswordModel($conn, $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $userUuid);
    }
}

if (!function_exists('adminUpdateUserWithoutPassword')) {
    function adminUpdateUserWithoutPassword(mysqli $conn, string $firstName, string $lastName, string $email, string $username, string $street, string $barangay, string $city, int $roleId, string $userUuid): bool
    {
        return adminUpdateUserWithoutPasswordModel($conn, $firstName, $lastName, $email, $username, $street, $barangay, $city, $roleId, $userUuid);
    }
}

if (!function_exists('adminGetUserIdByUuid')) {
    function adminGetUserIdByUuid(mysqli $conn, string $userUuid): int
    {
        return adminGetUserIdByUuidModel($conn, $userUuid);
    }
}

if (!function_exists('adminDeleteUserById')) {
    function adminDeleteUserById(mysqli $conn, int $userId): bool
    {
        return adminDeleteUserByIdModel($conn, $userId);
    }
}

if (!function_exists('adminGetUsers')) {
    function adminGetUsers(mysqli $conn): array
    {
        return adminGetUsersModel($conn);
    }
}

if (!function_exists('adminGetUserCount')) {
    function adminGetUserCount(mysqli $conn): int
    {
        return adminGetUserCountModel($conn);
    }
}

if (!function_exists('adminGetRoleCount')) {
    function adminGetRoleCount(mysqli $conn): int
    {
        return adminGetRoleCountModel($conn);
    }
}

if (!function_exists('adminGetRoles')) {
    function adminGetRoles(mysqli $conn): array
    {
        return adminGetRolesModel($conn);
    }
}

if (!function_exists('adminGetLatestSignupLabel')) {
    function adminGetLatestSignupLabel(mysqli $conn): string
    {
        return adminGetLatestSignupLabelModel($conn);
    }
}

if (!function_exists('adminHandleUserAction')) {
    function adminRedirectWithAddUserFormError(string $message, array $formData = []): void
    {
        $_SESSION['add_user_error'] = $message;
        $_SESSION['add_user_old'] = $formData;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    function adminGetUserDuplicateMessage(Throwable $exception): string
    {
        if ((int) $exception->getCode() !== 1062) {
            return 'Unable to save user right now.';
        }

        $errorMessage = strtolower($exception->getMessage());

        if (str_contains($errorMessage, 'email')) {
            return 'Email already exists.';
        }

        if (str_contains($errorMessage, 'username') || str_contains($errorMessage, 'user_name')) {
            return 'Username already exists.';
        }

        return 'Email or username already exists.';
    }

    function adminHandleUserAction(mysqli $conn): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_action'])) {
            return;
        }

        $action = $_POST['user_action'];

        if ($action === 'add') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $street = trim($_POST['street'] ?? '');
            $barangay = trim($_POST['barangay'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $roleId = (int) ($_POST['role_id'] ?? 0);

            if ($firstName === '' || $lastName === '' || $email === '' || $username === '' || $password === '' || $street === '' || $barangay === '' || $city === '' || $roleId <= 0) {
                redirectWithFlash('error', 'Please fill in all required user fields.');
            }

            if (!adminUserRoleExists($conn, $roleId)) {
                redirectWithFlash('error', 'Selected role does not exist.');
            }

            $existing = adminGetUserDuplicateByEmailOrUsername($conn, $email, $username);

            if ($existing !== null) {

                if (strcasecmp((string) ($existing['email'] ?? ''), $email) === 0) {
                    adminRedirectWithAddUserFormError(
                        'Email already exists.',
                        [
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $email,
                            'username' => $username,
                            'street' => $street,
                            'barangay' => $barangay,
                            'city' => $city,
                            'role_id' => $roleId,
                        ]
                    );
                }

                adminRedirectWithAddUserFormError(
                    'Username already exists.',
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'username' => $username,
                        'street' => $street,
                        'barangay' => $barangay,
                        'city' => $city,
                        'role_id' => $roleId,
                    ]
                );
            }

            $hashedPassword = $password;
            $uuid = generateUuidV4();
            $createdAt = date('Y-m-d H:i:s');

            try {
                if (adminAddUser($conn, $uuid, $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $createdAt)) {
                    redirectWithFlash('success', 'User added successfully.');
                }
            } catch (Throwable $exception) {
                adminRedirectWithAddUserFormError(
                    adminGetUserDuplicateMessage($exception),
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'username' => $username,
                        'street' => $street,
                        'barangay' => $barangay,
                        'city' => $city,
                        'role_id' => $roleId,
                    ]
                );
            }

            $errorMessage = $conn->errno === 1062 ? 'Email or username already exists.' : 'Unable to add user right now.';
            redirectWithFlash('error', $errorMessage);
        }

        if ($action === 'update') {
            $userUuid = trim($_POST['user_uuid'] ?? '');
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $street = trim($_POST['street'] ?? '');
            $barangay = trim($_POST['barangay'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $roleId = (int) ($_POST['role_id'] ?? 0);
            $currentSessionUserId = (int) ($_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 0);
            $targetUserId = $userUuid !== '' ? adminGetUserIdByUuid($conn, $userUuid) : 0;

            if ($userUuid === '' || $firstName === '' || $lastName === '' || $email === '' || $username === '' || $street === '' || $barangay === '' || $city === '' || $roleId <= 0) {
                redirectWithFlash('error', 'Please fill in all required user fields.');
            }

            if (!adminUserRoleExists($conn, $roleId)) {
                redirectWithFlash('error', 'Selected role does not exist.');
            }

            if ($currentSessionUserId > 0 && $currentSessionUserId === $targetUserId && $roleId !== 1) {
                redirectWithFlash('error', 'You cannot change your own role while logged in.');
            }

            $existing = adminGetUserDuplicateByEmailOrUsernameExceptUuid($conn, $email, $username, $userUuid);

            if ($existing !== null) {

                if (strcasecmp((string) ($existing['email'] ?? ''), $email) === 0) {
                    redirectWithFlash('error', 'Email already exists.');
                }

                redirectWithFlash('error', 'Username already exists.');
            }

            if ($password !== '') {
                $hashedPassword = $password;
                $updateResult = adminUpdateUserWithPassword($conn, $firstName, $lastName, $email, $username, $hashedPassword, $street, $barangay, $city, $roleId, $userUuid);
            } else {
                $updateResult = adminUpdateUserWithoutPassword($conn, $firstName, $lastName, $email, $username, $street, $barangay, $city, $roleId, $userUuid);
            }

            try {
                if ($updateResult) {
                    redirectWithFlash('success', 'User updated successfully.');
                }
            } catch (Throwable $exception) {
                redirectWithFlash('error', adminGetUserDuplicateMessage($exception));
            }

            $errorMessage = $conn->errno === 1062 ? 'Email or username already exists.' : 'Unable to update user right now.';
            redirectWithFlash('error', $errorMessage);
        }

        if ($action === 'delete') {
            $userId = intval($_POST['user_id'] ?? 0);
            $userUuid = trim($_POST['user_uuid'] ?? '');
            $currentSessionUserId = (int) ($_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 0);

            if ($userId <= 0 && $userUuid !== '') {
                $userId = adminGetUserIdByUuid($conn, $userUuid);
            }

            if ($userId <= 0) {
                redirectWithFlash('error', 'Invalid user selected for deletion.');
            }

            if ($currentSessionUserId > 0 && $currentSessionUserId === $userId) {
                redirectWithFlash('error', 'You cannot delete your own account while logged in.');
            }

            try {
                if (adminDeleteUserById($conn, $userId)) {
                    redirectWithFlash('success', 'User deleted successfully.');
                }

                redirectWithFlash('error', 'User was not deleted.');
            } catch (Throwable $exception) {
                $message = ((int) $exception->getCode() === 1451)
                    ? 'Cannot delete this user because there are related records.'
                    : 'Unable to delete user right now.';

                redirectWithFlash('error', $message);
            }
        }
    }
}
