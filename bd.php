<?php

$conn = new mysqli("localhost", "root", "", "prod1");

if ($conn->connect_error) {
    die("Помилка з'єднання: " . $conn->connect_error);
}

