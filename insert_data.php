<?php

// This file reads the generated SQL file and inserts data into database

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "mahmut_selim_yilbas"; 

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Read SQL file
$sql_file = file_get_contents('insert_data.sql');

// Split by semicolon to get individual queries
$queries = explode(';', $sql_file);

$success_count = 0;
$error_count = 0;

// Execute each query
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (mysqli_query($conn, $query)) {
            $success_count++;
        } else {
            echo "Error: " . mysqli_error($conn) . "<br>";
            $error_count++;
        }
    }
}

echo "Data insertion completed!<br>";
echo "Successful inserts: $success_count<br>";
echo "Errors: $error_count<br>";

// Close connection
mysqli_close($conn);

echo "<br><a href='login.html'>Go to Login Page</a>";
?>