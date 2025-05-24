<?php
// Album View Page – Shows songs in an album and album image

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get album info including image
$album_sql = "SELECT al.name as album_name, al.release_date, al.genre, al.image,
                     a.name as artist_name, a.artist_id
              FROM ALBUMS al
              INNER JOIN ARTISTS a ON al.artist_id = a.artist_id
              WHERE al.album_id = $album_id";
$album_result = mysqli_query($conn, $album_sql);
$album_info = mysqli_fetch_array($album_result);

// Get songs in album
$songs_sql = "SELECT song_id, title, duration, `rank`
              FROM SONGS
              WHERE album_id = $album_id
              ORDER BY `rank` ASC";
$songs_result = mysqli_query($conn, $songs_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $album_info['album_name']; ?> - Music Player</title>
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
        .album-info {
            margin-bottom: 40px;
            text-align: center;
        }
        .album-cover {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            margin: 0 auto 20px;
            object-fit: cover;
            display: block;
        }
        h1 {
            margin: 0 0 10px 0;
        }
        .artist-name {
            color: #b3b3b3;
            font-size: 18px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .artist-name:hover {
            color: #1db954;
            text-decoration: underline;
        }
        .album-details {
            color: #b3b3b3;
            font-size: 14px;
        }
        .songs-table {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
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
        }
        .songs-table tr:hover {
            background-color: #282828;
            cursor: pointer;
        }
        .track-number {
            color: #b3b3b3;
            width: 50px;
        }
        .song-title {
            font-weight: bold;
        }
        .duration {
            color: #b3b3b3;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="artistpage.php?id=<?php echo $album_info['artist_id']; ?>" class="back-link">← Back to Artist</a>
    </div>
    
    <div class="album-info">
        <img src="<?php echo $album_info['image']; ?>" alt="Album Cover" class="album-cover">
        <h1><?php echo $album_info['album_name']; ?></h1>
        <div class="artist-name" onclick="goToArtist(<?php echo $album_info['artist_id']; ?>)">
            <?php echo $album_info['artist_name']; ?>
        </div>
        <div class="album-details">
            <?php echo $album_info['release_date']; ?> • <?php echo $album_info['genre']; ?>
        </div>
    </div>
    
    <table class="songs-table">
        <thead>
            <tr>
                <th class="track-number">#</th>
                <th>Title</th>
                <th class="duration">Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_array($songs_result)) {
                $minutes = floor($row['duration'] / 60);
                $seconds = $row['duration'] % 60;
                $duration_formatted = sprintf("%d:%02d", $minutes, $seconds);
                
                echo "<tr onclick='playSong(" . $row['song_id'] . ")'>";
                echo "<td class='track-number'>" . $row['rank'] . "</td>";
                echo "<td class='song-title'>" . $row['title'] . "</td>";
                echo "<td class='duration'>" . $duration_formatted . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    
    <script>
        function playSong(songId) {
            window.location.href = 'currentmusic.php?id=' + songId;
        }
        function goToArtist(artistId) {
            window.location.href = 'artistpage.php?id=' + artistId;
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
