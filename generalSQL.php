<?php

// This file handles genre and country related queries

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

// Process custom query if submitted
$query_results = null;
$error_message = null;

if (isset($_POST['custom_query'])) {
    $custom_query = $_POST['custom_query'];
    
    // Only allow SELECT queries for safety
    if (stripos(trim($custom_query), 'SELECT') === 0) {
        $result = mysqli_query($conn, $custom_query);
        if ($result) {
            $query_results = array();
            while ($row = mysqli_fetch_array($result)) {
                $query_results[] = $row;
            }
        } else {
            $error_message = "Query error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Only SELECT queries are allowed.";
    }
}

// Predefined queries
$top_genres_sql = "SELECT s.genre, COUNT(*) as play_count
                   FROM PLAY_HISTORY ph
                   INNER JOIN SONGS s ON ph.song_id = s.song_id
                   GROUP BY s.genre
                   ORDER BY play_count DESC
                   LIMIT 5";
$top_genres_result = mysqli_query($conn, $top_genres_sql);

$songs_by_country_sql = "SELECT c.country_name, COUNT(DISTINCT s.song_id) as song_count
                         FROM COUNTRY c
                         INNER JOIN ARTISTS a ON c.country_id = a.country_id
                         INNER JOIN ALBUMS al ON a.artist_id = al.artist_id
                         INNER JOIN SONGS s ON al.album_id = s.album_id
                         GROUP BY c.country_name
                         ORDER BY song_count DESC
                         LIMIT 5";
$songs_by_country_result = mysqli_query($conn, $songs_by_country_sql);

$popular_artists_by_genre_sql = "SELECT genre, name, listeners
                                 FROM ARTISTS
                                 WHERE (genre, listeners) IN (
                                     SELECT genre, MAX(listeners)
                                     FROM ARTISTS
                                     GROUP BY genre
                                 )
                                 ORDER BY listeners DESC
                                 LIMIT 5";
$popular_artists_result = mysqli_query($conn, $popular_artists_by_genre_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>General SQL Operations - Music Player</title>
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
        }
        .back-link {
            color: #1db954;
            text-decoration: none;
            font-size: 16px;
        }
        h1 {
            margin: 0;
        }
        .section {
            background-color: #282828;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        h2 {
            margin-top: 0;
            color: #1db954;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .result-table th {
            background-color: #333;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #1db954;
        }
        .result-table td {
            padding: 10px;
            border-bottom: 1px solid #444;
        }
        .result-table tr:hover {
            background-color: #333;
        }
        .custom-query-section {
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        textarea {
            width: 100%;
            padding: 15px;
            background-color: #333;
            color: white;
            border: 1px solid #444;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
        }
        .submit-btn {
            margin-top: 15px;
            padding: 12px 30px;
            background-color: #1db954;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #1ed760;
        }
        .error {
            color: #ff6b6b;
            margin-top: 10px;
        }
        .custom-results {
            background-color: #282828;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>General SQL Operations</h1>
        <a href="homepage.php" class="back-link">← Back to Home</a>
    </div>
    
    <div class="custom-query-section">
        <h2>Custom SQL Query</h2>
        <form method="post">
            <textarea name="custom_query" placeholder="Enter your SELECT query here..."><?php echo isset($_POST['custom_query']) ? $_POST['custom_query'] : ''; ?></textarea>
            <br>
            <button type="submit" class="submit-btn">Execute Query</button>
        </form>
        
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($query_results !== null): ?>
            <div class="custom-results">
                <h3>Query Results (Top 5):</h3>
                <?php if (count($query_results) > 0): ?>
                    <table class="result-table">
                        <thead>
                            <tr>
                                <?php
                                // Display column headers
                                $first_row = $query_results[0];
                                $i = 0;
                                foreach ($first_row as $key => $value) {
                                    if ($i % 2 == 0) { // Skip numeric keys
                                        echo "<th>" . $key . "</th>";
                                    }
                                    $i++;
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Display up to 5 rows
                            $count = 0;
                            foreach ($query_results as $row) {
                                if ($count >= 5) break;
                                echo "<tr>";
                                $i = 0;
                                foreach ($row as $value) {
                                    if ($i % 2 == 0) { // Skip numeric keys
                                        echo "<td>" . $value . "</td>";
                                    }
                                    $i++;
                                }
                                echo "</tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>Top 5 Genres by Play Count</h2>
        <table class="result-table">
            <thead>
                <tr>
                    <th>Genre</th>
                    <th>Play Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($top_genres_result)) {
                    echo "<tr>";
                    echo "<td>" . $row['genre'] . "</td>";
                    echo "<td>" . $row['play_count'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>Top 5 Countries by Song Count</h2>
        <table class="result-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Number of Songs</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($songs_by_country_result)) {
                    echo "<tr>";
                    echo "<td>" . $row['country_name'] . "</td>";
                    echo "<td>" . $row['song_count'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>Most Popular Artist per Genre (Top 5)</h2>
        <table class="result-table">
            <thead>
                <tr>
                    <th>Genre</th>
                    <th>Artist</th>
                    <th>Listeners</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($popular_artists_result)) {
                    echo "<tr>";
                    echo "<td>" . $row['genre'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . number_format($row['listeners']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>