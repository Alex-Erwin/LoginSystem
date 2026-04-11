<?php
session_start();
include "/home/are1046/PHP-Includes/dbh.inc";

$conn = mysqli_connect($hostname, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = trim(addslashes($_POST["uname"]));
$psw      = trim(addslashes($_POST["psw"]));

$sql = "SELECT * FROM accounts WHERE username='$username' AND password='$psw'";
$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) > 0) {
    $_SESSION['logstatus'] = "TRUE";
    $_SESSION['username']  = $username;
    header('Location: welcome.php');
    exit;
} else {
    echo "Invalid username or password.<br>";
}

mysqli_close($conn);
?>
