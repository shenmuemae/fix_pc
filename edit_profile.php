<?php
global $conn;
session_start();
include 'bd.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT id, username, email FROM users WHERE username=?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userId, $dbUsername, $dbEmail);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];
    $newPassword = $_POST['new_password'];

    if (!empty($newUsername) && !empty($newEmail)) {
        $updateQuery = "UPDATE users SET username=?, email=? WHERE id=?";
        $updateStmt = $conn->prepare($updateQuery);

        if (!$updateStmt) {
            die("Error preparing update query: " . $conn->error);
        }

        $updateStmt->bind_param("ssi", $newUsername, $newEmail, $userId);
        $updateStmt->execute();
        $updateStmt->close();

        $_SESSION['username'] = $newUsername;

        $_SESSION['message'] = 'Profile updated successfully';
    } else {
        $_SESSION['message'] = 'Username and email are required';
    }

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $updatePasswordQuery = "UPDATE users SET password=? WHERE id=?";
        $updatePasswordStmt = $conn->prepare($updatePasswordQuery);

        if (!$updatePasswordStmt) {
            die("Error preparing password update query: " . $conn->error);
        }

        $updatePasswordStmt->bind_param("si", $hashedPassword, $userId);
        $updatePasswordStmt->execute();
        $updatePasswordStmt->close();

        $_SESSION['message'] .= ' Password updated successfully';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>

<header>
    <div class="container">
        <h1><a href="parts.php" style="color: #fff; text-decoration: none;">Центр зі збирання та ремонту комп'ютерів</a></h1>
        <nav>
            <a href="repair.php">Ремонт</a>
            <div class="services" id="servicesContainer">
                <a href="collection.php" id="servicesBtn">Збирання</a>
            </div>
            <a href="parts.php">Запчастини</a>
            <div class="login-signup">
                <?php if(isset($_SESSION['username'])) : ?>
                    <a class="profile-link" href="dashboard.php">Особистий кабінет</a>
                    <a class="logout-link" href="logout.php">Вихід</a>
                <?php else : ?>
                    <a class="login-link" href="login.php">Увійти</a>
                    <a class="signup-link" href="index.php">Зареєструватися</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<section class="main-section">
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (isset($_SESSION['message'])) : ?>
            <p class="msg"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="post" action="">
            <label for="new_username">New Username:</label>
            <input type="text" name="new_username" value="<?php echo $dbUsername; ?>" required>
            <br>

            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" value="<?php echo $dbEmail; ?>" required>
            <br>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password">
            <br>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</section>

</body>
</html>
