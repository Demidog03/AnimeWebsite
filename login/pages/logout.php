<?php
    session_start();
    unset($_SESSION['loggedIn']);
    session_destroy();
    header('Location: index1.php');
    exit();
?>
