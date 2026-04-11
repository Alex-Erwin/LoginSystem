<?php
session_start();
include "/home/are1046/PHP-Includes/dbh.inc";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Login Page</h1>

    <form action="index.php" method="post">
        <label for="uname">User Name:</label>
        <input type="text" id="uname" name="uname" value=""><br><br>

        <label for="psw">Password:</label>
        <input type="password" id="psw" name="psw" value=""><br><br>

        <input type="submit" name="login" value="Submit">
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>

<?php
if (isset($_POST["login"])) {
    if (!empty($_POST["uname"]) && !empty($_POST["psw"])) {

        $uname = $_POST['uname'];
        $psw   = $_POST['psw'];

        $conn = mysqli_connect($hostname, $user, $password, $database);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $stmt = $conn->prepare("SELECT id, username FROM accounts WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $uname, $psw);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION["username"]  = $uname;
            $_SESSION["passcode"]  = $psw;
            $_SESSION["logstatus"] = "TRUE";
            header("Location: welcome.php");
            exit;
        } else {
            echo "Invalid username or password.<br>";
        }

        $stmt->close();
        mysqli_close($conn);

    } else {
        echo "Missing username or password, fix it <br>";
    }
} else {
    echo "Waiting for your inputs";
}
?>
