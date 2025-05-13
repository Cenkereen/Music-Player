<?php
//CENK EREN 20220702032
session_start();

$playlist_name = isset($_GET['search']) ? urldecode($_GET['search']) : '';

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$sql = "SELECT SONGS.title, ARTISTS.name as artist_name, ALBUMS.name as album_name
        FROM SONGS
        JOIN PLAYLIST_SONGS  ON SONGS.song_id = PLAYLIST_SONGS.song_id
        JOIN PLAYLISTS  ON PLAYLIST_SONGS.playlist_id = PLAYLISTS.playlist_id
        JOIN ALBUMS ON SONGS.album_id = ALBUMS.album_id
        JOIN ARTISTS ON ALBUMS.artist_id = ARTISTS.artist_id
        WHERE PLAYLISTS.title = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $playlist_name);
$stmt->execute();
$result = $stmt->get_result();

$songs = array();
$artists = array();
$albums = array();
$exists = $result->num_rows > 0;

while($row = $result->fetch_assoc()) {
    $songs[] = $row['title'];
    $artists[] = $row['artist_name'];
    $albums[] = $row['album_name'];
}

header('Content-Type: application/json');
echo json_encode([
    'playlist_name' => $playlist_name,
    'exists' => $exists,
    'songs' => $songs,
    'artists' => $artists,
    'albums' => $albums
]);

$stmt->close();
$conn->close();
session_write_close();
?>