<?php

// This file creates a new playlist

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

$title = $_GET['title'];
$user_id = $_SESSION['user_id'];
$date_created = date('Y-m-d');
$description = "Created by user on " . $date_created;

// Get next playlist ID
$sql = "SELECT MAX(playlist_id) as max_id FROM PLAYLISTS";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$new_id = $row['max_id'] + 1;

// Insert new playlist
$sql = "INSERT INTO PLAYLISTS (playlist_id, user_id, title, description, date_created, image) VALUES ";
$sql .= "($new_id, $user_id, '$title', '$description', '$date_created', 'playlist$new_id.jpg')";

if (mysqli_query($conn, $sql)) {
    header("Location: playlistpage.php?id=" . $new_id);
} else {
    echo "Error creating playlist: " . mysqli_error($conn) . "<br>";
    echo "<a href='homepage.php'>Go back</a>";
}

mysqli_close($conn);
?>