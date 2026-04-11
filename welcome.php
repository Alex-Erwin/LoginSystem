<?php
session_start();

if (!isset($_SESSION['logstatus'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Home Page</h1>

    <form action="welcome.php" method="post">
        <input type="submit" name="logout" value="Log OUT">
    </form>
</body>
</html>

<?php
echo $_SESSION["username"] . "<br>";
echo $_SESSION["passcode"] . "<br>";

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
