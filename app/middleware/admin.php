<?php
session_start();
$root = dirname(__DIR__);
include_once($root . '/config/config.php');
include_once($root . '/helpers/admin.php');

if (!isset($_SESSION['authUser'])){
    $_SESSION['message'] = 'You must be logged in to access this page.';
    $_SESSION['code'] = 'warning';
    header('Location: /librarySystem/public/login.php');
    exit();

} else {
    $sessionUserId = (int) ($_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 0);
    $sessionRoleId = (int) ($_SESSION['role_id'] ?? 0);

    if (!authSessionUserExistsWithRole($conn, $sessionUserId, $sessionRoleId)) {
        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['message'] = 'You must be logged in to access this page.';
        $_SESSION['code'] = 'warning';
        header('Location: /librarySystem/public/login.php');
        exit();
    }

    if ($_SESSION['role_id'] !== 1) {
        $_SESSION['message'] = 'Access denied. Admins only.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/user/index');
        exit();
    }
}
?>