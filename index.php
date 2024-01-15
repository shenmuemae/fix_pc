<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Центр зі збирання та ремонту комп'ютерів</title>
    <link rel="stylesheet" href="styles3.css">

</head>
<body>

<header>
    <div class="container">
        <h1><a href="index.php" style="color: #fff; text-decoration: none;">Центр зі збирання та ремонту комп'ютерів</a></h1>
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
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>


<section class="main-section">
    <div class="container">
        <h2>Реєстрація</h2>
        <form class="repair-form" method="post" action="signup_process.php">
            <label for="username">Ім'я користувача:</label>
            <input type="text" name="username" id="username" required> <br>
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" required> <br>
            <label for="password">Підтвердіть пароль:</label>
            <input type="password" name="password_confirm" id="password" required> <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required> <br>
            <input type="submit" value="Зареєструватися">
        </form>

        <p>Вже маєте обліковий запис? <a href="login.php">Увійдіть</a></p>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="msg">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']); // Clear the message after displaying it
        }
        ?>

    </div>
</section>


</body>
</html>
