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

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Get user's playlists
$playlist_sql = "SELECT playlist_id, title, image FROM PLAYLISTS WHERE user_id = $user_id";
$playlist_result = mysqli_query($conn, $playlist_sql);

// Get last 10 played songs 
$history_sql = "SELECT s.song_id, s.title, s.image, ph.playtime 
                FROM PLAY_HISTORY ph 
                INNER JOIN SONGS s ON ph.song_id = s.song_id 
                WHERE ph.user_id = $user_id 
                ORDER BY ph.playtime DESC 
                LIMIT 10";
$history_result = mysqli_query($conn, $history_sql);

// Get user's country
$country_sql = "SELECT country_id FROM USERS WHERE user_id = $user_id";
$country_result = mysqli_query($conn, $country_sql);
$country_row = mysqli_fetch_array($country_result);
$user_country_id = $country_row['country_id'];

// Get 5 artists from user's country 
$artist_sql = "SELECT artist_id, name, image, listeners 
               FROM ARTISTS 
               WHERE country_id = $user_country_id 
               ORDER BY listeners DESC 
               LIMIT 5";
$artist_result = mysqli_query($conn, $artist_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hello, <?php echo $user_name; ?>!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: white;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .left-panel {
            width: 30%;
            background-color: #000;
            padding: 20px;
            overflow-y: auto;
        }
        .right-panel {
            width: 70%;
            padding: 20px;
            overflow-y: auto;
        }
        .search-bar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .playlist-item, .song-item, .artist-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #282828;
            border-radius: 5px;
            cursor: pointer;
        }
        .playlist-item:hover, .song-item:hover, .artist-item:hover {
            background-color: #333;
        }
        .item-image {
            width: 50px;
            height: 50px;
            background-color: #444;
            margin-right: 15px;
            border-radius: 5px;
            object-fit: cover;
        }
        .item-info {
            flex: 1;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            margin: 0;
        }
        .add-button {
            width: 40px;
            height: 40px;
            background-color: #1db954;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
        }
        .add-button:hover {
            background-color: #1ed760;
        }
        .logout-link {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #1db954;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-link">Logout</a>
    
    <div class="container">
        <!-- Left Panel - Playlists -->
        <div class="left-panel">
            <div class="section-header">
                <h2>My Playlists</h2>
                <button class="add-button" onclick="createPlaylist()">+</button>
            </div>
            
            <form action="search.php" method="post">
                <input type="text" name="search_query" class="search-bar" placeholder="Search playlists or songs...">
                <input type="hidden" name="search_type" value="playlist_song">
            </form>
            
            <div id="playlists">
                <?php
                while ($row = mysqli_fetch_array($playlist_result)) {
                    $img_url = (!empty($row['image']) && filter_var($row['image'], FILTER_VALIDATE_URL)) 
                               ? htmlspecialchars($row['image']) 
                               : 'https://picsum.photos/seed/playlist' . $row['playlist_id'] . '/50/50';

                    echo "<div class='playlist-item' onclick='openPlaylist(" . $row['playlist_id'] . ")'>";
                    echo "<img src='$img_url' class='item-image' alt='playlist cover'>";
                    echo "<div class='item-info'>";
                    echo "<strong>" . $row['title'] . "</strong>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        
        <!-- Right Panel -->
        <div class="right-panel">
            <!-- Play History Section -->
            <div class="section">
                <h2>Recently Played</h2>
                <form action="search.php" method="post">
                    <input type="text" name="search_query" class="search-bar" placeholder="Search songs to play...">
                    <input type="hidden" name="search_type" value="song">
                </form>
                
                <div id="history">
                    <?php
                    while ($row = mysqli_fetch_array($history_result)) {
                        $img_url = (!empty($row['image']) && filter_var($row['image'], FILTER_VALIDATE_URL)) 
                                   ? htmlspecialchars($row['image']) 
                                   : 'https://picsum.photos/seed/song' . $row['song_id'] . '/50/50';

                        echo "<div class='song-item' onclick='playSong(" . $row['song_id'] . ")'>";
                        echo "<img src='$img_url' class='item-image' alt='song cover'>";
                        echo "<div class='item-info'>";
                        echo "<strong>" . $row['title'] . "</strong><br>";
                        echo "<small>Played: " . $row['playtime'] . "</small>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- Artists Section -->
            <div class="section">
                <h2>Popular Artists in Your Country</h2>
                <form action="search.php" method="post">
                    <input type="text" name="search_query" class="search-bar" placeholder="Search artists...">
                    <input type="hidden" name="search_type" value="artist">
                </form>
                
                <div id="artists">
                    <?php
                    while ($row = mysqli_fetch_array($artist_result)) {
                        $img_url = (!empty($row['image']) && filter_var($row['image'], FILTER_VALIDATE_URL)) 
                                   ? htmlspecialchars($row['image']) 
                                   : 'https://picsum.photos/seed/artist' . $row['artist_id'] . '/50/50';

                        echo "<div class='artist-item' onclick='openArtist(" . $row['artist_id'] . ")'>";
                        echo "<img src='$img_url' class='item-image' alt='artist photo'>";
                        echo "<div class='item-info'>";
                        echo "<strong>" . $row['name'] . "</strong><br>";
                        echo "<small>" . number_format($row['listeners']) . " listeners</small>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openPlaylist(playlistId) {
            window.location.href = 'playlistpage.php?id=' + playlistId;
        }

        function playSong(songId) {
            window.location.href = 'currentmusic.php?id=' + songId;
        }

        function openArtist(artistId) {
            window.location.href = 'artistpage.php?id=' + artistId;
        }

        function createPlaylist() {
            var title = prompt("Enter playlist name:");
            if (title) {
                window.location.href = 'create_playlist.php?title=' + encodeURIComponent(title);
            }
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
