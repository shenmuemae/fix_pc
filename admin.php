<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 1) {
    header("Location: index.php");
    exit();
}

global $conn;
include 'bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $requestId = $_POST['request_id'];
    $newStatus = $_POST['new_status'];

    $requestId = mysqli_real_escape_string($conn, $requestId);
    $newStatus = mysqli_real_escape_string($conn, $newStatus);

    $updateQuery = "UPDATE repair_requests SET timestamp = '$newStatus' WHERE id = '$requestId'";
    mysqli_query($conn, $updateQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_request'])) {
    $requestId = $_POST['request_id'];

    $requestId = mysqli_real_escape_string($conn, $requestId);

    $deleteQuery = "DELETE FROM repair_requests WHERE id = '$requestId'";
    mysqli_query($conn, $deleteQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_collection_status'])) {
    $collectionId = $_POST['collection_id'];
    $newCollectionStatus = $_POST['new_collection_status'];

    $collectionId = mysqli_real_escape_string($conn, $collectionId);
    $newCollectionStatus = mysqli_real_escape_string($conn, $newCollectionStatus);

    $updateCollectionQuery = "UPDATE collection_requests SET status = '$newCollectionStatus' WHERE id = '$collectionId'";
    mysqli_query($conn, $updateCollectionQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_collection_request'])) {
    $collectionId = $_POST['collection_id'];

    $collectionId = mysqli_real_escape_string($conn, $collectionId);

    $deleteCollectionQuery = "DELETE FROM collection_requests WHERE id = '$collectionId'";
    mysqli_query($conn, $deleteCollectionQuery);
}

$repairQuery = "SELECT * FROM repair_requests";
$repairResult = mysqli_query($conn, $repairQuery);

$collectionQuery = "SELECT * FROM collection_requests";
$collectionResult = mysqli_query($conn, $collectionQuery);
function addItem($tableName, $name, $price, $description) {
    global $conn;

    $name = mysqli_real_escape_string($conn, $name);
    $price = mysqli_real_escape_string($conn, $price);
    $description = mysqli_real_escape_string($conn, $description);

    $query = "INSERT INTO $tableName (name, price, description) VALUES ('$name', '$price', '$description')";
    mysqli_query($conn, $query);
}

function deleteItem($tableName, $itemId) {
    global $conn;

    $itemId = mysqli_real_escape_string($conn, $itemId);

    $checkUsageQuery = "SELECT COUNT(*) FROM collection_requests WHERE $tableName = '$itemId'";
    $checkUsageResult = mysqli_query($conn, $checkUsageQuery);

    if ($checkUsageResult) {
        $usageCount = mysqli_fetch_assoc($checkUsageResult)['COUNT(*)'];

        if ($usageCount > 0) {
            // Если запчасть используется, обновляем статус на Відмовлено
            $updateStatusQuery = "UPDATE collection_requests SET status = 'Denied' WHERE $tableName = '$itemId'";
            mysqli_query($conn, $updateStatusQuery);
        }
    }

    $query = "DELETE FROM $tableName WHERE id = '$itemId'";
    mysqli_query($conn, $query);
}

function getItems($tableName) {
    global $conn;

    $query = "SELECT * FROM $tableName";
    return mysqli_query($conn, $query);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $tableName = $_POST['table_name'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    addItem($tableName, $name, $price, $description);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $tableName = $_POST['table_name'];
    $itemId = $_POST['item_id'];

    deleteItem($tableName, $itemId);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адміністраторський інтерфейс</title>
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


        .status-select, .collection-status-select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .update-btn, .delete-btn {
            padding: 5px 10px;
            margin-top: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .delete-btn {
            background-color: #f44336;
        }

    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Панель адміністратора</h1>
        <nav>
            <div class="login-signup">
                <a class="logout-link" href="logout.php">Вихід</a>
            </div>
        </nav>
    </div>
</header>

<section class="main-section">
    <div class="container">

        <h3>Запити на ремонт</h3>
        <table>
            <thead>
            <tr>
                <th>Модель комп'ютера</th>
                <th>Опис проблеми</th>
                <th>Имя клиента</th>
                <th>Email</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($repairResult)) {
                ?>
                <tr>

                    <td><?php echo $row['computer_model']; ?></td>
                    <td><?php echo $row['issue_description']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['customer_email']; ?></td>
                    <td><?php echo $row['timestamp']; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <select name="new_status" class="status-select">
                                <option value="На розгляді" <?php echo ($row['timestamp'] === 'Pending') ? 'selected' : ''; ?>>На розгляді</option>
                                <option value="В процесі" <?php echo ($row['timestamp'] === 'In Progress') ? 'selected' : ''; ?>>В процесі</option>
                                <option value="Завершено" <?php echo ($row['timestamp'] === 'Completed') ? 'selected' : ''; ?>>Завершено</option>
                                <option value="Відмовлено" <?php echo ($row['timestamp'] === 'Denied') ? 'selected' : ''; ?>>Відмовлено</option>
                            </select>
                            <button type="submit" name="update_status" class="update-btn">Оновити статус</button>
                        </form>
                        <form method="post" action="">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_request" class="delete-btn">Видалити</button>
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>


        <h3>Запити на збірку</h3>
        <table>
            <thead>
            <tr>
                <th>Материнська плата</th>
                <th>Процесор</th>
                <th>Оперативна пам'ять</th>
                <th>Відеокарта</th>
                <th>Email</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($collectionResult)) {
                ?>
                <tr>
                    <td><?php echo $row['motherboard']; ?></td>
                    <td><?php echo $row['processor']; ?></td>
                    <td><?php echo $row['ram']; ?></td>
                    <td><?php echo $row['videocard']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="collection_id" value="<?php echo $row['id']; ?>">
                            <select name="new_collection_status" class="collection-status-select">
                                <option value="На розгляді" <?php echo ($row['status'] === 'Pending') ? 'selected' : ''; ?>>На розгляді</option>
                                <option value="В процесі" <?php echo ($row['status'] === 'In Progress') ? 'selected' : ''; ?>>В процесі</option>
                                <option value="Завершено" <?php echo ($row['status'] === 'Completed') ? 'selected' : ''; ?>>Завершено</option>
                                <option value="Відмовлено" <?php echo ($row['status'] === 'Denied') ? 'selected' : ''; ?>>Відмовлено</option>
                            </select>
                            <button type="submit" name="update_collection_status" class="update-btn">Оновити статус</button>
                        </form>
                        <form method="post" action="">
                            <input type="hidden" name="collection_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_collection_request" class="delete-btn">Видалити</button>
                        </form>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <h3>Материнська плата</h3>
    <table>
        <thead>
        <tr>

            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $motherboardResult = getItems('motherboard');
        while ($row = mysqli_fetch_assoc($motherboardResult)) {
            ?>
            <tr>

                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="table_name" value="motherboard">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_item">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <h3>Оперативна пам'ять</h3>
    <table>
        <thead>
        <tr>

            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ramResult = getItems('ram');
        while ($row = mysqli_fetch_assoc($ramResult)) {
            ?>
            <tr>

                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="table_name" value="ram">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_item">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <h3>Процесор</h3>
    <table>
        <thead>
        <tr>

            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $processorResult = getItems('processor');
        while ($row = mysqli_fetch_assoc($processorResult)) {
            ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="table_name" value="processor">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_item">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <h3>Відеокарта</h3>
    <table>
        <thead>
        <tr>

            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $videocardResult = getItems('videocard');
        while ($row = mysqli_fetch_assoc($videocardResult)) {
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="table_name" value="videocard">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_item">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

    <h3>Додати деталь</h3>
    <form class="repair-form" method="post" action="">
        <label  for="table_name">Виберіть тип:</label>
        <select name="table_name">
            <option value="motherboard">Материнська плата</option>
            <option value="ram">Оперативна пам'ять</option>
            <option value="processor">Процесор</option>
            <option value="videocard">Відеокарта</option>
        </select>
        <br>
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="price">Price:</label>
        <input type="text" name="price" required>
        <br>
        <label for="description">Description:</label>
        <input type="text" name="description" required>
        <br>
        <button type="submit" name="add_item">Додати деталь</button>
    </form>

</section>
</body>
</html>
