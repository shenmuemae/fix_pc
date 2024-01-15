<?php
global $conn;
include 'bd.php';
session_start();

if (!isset($_SESSION['username'])) {
    $errorMessage = "Увійти в систему для оформмлення заявки";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $computerModel = $_POST["computerModel"];
    $issueDescription = $_POST["issueDescription"];

    $username = $_SESSION['username'];
    $email = $_SESSION['email'];

    $computerModel = mysqli_real_escape_string($conn, $computerModel);
    $issueDescription = mysqli_real_escape_string($conn, $issueDescription);
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);

    $sql = "INSERT INTO repair_requests (computer_model, issue_description, customer_name, customer_email)
            VALUES ('$computerModel', '$issueDescription', '$username', '$email')";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "Дані успішно збережено";
    } else {
        $errorMessage = "Помилка: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма ремонту комп'ютера</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>

<header>
    <div class="container">
        <h1><a href="parts.php" style="  color: #ffffff; text-decoration: none;">Центр зі збирання та ремонту комп'ютерів</a></h1>
        <nav>
            <a href="repair.php">Ремонт</a>
            <div class="services" id="servicesContainer">
                <a href="collection.php">Збирання</a>
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
        <?php
        if (isset($successMessage)) {
            echo '<h2>' . $successMessage . '</h2>';
        } elseif (isset($errorMessage)) {
            echo '<h2>' . $errorMessage . '</h2>';
        } else {
            ?>

            <h2>Форма ремонту комп'ютера</h2>
            <form class="repair-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="computerModel">Модель комп'ютера:</label>
                <input type="text" name="computerModel" required>

                <label for="issueDescription">Опис проблеми:</label>
                <input name="issueDescription" rows="10" cols="51" required>

                <input type="submit" value="Відправити">
            </form>
        <?php } ?>
    </div>
</section>


</body>
</html>
