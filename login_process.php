<?php
global $conn;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('bd.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        $query = "SELECT username, password, email, user_role FROM users WHERE username=?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Помилка підготовки запиту: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($db_username, $db_password, $db_email, $user_role);

        if ($stmt->fetch()) {
            if ($email === $db_email && password_verify($password, $db_password)) {
                $_SESSION['username'] = $db_username;
                $_SESSION['email'] = $db_email;
                $_SESSION['user_role'] = $user_role;

                if ($user_role == 1) {
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $_SESSION['login_error'] = "Невірний пароль або електронна пошта.";
            }
        } else {
            $_SESSION['login_error'] = "Користувача з іменем '$username' не знайдено.";
        }

        $stmt->close();
    } else {
        $_SESSION['login_error'] = "Не всі обов'язкові поля були заповнені.";
    }
}

$conn->close();
?>
