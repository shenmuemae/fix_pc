<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Центр зі збирання та ремонту комп'ютерів</title>
    <link rel="stylesheet" href="styles3.css">
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
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo '<a class="signup-link" href="profile.php">Особистий кабінет</a>';
                } else {
                    echo '<a class="signup-link" href="index.php">Зареєструватися</a>';
                }
                ?>
            </div>
        </nav>
    </div>
</header>

<section class="main-section">
    <div class="container">
        <h2>Увійти</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include('login_process.php');
        }
        if (isset($_SESSION['login_error'])) {
            echo '<p class="error-message">' . $_SESSION['login_error'] . '</p>';
            unset($_SESSION['login_error']);
        }
        ?>
        <form class="repair-form" action="login.php" method="post">
            <label for="username">Ім'я користувача:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Пошта:</label>
            <input type="text" name="email" id="email" required>

            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Увійти">
        </form>
        <p>Не маєте облікового запису? <a href="index.php">Зареєструйтеся</a></p>
    </div>
</section>


</body>
</html>
