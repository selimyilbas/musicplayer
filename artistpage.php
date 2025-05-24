<?php
// Artist Page - Displays artist info, albums, and top songs

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

$artist_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get artist info
$artist_sql = "SELECT a.name, a.genre, a.bio, a.listeners, a.total_num_music, a.total_albums, a.image,
                      c.country_name
               FROM ARTISTS a
               INNER JOIN COUNTRY c ON a.country_id = c.country_id
               WHERE a.artist_id = $artist_id";
$artist_result = mysqli_query($conn, $artist_sql);
$artist_info = mysqli_fetch_array($artist_result);

// Get last 5 albums (with image)
$albums_sql = "SELECT album_id, name, release_date, music_number, image
               FROM ALBUMS
               WHERE artist_id = $artist_id
               ORDER BY release_date DESC
               LIMIT 5";
$albums_result = mysqli_query($conn, $albums_sql);

// Get top 5 most listened songs
$songs_sql = "SELECT s.song_id, s.title, s.duration, al.name as album_name
              FROM SONGS s
              INNER JOIN ALBUMS al ON s.album_id = al.album_id
              WHERE al.artist_id = $artist_id
              ORDER BY s.rank ASC
              LIMIT 5";
$songs_result = mysqli_query($conn, $songs_sql);

// Follow status
$follow_sql = "SELECT most_played_artist FROM USERS WHERE user_id = $user_id";
$follow_result = mysqli_query($conn, $follow_sql);
$follow_row = mysqli_fetch_array($follow_result);
$is_following = ($follow_row['most_played_artist'] == 'Artist ' . $artist_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $artist_info['name']; ?> - Music Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: white;
            display: flex;
            height: 100vh;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #1db954;
            text-decoration: none;
            font-size: 16px;
            z-index: 10;
        }
        .left-panel {
            width: 30%;
            padding: 40px;
            background-color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .right-panel {
            width: 70%;
            padding: 40px;
            overflow-y: auto;
        }
        .artist-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 30px;
        }
        .artist-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .artist-stats {
            text-align: center;
            margin-bottom: 20px;
        }
        .stat {
            margin: 5px 0;
            color: #b3b3b3;
        }
        .artist-bio {
            text-align: center;
            color: #b3b3b3;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .follow-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .follow-btn.following {
            background-color: transparent;
            color: #1db954;
            border: 1px solid #1db954;
        }
        .follow-btn.not-following {
            background-color: #1db954;
            color: white;
        }
        .follow-btn:hover {
            opacity: 0.8;
        }
        .section {
            margin-bottom: 50px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .album-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }
        .album-item {
            background-color: #282828;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .album-item:hover {
            background-color: #333;
        }
        .album-cover {
            width: 100%;
            height: 120px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .album-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .album-info {
            font-size: 12px;
            color: #b3b3b3;
        }
        .song-list {
            background-color: #282828;
            border-radius: 8px;
            padding: 20px;
        }
        .song-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #444;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .song-item:last-child {
            border-bottom: none;
        }
        .song-item:hover {
            background-color: #333;
            margin: 0 -20px;
            padding: 15px 20px;
        }
        .song-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .song-album {
            font-size: 14px;
            color: #b3b3b3;
        }
        .song-duration {
            color: #b3b3b3;
        }
    </style>
</head>
<body>
    <a href="homepage.php" class="back-link">← Back to Home</a>

    <div class="left-panel">
        <img class="artist-image" src="<?php echo $artist_info['image']; ?>" alt="Artist Image" />
        <div class="artist-name"><?php echo $artist_info['name']; ?></div>
        <div class="artist-stats">
            <div class="stat"><?php echo number_format($artist_info['listeners']); ?> listeners</div>
            <div class="stat"><?php echo $artist_info['genre']; ?></div>
            <div class="stat"><?php echo $artist_info['country_name']; ?></div>
            <div class="stat"><?php echo $artist_info['total_albums']; ?> albums • <?php echo $artist_info['total_num_music']; ?> songs</div>
        </div>
        <div class="artist-bio"><?php echo $artist_info['bio']; ?></div>
        <button class="follow-btn <?php echo $is_following ? 'following' : 'not-following'; ?>" onclick="toggleFollow(<?php echo $artist_id; ?>)">
            <?php echo $is_following ? 'Following' : 'Follow'; ?>
        </button>
    </div>

    <div class="right-panel">
        <div class="section">
            <h2>Latest Albums</h2>
            <div class="album-grid">
                <?php
                while ($album = mysqli_fetch_array($albums_result)) {
                    echo "<div class='album-item' onclick='openAlbum(" . $album['album_id'] . ")'>";
                    echo "<img class='album-cover' src='" . $album['image'] . "' alt='Album Cover'>";
                    echo "<div class='album-name'>" . $album['name'] . "</div>";
                    echo "<div class='album-info'>" . $album['release_date'] . " • " . $album['music_number'] . " songs</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="section">
            <h2>Top Songs</h2>
            <div class="song-list">
                <?php
                while ($song = mysqli_fetch_array($songs_result)) {
                    $minutes = floor($song['duration'] / 60);
                    $seconds = $song['duration'] % 60;
                    $duration_formatted = sprintf("%d:%02d", $minutes, $seconds);

                    echo "<div class='song-item' onclick='playSong(" . $song['song_id'] . ")'>";
                    echo "<div>";
                    echo "<div class='song-title'>" . $song['title'] . "</div>";
                    echo "<div class='song-album'>" . $song['album_name'] . "</div>";
                    echo "</div>";
                    echo "<div class='song-duration'>" . $duration_formatted . "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function toggleFollow(artistId) {
            window.location.href = 'toggle_follow.php?artist_id=' + artistId;
        }

        function openAlbum(albumId) {
            window.location.href = 'album_view.php?id=' + albumId;
        }

        function playSong(songId) {
            window.location.href = 'currentmusic.php?id=' + songId;
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
