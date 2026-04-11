<?php
session_start();
include "/home/are1046/PHP-Includes/dbh.inc";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Registration Page</h1>

    <form action="register.php" method="post">
        <label for="uname">Username:</label>
        <input type="text" id="uname" name="uname" value=""><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value=""><br><br>

        <label for="psw">Password:</label>
        <input type="password" id="psw" name="psw" value=""><br><br>

        <label for="psw2">Re-enter Password:</label>
        <input type="password" id="psw2" name="psw2" value=""><br><br>

        <input type="submit" name="register" value="Register">
    </form>

    <p>Already have an account? <a href="index.php">Login here</a></p>

</body>
</html>

<?php
$conn = mysqli_connect($hostname, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["register"])) {
    if (!empty($_POST["uname"]) && !empty($_POST["email"]) && !empty($_POST["psw"]) && !empty($_POST["psw2"])) {
        if ($_POST["psw"] == $_POST["psw2"]) {
            $username = trim(addslashes($_POST["uname"]));
            $email    = trim(addslashes($_POST["email"]));
            $psw      = trim(addslashes($_POST["psw"]));

            $sql = "INSERT INTO accounts (username, password, email) VALUES ('$username', '$psw', '$email')";
            mysqli_query($conn, $sql);
            echo "User registered successfully! <a href='index.php'>Login here</a>";
        } else {
            echo "Both passwords don't match. Please try again.<br>";
        }
    } else {
        echo "Missing fields - please fill in all fields.<br>";
    }
} else {
    echo "Waiting for entry...";
}

mysqli_close($conn);
?>
