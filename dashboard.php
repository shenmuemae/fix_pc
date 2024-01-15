<?php
global $conn;
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include('bd.php');

$username = $_SESSION['username'];

$repairQuery = "SELECT computer_model, issue_description, timestamp, id FROM repair_requests WHERE customer_name = ?";
$repairStmt = $conn->prepare($repairQuery);

if ($repairStmt) {
    $repairStmt->bind_param("s", $username);
    $repairStmt->execute();
    $repairStmt->bind_result($computerModel, $issueDescription, $timestamp, $repairRequestId);

    $repairRequests = [];
    while ($repairStmt->fetch()) {
        $repairRequests[] = [
            'computer_model' => $computerModel,
            'issue_description' => $issueDescription,
            'timestamp' => $timestamp,
            'id' => $repairRequestId,
        ];
    }

    $repairStmt->close();
} else {
    echo "Помилка підготовки запиту на ремонт: " . $conn->error;
}

$assemblyQuery = "SELECT processor, motherboard, ram, videocard, status, id FROM collection_requests WHERE username = ?";
$assemblyStmt = $conn->prepare($assemblyQuery);

if ($assemblyStmt) {
    $assemblyStmt->bind_param("s", $username);
    $assemblyStmt->execute();
    $assemblyStmt->bind_result($processor, $motherboard, $ram, $videoсard, $status, $assemblyRequestId);

    $assemblyRequests = [];
    while ($assemblyStmt->fetch()) {
        $assemblyRequests[] = [
            'processor' => $processor,
            'motherboard' => $motherboard,
            'ram' => $ram,
            'videocard' => $videoсard,
            'status' => $status,
            'id' => $assemblyRequestId,
        ];
    }

    $assemblyStmt->close();
} else {
    echo "Помилка підготовки запиту на збірку: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_repair_request'])) {
    $requestId = $_POST['repair_request_id'];

    $deleteRepairQuery = "DELETE FROM repair_requests WHERE id = ?";
    $deleteRepairStmt = $conn->prepare($deleteRepairQuery);

    if ($deleteRepairStmt) {
        $deleteRepairStmt->bind_param("i", $requestId);
        $deleteRepairStmt->execute();
        $deleteRepairStmt->close();

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Помилка підготовки запиту на видалення запиту на ремонт: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_assembly_request'])) {
    $requestId = $_POST['assembly_request_id'];

    $deleteAssemblyQuery = "DELETE FROM collection_requests WHERE id = ?";
    $deleteAssemblyStmt = $conn->prepare($deleteAssemblyQuery);

    if ($deleteAssemblyStmt) {
        $deleteAssemblyStmt->bind_param("i", $requestId);
        $deleteAssemblyStmt->execute();
        $deleteAssemblyStmt->close();

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Помилка підготовки запиту на видалення запиту на збірку: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles3.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 2px solid #00CED1;
            text-align: left;
            padding: 10px;
        }

        th {
            background-color: #e0ffff;


    </style>
</head>
<body>

<header>
    <header>
        <div class="container">
            <h1 >Центр зі збирання та ремонту комп'ютерів</h1>
            <nav>
                <a href="repair.php">Ремонт</a>
                <div class="services" id="servicesContainer">
                    <a href=collection.php>Збирання</a>
                </div>
                <a href="parts.php">Запчастини</a>
                <div class="login-signup">
                    <a class="logout-link" href="logout.php">Вихід</a>
                </div>



            </nav>
        </div>
    </header>
</header>

<section class="main-section">
    <div class="container">
        <h2>Ваш особистий кабінет</h2>

        <?php if (!empty($repairRequests)) : ?>
            <h3>Ваші запити на ремонт</h3>
            <table>

                <th>Модель комп'ютера</th>
                <th>Опис проблеми</th>
                <th>Статус</th>
                <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($repairRequests as $repairRequests) : ?>
                    <tr>
                        <td><?php echo $repairRequests['computer_model']; ?></td>
                        <td><?php echo $repairRequests['issue_description']; ?></td>
                        <td ><?php echo $repairRequests['timestamp']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="repair_request_id" value="<?php echo $repairRequests['id']; ?>">
                                <button type="submit" name="delete_repair_request">Видалити</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($assemblyRequests)) : ?>
            <h3>Ваші запити на збірку</h3>
            <table>
                <thead>
                <tr>

                    <th>Процесор</th>
                    <th>Материнська плата</th>
                    <th>Оперативна пам'ять</th>
                    <th>Відеокарта</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($assemblyRequests as $assemblyRequest) : ?>
                    <tr>
                        <td><?php echo $assemblyRequest['processor']; ?></td>
                        <td><?php echo $assemblyRequest['motherboard']; ?></td>
                        <td><?php echo $assemblyRequest['ram']; ?></td>
                        <td><?php echo $assemblyRequest['videocard']; ?></td>
                        <td><?php echo $assemblyRequest['status']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="assembly_request_id" value="<?php echo $assemblyRequest['id']; ?>">
                                <button type="submit" name="delete_assembly_request">Видалити</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</section>



</body>
</html>
