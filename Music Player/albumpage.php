<?php
// Start the session
session_start();

// Get the album name from the URL parameter
$album_name = isset($_GET['album']) ? urldecode($_GET['album']) : '';

// Database connection details
$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

// Create the connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// Prepare the SQL query to get album details along with its songs and artist info
$sql = "SELECT 
            ALBUMS.name as album_name, 
            ALBUMS.release_date, 
            ALBUMS.genre as album_genre, 
            ALBUMS.image as album_image, 
            ARTISTS.name as artist_name, 
            ARTISTS.bio as artist_bio, 
            ARTISTS.country_id, 
            ARTISTS.image as artist_image, 
            SONGS.title as song_title, 
            SONGS.duration as song_duration
        FROM ALBUMS
        JOIN ARTISTS ON ALBUMS.artist_id = ARTISTS.artist_id
        JOIN SONGS ON ALBUMS.album_id = SONGS.album_id
        WHERE ALBUMS.name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $album_name);
$stmt->execute();
$result = $stmt->get_result();

$album_details = array();
$songs = array();
$exists = $result->num_rows > 0;

if ($exists) {
    // Fetch album details and songs
    while ($row = $result->fetch_assoc()) {
        $album_details = [
            'name' => $row['album_name'],
            'release_date' => $row['release_date'],
            'genre' => $row['album_genre'],
            'image' => $row['album_image'],
            'artist_name' => $row['artist_name'],
            'artist_bio' => $row['artist_bio'],
            'artist_image' => $row['artist_image']
        ];
        $songs[] = [
            'title' => $row['song_title'],
            'duration' => $row['song_duration']
        ];
    }
}

// Return the data in JSON format
header('Content-Type: application/json');
echo json_encode([
    'exists' => $exists,
    'album_details' => $album_details,
    'songs' => $songs
]);

// Close the statement and connection
$stmt->close();
$conn->close();

// Close the session
session_write_close();
?>
