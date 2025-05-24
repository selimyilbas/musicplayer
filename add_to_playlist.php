<?php

// This file handles adding songs to playlists

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

$song_search = $_POST['song_search'];
$playlist_id = $_POST['playlist_id'];

// Search for the song
$sql = "SELECT song_id, title FROM SONGS WHERE title LIKE '%$song_search%' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $song = mysqli_fetch_array($result);
    
    // Check if song already in playlist
    $check_sql = "SELECT playlistsong_id FROM PLAYLIST_SONGS 
                  WHERE playlist_id = $playlist_id AND song_id = " . $song['song_id'];
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) == 0) {
        // Get next ID
        $max_sql = "SELECT MAX(playlistsong_id) as max_id FROM PLAYLIST_SONGS";
        $max_result = mysqli_query($conn, $max_sql);
        $max_row = mysqli_fetch_array($max_result);
        $new_id = $max_row['max_id'] + 1;
        
        // Add song to playlist
        $insert_sql = "INSERT INTO PLAYLIST_SONGS (playlistsong_id, playlist_id, song_id, date_added) VALUES ";
        $insert_sql .= "($new_id, $playlist_id, " . $song['song_id'] . ", '" . date('Y-m-d') . "')";
        
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: playlistpage.php?id=" . $playlist_id . "&added=1");
        } else {
            echo "Error adding song: " . mysqli_error($conn);
        }
    } else {
        header("Location: playlistpage.php?id=" . $playlist_id . "&exists=1");
    }
} else {
    header("Location: playlistpage.php?id=" . $playlist_id . "&notfound=1");
}

mysqli_close($conn);
?>