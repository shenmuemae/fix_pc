<?php
global $conn;
include 'bd.php';
session_start();

$sqlProcessor = "SELECT * FROM processor";
$sqlMotherboard = "SELECT * FROM motherboard";
$sqlRAM = "SELECT * FROM ram";
$sqlVideoCard = "SELECT * FROM videocard";

$resultProcessor = $conn->query($sqlProcessor);
$resultMotherboard = $conn->query($sqlMotherboard);
$resultRAM = $conn->query($sqlRAM);
$resultVideoCard = $conn->query($sqlVideoCard);

function printOptions($result, $columnName) {
    echo "<option value=''> </option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row[$columnName]}'>{$row[$columnName]}</option>";
    }
}

$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['username'])) {
        $selectedProcessor = $_POST["processor"];
        $selectedMotherboard = $_POST["motherboard"];
        $selectedRAM = $_POST["ram"];
        $selectedVideoCard = $_POST["videoCard"];

        $username = $_SESSION['username'];
        $email = $_SESSION['email'];


        $status = "на розгляді";

        $sqlInsert = "INSERT INTO collection_requests (username, email, processor, motherboard, ram, videocard, status)
                      VALUES ('$username', '$email', '$selectedProcessor', '$selectedMotherboard', '$selectedRAM', '$selectedVideoCard', '$status')";

        if ($conn->query($sqlInsert) === TRUE) {
            $successMessage = "Дані успішно збережено";
        } else {
            echo "Помилка: " . $sqlInsert . "<br>" . $conn->error;
        }
    } else {
        $successMessage = "Для збереження данних вам необхідно увійти в систему";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Збірка комп'ютера</title>
    <link rel="stylesheet" href="styles3.css">

    <style>
        form {
            max-width: 300px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }


        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }


        .success-message {
            margin-top: 20px;
            color: #000000;
        }
    </style>
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

<section class="main-section" >
    <div class="container" >
        <?php if (!empty($successMessage)) : ?>
            <h2 class="success-message"><?php echo $successMessage; ?></h2>
        <?php else : ?>
            <h2>Виберіть запчастини для складання комп'ютера:</h2>
            <form class="repair-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="processor">Процесор:</label>
                <select name="processor" required>
                    <?php printOptions($resultProcessor, "name"); ?>
                </select>

                <label for="motherboard">Материнська плата:</label>
                <select name="motherboard" required>
                    <?php printOptions($resultMotherboard, "name"); ?>
                </select>

                <label for="ram">Оперативна пам'ять:</label>
                <select name="ram" required>
                    <?php printOptions($resultRAM, "name"); ?>
                </select>

                <label for="videoCard">Відеокарта:</label>
                <select name="videoCard" required>
                    <?php printOptions($resultVideoCard, "name"); ?>
                </select>

                <br><br>

                <input type="submit" value="Відправити">
            </form>
        <?php endif; ?>
    </div>
</section>


</body>
</html>
