<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "mysql";

// Bağlantı kur
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die(" Connection failed: " . mysqli_connect_error());
}

// Veritabanını oluştur
$sql = "CREATE DATABASE IF NOT EXISTS mahmut_selim_yilbas";
if (!mysqli_query($conn, $sql)) {
    die(" Error creating database: " . mysqli_error($conn));
}
echo "✅ Database created successfully<br>";

// Veritabanını seç
mysqli_select_db($conn, 'mahmut_selim_yilbas');

// COUNTRY
$sql = "CREATE TABLE IF NOT EXISTS COUNTRY (
    country_id INT PRIMARY KEY,
    country_name VARCHAR(100),
    country_code VARCHAR(10)
)";
if (!mysqli_query($conn, $sql)) {
    die(" COUNTRY table error: " . mysqli_error($conn));
}
echo "✅ COUNTRY table created successfully<br>";

// USERS
$sql = "CREATE TABLE IF NOT EXISTS USERS (
    user_id INT PRIMARY KEY,
    country_id INT,
    age INT,
    name VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100),
    password VARCHAR(100),
    date_joined DATE,
    last_login DATE,
    follower_num INT DEFAULT 0,
    subscription_type VARCHAR(50),
    top_genre VARCHAR(50),
    num_songs_liked INT DEFAULT 0,
    most_played_artist VARCHAR(100),
    image VARCHAR(200),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" USERS table error: " . mysqli_error($conn));
}
echo "✅ USERS table created successfully<br>";

// ARTISTS
$sql = "CREATE TABLE IF NOT EXISTS ARTISTS (
    artist_id INT PRIMARY KEY,
    name VARCHAR(100),
    genre VARCHAR(50),
    date_joined DATE,
    total_num_music INT DEFAULT 0,
    total_albums INT DEFAULT 0,
    listeners INT DEFAULT 0,
    bio VARCHAR(500),
    country_id INT,
    image VARCHAR(200),
    FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" ARTISTS table error: " . mysqli_error($conn));
}
echo "✅ ARTISTS table created successfully<br>";

// ALBUMS
$sql = "CREATE TABLE IF NOT EXISTS ALBUMS (
    album_id INT PRIMARY KEY,
    artist_id INT,
    name VARCHAR(100),
    release_date DATE,
    genre VARCHAR(50),
    music_number INT DEFAULT 0,
    image VARCHAR(200),
    FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" ALBUMS table error: " . mysqli_error($conn));
}
echo "✅ ALBUMS table created successfully<br>";

// SONGS (rank düzeltildi!)
$sql = "CREATE TABLE IF NOT EXISTS SONGS (
    song_id INT PRIMARY KEY,
    album_id INT,
    title VARCHAR(100),
    duration INT,
    genre VARCHAR(50),
    release_date DATE,
    `rank` INT,
    image VARCHAR(200),
    FOREIGN KEY (album_id) REFERENCES ALBUMS(album_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" SONGS table error: " . mysqli_error($conn));
}
echo "✅ SONGS table created successfully<br>";

// PLAYLISTS
$sql = "CREATE TABLE IF NOT EXISTS PLAYLISTS (
    playlist_id INT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100),
    description VARCHAR(300),
    date_created DATE,
    image VARCHAR(200),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" PLAYLISTS table error: " . mysqli_error($conn));
}
echo "✅ PLAYLISTS table created successfully<br>";

// PLAYLIST_SONGS
$sql = "CREATE TABLE IF NOT EXISTS PLAYLIST_SONGS (
    playlistsong_id INT PRIMARY KEY,
    playlist_id INT,
    song_id INT,
    date_added DATE,
    FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS(playlist_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" PLAYLIST_SONGS table error: " . mysqli_error($conn));
}
echo "✅ PLAYLIST_SONGS table created successfully<br>";

// PLAY_HISTORY
$sql = "CREATE TABLE IF NOT EXISTS PLAY_HISTORY (
    play_id INT PRIMARY KEY,
    user_id INT,
    song_id INT,
    playtime DATETIME,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id),
    FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
)";
if (!mysqli_query($conn, $sql)) {
    die(" PLAY_HISTORY table error: " . mysqli_error($conn));
}
echo "✅ PLAY_HISTORY table created successfully<br>";

// Bağlantıyı kapat
mysqli_close($conn);

echo "<br><strong>🎉 Database setup completed successfully!</strong><br>";
echo "<a href='generate_data.php'>➡️ Next: Generate Sample Data</a>";
?>
