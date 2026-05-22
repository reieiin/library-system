<?php
session_start();
$root = dirname(__DIR__);
include_once($root . '/config/config.php');
include_once($root . '/helpers/admin.php');
include_once($root . '/models/AuthModel.php');

if (isset($_POST['loginButton'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = userGetLoginUser($conn, $username, $password);

    if ($stmt) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);

            $user_id = $data['user_id'];
            $fullname = $data['first_name'] . ' ' . $data['last_name'];
            $username = $data['username'];
            $userRole = $data['role_id'];

            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $userRole;
            $_SESSION['authUser'] = [
                'user_id' => $user_id,
                'fullname' => $fullname,
                'username' => $username
            ];

            $_SESSION['message'] = 'Welcome, ' . $fullname . '!';
            $_SESSION['code'] = 'success';

            if ($userRole == 1) {
                header('Location: /librarySystem/public/admin/index.php');
                exit();

            }
            if ($userRole == 2) {
                header('Location: /librarySystem/public/user/index.php');
                exit();
            }

        } else {
            $_SESSION['message'] = 'Invalid username or password.';
            $_SESSION['code'] = 'error';
            header('Location: /librarySystem/public/login.php');
            exit();
        }

    } else {
        $_SESSION['message'] = 'Something went wrong. Please try again.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/login.php');
        exit();
    }


}

if (isset($_POST['registerButton'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];

    $uuid = generateUuidV4(); // Function to generate a unique UUID for the user

    //Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = 'Invalid email format.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/registration');
        exit();
    }

    // Check email already exists
    if (userEmailExists($conn, $email)) {
        $_SESSION['message'] = 'Email already exists. Please use a different email.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/registration');
        exit();
    }

    //Check username already exists
    if (userUsernameExists($conn, $username)) {
        $_SESSION['message'] = 'Username already exists. Please choose a different username.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/registration');
        exit();
    }

    // Check password confirmation
    if ($password !== $confirm_password) {
        $_SESSION['message'] = 'Passwords do not match. Please try again.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/registration');
        exit();
    }

    // Insert new user into database;
    if (userRegisterAccount($conn, $uuid, $first_name, $last_name, $email, $username, $password, $street, $barangay, $city)) {
        $_SESSION['message'] = 'Registration successful. You can now log in.';
        $_SESSION['code'] = 'success';
        header('Location: /librarySystem/public/login.php');
        exit();
    } else {
        $_SESSION['message'] = 'Something went wrong. Please try again.';
        $_SESSION['code'] = 'error';
        header('Location: /librarySystem/public/registration');
        exit();
    }
}