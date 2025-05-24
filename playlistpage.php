<?php
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

$playlist_id = $_GET['id'];

// Get playlist info
$playlist_sql = "SELECT p.title, p.description, u.name as owner_name 
                 FROM PLAYLISTS p 
                 INNER JOIN USERS u ON p.user_id = u.user_id 
                 WHERE p.playlist_id = $playlist_id";
$playlist_result = mysqli_query($conn, $playlist_sql);
$playlist_info = mysqli_fetch_array($playlist_result);

// Get songs in playlist (with image)
$songs_sql = "SELECT s.song_id, s.title as song_title, s.duration, s.image,
                     a.name as artist_name, c.country_name, al.name as album_name
              FROM PLAYLIST_SONGS ps
              INNER JOIN SONGS s ON ps.song_id = s.song_id
              INNER JOIN ALBUMS al ON s.album_id = al.album_id
              INNER JOIN ARTISTS a ON al.artist_id = a.artist_id
              INNER JOIN COUNTRY c ON a.country_id = c.country_id
              WHERE ps.playlist_id = $playlist_id
              ORDER BY ps.date_added DESC";
$songs_result = mysqli_query($conn, $songs_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $playlist_info['title']; ?> - Music Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #121212;
            color: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        .back-link {
            color: #1db954;
            text-decoration: none;
            font-size: 16px;
        }
        .playlist-info {
            margin-bottom: 30px;
        }
        h1 {
            margin: 0 0 10px 0;
        }
        .description {
            color: #b3b3b3;
            margin-bottom: 5px;
        }
        .owner {
            color: #b3b3b3;
            font-size: 14px;
        }
        .search-section {
            margin-bottom: 30px;
        }
        .search-bar {
            width: 100%;
            max-width: 500px;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .songs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .songs-table th {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #333;
            color: #b3b3b3;
        }
        .songs-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #282828;
            vertical-align: middle;
        }
        .songs-table tr:hover {
            background-color: #282828;
            cursor: pointer;
        }
        .song-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
        }
        .song-details {
            display: flex;
            align-items: center;
        }
        .song-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .song-artist {
            color: #b3b3b3;
            font-size: 14px;
        }
        .duration {
            color: #b3b3b3;
        }
        .country {
            color: #1db954;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="homepage.php" class="back-link">← Back to Home</a>
    </div>

    <div class="playlist-info">
        <h1><?php echo $playlist_info['title']; ?></h1>
        <div class="description"><?php echo $playlist_info['description']; ?></div>
        <div class="owner">Created by <?php echo $playlist_info['owner_name']; ?></div>
    </div>

    <div class="search-section">
        <form action="add_to_playlist.php" method="post">
            <input type="text" name="song_search" class="search-bar" placeholder="Search songs to add to playlist...">
            <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
        </form>
    </div>

    <table class="songs-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Album</th>
                <th>Duration</th>
                <th>Country</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_array($songs_result)) {
                $minutes = floor($row['duration'] / 60);
                $seconds = $row['duration'] % 60;
                $duration_formatted = sprintf("%d:%02d", $minutes, $seconds);
                $img_url = htmlspecialchars($row['image']);

                echo "<tr onclick='playSong(" . $row['song_id'] . ")'>";
                echo "<td>";
                echo "<div class='song-details'>";
                echo "<img src='$img_url' class='song-img' alt='Song Image'>";
                echo "<div>";
                echo "<div class='song-title'>" . $row['song_title'] . "</div>";
                echo "<div class='song-artist'>" . $row['artist_name'] . "</div>";
                echo "</div>";
                echo "</div>";
                echo "</td>";
                echo "<td>" . $row['album_name'] . "</td>";
                echo "<td class='duration'>" . $duration_formatted . "</td>";
                echo "<td class='country'>" . $row['country_name'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function playSong(songId) {
            window.location.href = 'currentmusic.php?id=' + songId;
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
