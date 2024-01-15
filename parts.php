<?php
global $conn;
include 'bd.php';

session_start();

function displayParts($tableName, $partName) {
    global $conn;
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);

    echo "<h3>$partName</h3>";

    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Назва</th><th>Ціна</th><th>Опис</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['name']}</td><td>{$row['price']} грн</td><td>{$row['description']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Немає доступних запчастин.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список запчастин</title>
    <link rel="stylesheet" href="styles3.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 2px solid #00CED1; /* Берюзова рамка */
            text-align: left;
            padding: 10px;
        }

        th {
            background-color: #e0ffff; /* Світло-берюзова заливка */
        }

        h3 {
            margin-top: 30px;
        }


    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Центр зі збирання та ремонту комп'ютерів</h1>
        <nav>
            <a href="repair.php">Ремонт</a>
            <div class="services" id="servicesContainer">
                <a href=collection.php>Збирання</a>
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
        <h2>Список запчастин</h2>

        <?php
        displayParts('motherboard', 'Материнські плати');
        displayParts('processor', 'Процесори');
        displayParts('ram', 'Оперативна память');
        displayParts('videocard', 'Відеокарти');
        ?>
    </div>
</section>



</body>
</html>

<?php
$conn->close();
?>
