<?php
session_start();

if (isset($_SESSION['user_id'])) {

    echo 'Користувач залогінений. ID: ' . $_SESSION['user_id'];
} else {
    echo 'Користувач не залогінений';
}
?>
