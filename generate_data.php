<?php

// This file reads text files and generates SQL INSERT statements

// Read text files
$countries = file('countries.txt');
$names = file('names.txt');
$artist_names = file('artist_names.txt');
$song_titles = file('song_titles.txt');
$album_names = file('album_names.txt');
$genres = file('genres.txt');

// Clean up the arrays (remove newlines)
for ($i = 0; $i < count($countries); $i++) {
    $countries[$i] = trim($countries[$i]);
}
for ($i = 0; $i < count($names); $i++) {
    $names[$i] = trim($names[$i]);
}
for ($i = 0; $i < count($artist_names); $i++) {
    $artist_names[$i] = trim($artist_names[$i]);
}
for ($i = 0; $i < count($song_titles); $i++) {
    $song_titles[$i] = trim($song_titles[$i]);
}
for ($i = 0; $i < count($album_names); $i++) {
    $album_names[$i] = trim($album_names[$i]);
}
for ($i = 0; $i < count($genres); $i++) {
    $genres[$i] = trim($genres[$i]);
}

// Open output file
$output = fopen('insert_data.sql', 'w');

// Generate COUNTRY inserts
fwrite($output, "-- Insert Countries\n");
foreach ($countries as $country) {
    $parts = explode(',', $country);
    $sql = "INSERT INTO COUNTRY VALUES ($parts[0], '$parts[1]', '$parts[2]');\n";
    fwrite($output, $sql);
}

// Generate USERS inserts (100 users)
fwrite($output, "\n-- Insert Users\n");
for ($i = 1; $i <= 100; $i++) {
    $name_index = $i % count($names);
    $name = $names[$name_index];
    $username = strtolower(str_replace(' ', '_', $name)) . $i;
    $email = $username . '@email.com';
    $password = 'password' . $i;
    $country_id = ($i % 10) + 1;
    $age = 18 + ($i % 47); // Ages 18-65
    
    // Generate dates
    $days_ago_joined = 365 - ($i * 3);
    $date_joined = date('Y-m-d', strtotime('-' . $days_ago_joined . ' days'));
    $days_ago_login = $i % 30;
    $last_login = date('Y-m-d', strtotime('-' . $days_ago_login . ' days'));
    
    $follower_num = $i * 10;
    $subscription_types = array('Free', 'Premium', 'Pro');
    $subscription = $subscription_types[$i % 3];
    $genre_index = $i % count($genres);
    $top_genre = $genres[$genre_index];
    $num_songs_liked = $i * 5;
    $artist_index = ($i % 100) + 1;
    $most_played_artist = 'Artist ' . $artist_index;
    
    $sql = "INSERT INTO USERS VALUES ";
    $sql .= "($i, $country_id, $age, '$name', '$username', '$email', '$password', '$date_joined', '$last_login', $follower_num, '$subscription', '$top_genre', $num_songs_liked, '$most_played_artist', 'user$i.jpg');\n";
    fwrite($output, $sql);
}

// Generate ARTISTS inserts (100 artists)
fwrite($output, "\n-- Insert Artists\n");
for ($i = 1; $i <= 100; $i++) {
    $artist_index = $i % count($artist_names);
    $name = $artist_names[$artist_index] . ' ' . $i;
    $genre_index = $i % count($genres);
    $genre = $genres[$genre_index];
    
    $days_ago = 3650 - ($i * 30);
    $date_joined = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $total_music = 10 + ($i % 90);
    $total_albums = 1 + ($i % 19);
    $listeners = 1000 + ($i * 1000);
    $bio = "This is artist number $i, specializing in $genre music.";
    $country_id = ($i % 10) + 1;
    
    $sql = "INSERT INTO ARTISTS VALUES ";
    $sql .= "($i, '$name', '$genre', '$date_joined', $total_music, $total_albums, $listeners, '$bio', $country_id, 'artist$i.jpg');\n";
    fwrite($output, $sql);
}

// Generate ALBUMS inserts (200 albums)
fwrite($output, "\n-- Insert Albums\n");
for ($i = 1; $i <= 200; $i++) {
    $artist_id = ($i % 100) + 1;
    $album_index = $i % count($album_names);
    $name = $album_names[$album_index] . ' ' . $i;
    
    $days_ago = 1825 - ($i * 9);
    $release_date = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $genre_index = $i % count($genres);
    $genre = $genres[$genre_index];
    $music_number = 8 + ($i % 12);
    
    $sql = "INSERT INTO ALBUMS VALUES ";
    $sql .= "($i, $artist_id, '$name', '$release_date', '$genre', $music_number, 'album$i.jpg');\n";
    fwrite($output, $sql);
}

// Generate SONGS inserts (1000 songs)
fwrite($output, "\n-- Insert Songs\n");
for ($i = 1; $i <= 1000; $i++) {
    $album_id = ($i % 200) + 1;
    $song_index = $i % count($song_titles);
    $title = $song_titles[$song_index] . ' ' . $i;
    $duration = 120 + ($i % 180); // 2-5 minutes in seconds
    
    $genre_index = $i % count($genres);
    $genre = $genres[$genre_index];
    
    $days_ago = 1825 - (($i % 200) * 9);
    $release_date = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $rank = ($i % 100) + 1;
    
    // Use album image for song
    $sql = "INSERT INTO SONGS VALUES ";
    $sql .= "($i, $album_id, '$title', $duration, '$genre', '$release_date', $rank, 'album$album_id.jpg');\n";
    fwrite($output, $sql);
}

// Generate PLAYLISTS inserts (500 playlists)
fwrite($output, "\n-- Insert Playlists\n");
for ($i = 1; $i <= 500; $i++) {
    $user_id = ($i % 100) + 1;
    $playlist_titles = array('My Favorites', 'Workout Mix', 'Chill Vibes', 'Party Time', 'Road Trip');
    $title_index = $i % 5;
    $title = $playlist_titles[$title_index] . ' ' . $i;
    $description = 'This is playlist number ' . $i . ' created by user ' . $user_id;
    
    $days_ago = $i % 365;
    $date_created = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $sql = "INSERT INTO PLAYLISTS VALUES ";
    $sql .= "($i, $user_id, '$title', '$description', '$date_created', 'playlist$i.jpg');\n";
    fwrite($output, $sql);
}

// Generate PLAYLIST_SONGS inserts (500 entries)
fwrite($output, "\n-- Insert Playlist Songs\n");
for ($i = 1; $i <= 500; $i++) {
    $playlist_id = ($i % 500) + 1;
    $song_id = ($i % 1000) + 1;
    
    $days_ago = $i % 365;
    $date_added = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $sql = "INSERT INTO PLAYLIST_SONGS VALUES ";
    $sql .= "($i, $playlist_id, $song_id, '$date_added');\n";
    fwrite($output, $sql);
}

// Generate PLAY_HISTORY inserts (100 entries)
fwrite($output, "\n-- Insert Play History\n");
for ($i = 1; $i <= 100; $i++) {
    $user_id = ($i % 100) + 1;
    $song_id = ($i % 1000) + 1;
    
    $days_ago = $i % 30;
    $playtime = date('Y-m-d', strtotime('-' . $days_ago . ' days'));
    
    $sql = "INSERT INTO PLAY_HISTORY VALUES ";
    $sql .= "($i, $user_id, $song_id, '$playtime');\n";
    fwrite($output, $sql);
}

// Close file
fclose($output);

echo "Data generation completed!<br>";
echo "Created file: insert_data.sql<br>";
echo "<br><a href='insert_data.php'>Next: Insert Data into Database</a>";
?>