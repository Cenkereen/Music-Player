<?php
//CENK EREN 20220702032
$servername = "localhost";
$username = "root";
$password = "12345678";

$conn = new mysqli($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE CENK_EREN";

if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . mysqli_connect_error());
}

mysqli_select_db($conn, 'CENK_EREN');

//CREATE COUNTRY
$sql = "CREATE TABLE COUNTRY (
    country_id INT AUTO_INCREMENT PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL,
    country_code VARCHAR(5) NOT NULL
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

// CREATE USER
$sql = "CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    age INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    follower_num INT(6) DEFAULT 0,
    subscription_type VARCHAR(20),
    top_genre VARCHAR(50),
    num_songs_liked INT(6) DEFAULT 0,
    most_played_artist VARCHAR(100),
    image VARCHAR(255) DEFAULT 'default.jpg',
    
    FOREIGN KEY (country_id) REFERENCES COUNTRY (country_id)
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE PLAY_HISTORY
$sql = "CREATE TABLE PLAY_HISTORY (
    play_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    playtime INT NOT NULL
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE ARTISTS
$sql = "CREATE TABLE ARTISTS (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_num_music INT DEFAULT 0,
    total_albums INT DEFAULT 0,
    listeners INT DEFAULT 0,
    bio VARCHAR(250),
    country_id INT,
    image VARCHAR(255) DEFAULT 'default.jpg',

    FOREIGN KEY (country_id) REFERENCES COUNTRY (country_id)
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE ALBUMS
$sql = "CREATE TABLE ALBUMS (
    album_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    release_date DATE,
    genre VARCHAR(50) NOT NULL,
    music_number INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',

    FOREIGN KEY (artist_id) REFERENCES ARTISTS (artist_id)
)";
if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE SONGS
$sql = "CREATE TABLE SONGS (
    song_id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    duration INT DEFAULT 0,
    genre VARCHAR(50),
    release_date DATE,
    `rank` INT,
    image VARCHAR(255) DEFAULT 'default.jpg',

    FOREIGN KEY (album_id) REFERENCES ALBUMS (album_id)
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE PLAYLISTS
$sql = "CREATE TABLE PLAYLISTS (
    playlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(250),
    date_created DATE,
    image VARCHAR(255) DEFAULT 'default.jpg',

    FOREIGN KEY (user_id) REFERENCES USERS (user_id)
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

//CREATE PLAYLIST_SONGS
$sql = "CREATE TABLE PLAYLIST_SONGS (
    playlistsong_id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    song_id INT NOT NULL,
    date_added DATE,

    FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS (playlist_id),
    FOREIGN KEY (song_id) REFERENCES SONGS (song_id)
)";

if($conn->query($sql) === FALSE) {
    die("Error creating table: " . mysqli_connect_error());
}

exec('php generete_data.php');

$file = 'insert_values.sql'; 
$sql_queries = file_get_contents($file);

if ($sql_queries === false) {
    die("Error reading the SQL file.");
}

$queries = explode(';', $sql_queries);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === FALSE) echo "Error executing query: " . $conn->error . "<br>";
    }
}

$conn->close();

header("Location: login.html");
exit();
?>