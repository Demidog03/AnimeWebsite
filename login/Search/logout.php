<?php
session_start();

if(isset($_SESSION['user_id'])){
    unset($_SESSION['user_id']);
}

header("Location: login.php");
die;

<?php
    session_start();
    unset($_SESSION['loggedIn']);
    session_destroy();
    header('Location: index.php');
    exit();
?>
