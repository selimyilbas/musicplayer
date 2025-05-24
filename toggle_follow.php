<?php

// This file handles following/unfollowing artists

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "mahmut_selim_yilbas"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$artist_id = $_GET['artist_id'];
$user_id = $_SESSION['user_id'];

// Get current most played artist
$sql = "SELECT most_played_artist FROM USERS WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

$current_artist = $row['most_played_artist'];
$target_artist = 'Artist ' . $artist_id;

// Toggle follow status
if ($current_artist == $target_artist) {
    // Unfollow
    $update_sql = "UPDATE USERS SET most_played_artist = NULL WHERE user_id = $user_id";
} else {
    // Follow
    $update_sql = "UPDATE USERS SET most_played_artist = '$target_artist' WHERE user_id = $user_id";
}

mysqli_query($conn, $update_sql);
mysqli_close($conn);

header("Location: artistpage.php?id=" . $artist_id);
?>