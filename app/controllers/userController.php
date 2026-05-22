<?php
session_start();
$root = dirname(__DIR__);
include_once($root . '/config/config.php');


if (isset($_POST['logoutButton'])) {
    unset($_SESSION['authUser']);
    unset($_SESSION['user_id']);
    unset($_SESSION['role_id']);
    session_destroy();
    header('Location: /librarySystem/public/login.php');
    exit();
}