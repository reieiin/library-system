<?php

if (!function_exists('userGetLoginUser')) {
    function userGetLoginUser(mysqli $conn, string $username, string $password): mysqli_stmt|false
    {
        $loginQuery = "SELECT `user_id`, `first_name`, `last_name`, `email`, `username`, `password`, `role_id` FROM `users` WHERE username = ? AND password = ? LIMIT 1";
        $stmt = $conn->prepare($loginQuery);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            mysqli_stmt_execute($stmt);
        }

        return $stmt ?: false;
    }
}

if (!function_exists('userEmailExists')) {
    function userEmailExists(mysqli $conn, string $email): bool
    {
        $checkEmail = mysqli_query($conn, "SELECT `email` FROM `users` WHERE email = '$email' LIMIT 1");

        return $checkEmail && mysqli_num_rows($checkEmail) > 0;
    }
}

if (!function_exists('userUsernameExists')) {
    function userUsernameExists(mysqli $conn, string $username): bool
    {
        $checkUsername = mysqli_query($conn, "SELECT `username` FROM `users` WHERE username = '$username' LIMIT 1");

        return $checkUsername && mysqli_num_rows($checkUsername) > 0;
    }
}

if (!function_exists('userRegisterAccount')) {
    function userRegisterAccount(mysqli $conn, string $uuid, string $firstName, string $lastName, string $email, string $username, string $password, string $street, string $barangay, string $city): bool
    {
        $query = "INSERT INTO `users`
(`uuid`, `first_name`, `last_name`, `email`, `username`, `password`, `street`, `barangay`, `city`, `role_id`)
VALUES
('$uuid', '$firstName', '$lastName', '$email', '$username', '$password', '$street', '$barangay', '$city', 2)";

        return mysqli_query($conn, $query);
    }
}