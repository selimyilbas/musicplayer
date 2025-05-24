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

$song_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get song info with artist, album and image
$sql = "SELECT s.title as song_title, s.duration, s.genre, s.release_date,
               a.name as artist_name, a.artist_id,
               al.name as album_name, al.album_id,
               al.image as album_image
        FROM SONGS s
        INNER JOIN ALBUMS al ON s.album_id = al.album_id
        INNER JOIN ARTISTS a ON al.artist_id = a.artist_id
        WHERE s.song_id = $song_id";
$result = mysqli_query($conn, $sql);
$song_info = mysqli_fetch_array($result);

// Add to play history
$history_sql = "SELECT MAX(play_id) as max_id FROM PLAY_HISTORY";
$history_result = mysqli_query($conn, $history_sql);
$history_row = mysqli_fetch_array($history_result);
$new_play_id = $history_row['max_id'] + 1;

$insert_history = "INSERT INTO PLAY_HISTORY (play_id, user_id, song_id, playtime)
                   VALUES ($new_play_id, $user_id, $song_id, '" . date('Y-m-d') . "')";
mysqli_query($conn, $insert_history);

// Format duration
$minutes = floor($song_info['duration'] / 60);
$seconds = $song_info['duration'] % 60;
$duration_formatted = sprintf("%d:%02d", $minutes, $seconds);

// Album image (safety fallback)
$album_image = !empty($song_info['album_image']) ? htmlspecialchars($song_info['album_image']) : 'https://picsum.photos/300';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Now Playing - <?php echo $song_info['song_title']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .player-container {
            text-align: center;
            width: 90%;
            max-width: 600px;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #1db954;
            text-decoration: none;
            font-size: 16px;
        }
        .album-cover {
            width: 300px;
            height: 300px;
            margin: 0 auto 30px;
            border-radius: 10px;
            object-fit: cover;
            display: block;
        }
        .song-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .artist-name {
            font-size: 20px;
            color: #b3b3b3;
            margin-bottom: 5px;
            cursor: pointer;
        }
        .artist-name:hover {
            color: #1db954;
            text-decoration: underline;
        }
        .album-name {
            font-size: 16px;
            color: #b3b3b3;
            margin-bottom: 30px;
        }
        .player-controls {
            margin: 40px 0;
        }
        .progress-bar {
            width: 100%;
            height: 4px;
            background-color: #333;
            border-radius: 2px;
            margin: 20px 0;
            position: relative;
            cursor: pointer;
        }
        .progress {
            height: 100%;
            background-color: #1db954;
            border-radius: 2px;
            width: 0%;
            transition: width 0.1s;
        }
        .time-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #b3b3b3;
        }
        .control-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 30px 0;
        }
        .control-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
            padding: 10px;
            transition: color 0.3s;
        }
        .control-btn:hover {
            color: #1db954;
        }
        .play-btn {
            font-size: 40px;
            background-color: #1db954;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .play-btn:hover {
            background-color: #1ed760;
            color: white;
        }
        .song-info {
            margin-top: 40px;
            text-align: left;
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
        }
        .info-row {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            color: #b3b3b3;
        }
    </style>
</head>
<body>
    <a href="homepage.php" class="back-link">← Back to Home</a>
    
    <div class="player-container">
        <img src="<?php echo $album_image; ?>" alt="Album Cover" class="album-cover">
        
        <div class="song-title"><?php echo $song_info['song_title']; ?></div>
        <div class="artist-name" onclick="goToArtist(<?php echo $song_info['artist_id']; ?>)">
            <?php echo $song_info['artist_name']; ?>
        </div>
        <div class="album-name"><?php echo $song_info['album_name']; ?></div>
        
        <div class="player-controls">
            <div class="progress-bar" onclick="seek(event)">
                <div class="progress" id="progress"></div>
            </div>
            <div class="time-info">
                <span id="current-time">0:00</span>
                <span><?php echo $duration_formatted; ?></span>
            </div>
            
            <div class="control-buttons">
                <button class="control-btn">⏮</button>
                <button class="control-btn play-btn" onclick="togglePlay()">▶</button>
                <button class="control-btn">⏭</button>
            </div>
        </div>
        
        <div class="song-info">
            <div class="info-row">
                <span class="info-label">Genre:</span>
                <span><?php echo $song_info['genre']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Release Date:</span>
                <span><?php echo $song_info['release_date']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Album:</span>
                <span><?php echo $song_info['album_name']; ?></span>
            </div>
        </div>
    </div>
    
    <script>
        function goToArtist(artistId) {
            window.location.href = 'artistpage.php?id=' + artistId;
        }

        var isPlaying = false;
        var progress = 0;
        var duration = <?php echo $song_info['duration']; ?>;
        var interval;

        function togglePlay() {
            var playBtn = document.querySelector('.play-btn');
            if (isPlaying) {
                playBtn.innerHTML = '▶';
                clearInterval(interval);
            } else {
                playBtn.innerHTML = '❚❚';
                interval = setInterval(() => {
                    progress += 1;
                    let percent = (progress / duration) * 100;
                    document.getElementById('progress').style.width = percent + "%";

                    let minutes = Math.floor(progress / 60);
                    let seconds = progress % 60;
                    document.getElementById('current-time').innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    if (progress >= duration) {
                        clearInterval(interval);
                        isPlaying = false;
                        playBtn.innerHTML = '▶';
                    }
                }, 1000);
            }
            isPlaying = !isPlaying;
        }

        function seek(event) {
            const bar = document.querySelector(".progress-bar");
            const percent = event.offsetX / bar.offsetWidth;
            progress = Math.floor(percent * duration);
            document.getElementById("progress").style.width = (percent * 100) + "%";

            let minutes = Math.floor(progress / 60);
            let seconds = progress % 60;
            document.getElementById("current-time").innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>
