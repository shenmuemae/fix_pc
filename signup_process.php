<?php
session_start();
global $conn;
include('bd.php');

$username = $_POST['username'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$email = $_POST['email'];

$minLength = 6;
if (strlen($username) < $minLength || strlen($password) < $minLength) {
    $_SESSION['message'] = 'Мінімальна довжина імені та пароля - ' . $minLength . ' символи';
    header('Location: index.php');
    exit();
}

if ($password === $password_confirm) {
    $checkQuery = "SELECT id FROM `users` WHERE `username` = '$username' OR `email` = '$email'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = 'Користувач з таким ім\'ям або електронною поштою вже існує';
        header('Location: index.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $insertQuery = "INSERT INTO `users` (`username`, `password`, `email`) VALUES ('$username', '$hashedPassword', '$email')";
    mysqli_query($conn, $insertQuery);

    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    header("Location: dashboard.php");
    exit();
} else {
    $_SESSION['message'] = 'Паролі не співпадають';
    header('Location: index.php');
    exit();
}
?>
