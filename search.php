<?php

// This file handles search queries from homepage

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

$search_query = $_POST['search_query'];
$search_type = $_POST['search_type'];

if ($search_type == 'playlist_song') {
    // Search for playlist
    $sql = "SELECT playlist_id FROM PLAYLISTS WHERE title LIKE '%$search_query%' AND user_id = " . $_SESSION['user_id'] . " LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        header("Location: playlistpage.php?id=" . $row['playlist_id']);
    } else {
        // Search for song
        $sql = "SELECT song_id FROM SONGS WHERE title LIKE '%$search_query%' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            header("Location: currentmusic.php?id=" . $row['song_id']);
        } else {
            echo "No results found.<br>";
            echo "<a href='homepage.php'>Go back</a>";
        }
    }
} else if ($search_type == 'song') {
    // Search for song to play
    $sql = "SELECT song_id FROM SONGS WHERE title LIKE '%$search_query%' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        
        // Add to play history
        $history_sql = "INSERT INTO PLAY_HISTORY (play_id, user_id, song_id, playtime) VALUES ";
        $history_sql .= "((SELECT MAX(play_id) + 1 FROM PLAY_HISTORY AS ph2), " . $_SESSION['user_id'] . ", " . $row['song_id'] . ", '" . date('Y-m-d') . "')";
        mysqli_query($conn, $history_sql);
        
        header("Location: currentmusic.php?id=" . $row['song_id']);
    } else {
        echo "Song not found.<br>";
        echo "<a href='homepage.php'>Go back</a>";
    }
} else if ($search_type == 'artist') {
    // Search for artist
    $sql = "SELECT artist_id FROM ARTISTS WHERE name LIKE '%$search_query%' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        header("Location: artistpage.php?id=" . $row['artist_id']);
    } else {
        echo "Artist not found.<br>";
        echo "<a href='homepage.php'>Go back</a>";
    }
}

mysqli_close($conn);
?>