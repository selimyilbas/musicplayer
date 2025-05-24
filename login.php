<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "mahmut_selim_yilbas";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = trim($_POST['username']);
$pass = trim($_POST['password']);

// Format kontrolü
if (empty($user) || empty($pass)) {
    $_SESSION['login_error'] = "Please fill in both username and password.";
    header("Location: login.html");
    exit();
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $user)) {
    $_SESSION['login_error'] = "Username can only contain letters, numbers, and underscores.";
    header("Location: login.html");
    exit();
}

$user_safe = mysqli_real_escape_string($conn, $user);
$pass_safe = mysqli_real_escape_string($conn, $pass);

$sql = "SELECT user_id, name, username FROM USERS WHERE username = '$user_safe' AND password = '$pass_safe'";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['username'] = $row['username'];

    // Clear any old error
    unset($_SESSION['login_error']);

    $update_sql = "UPDATE USERS SET last_login = '" . date('Y-m-d') . "' WHERE user_id = " . $row['user_id'];
    mysqli_query($conn, $update_sql);

    header("Location: homepage.php");
    exit();
} else {
    $_SESSION['login_error'] = "Incorrect username or password.";
    header("Location: login.html");
    exit();
}

mysqli_close($conn);
?>
