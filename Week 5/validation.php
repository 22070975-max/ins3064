<?php
session_start();

/* Connect to database */
$con = mysqli_connect('localhost', 'root', '', 'LoginReg');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

/* Get form data */
$username = $_POST['user'];
$password = $_POST['password'];

/* Query the database */
$sql = "SELECT * FROM table1 WHERE username='$username' AND password='$password'";
$result = mysqli_query($con, $sql);

/* Check if user exists */
$num = mysqli_num_rows($result);

if ($num == 1) {
    $_SESSION['username'] = $username;
    header('Location: home.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>
