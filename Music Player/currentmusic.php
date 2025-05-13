<?php
//CENK EREN 20220702032
session_start();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$song_name = $data['song'];

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

$sql = "SELECT SONGS.song_id, SONGS.title, SONGS.duration, SONGS.genre, SONGS.release_date, SONGS.`rank`, SONGS.image,
               ARTISTS.name as artist_name, ALBUMS.name as album_name
        FROM SONGS
        JOIN ALBUMS ON SONGS.album_id = ALBUMS.album_id
        JOIN ARTISTS ON ALBUMS.artist_id = ARTISTS.artist_id
        WHERE SONGS.title = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $song_name);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;

if ($exists) {
    $song_info = $result->fetch_assoc();
    
    $play_sql = "INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES (?, ?, ?)";
    $play_stmt = $conn->prepare($play_sql);
    $current_time = time();
    $play_stmt->bind_param("iii", $user_id, $song_info['song_id'], $current_time);
    $play_stmt->execute();
    $play_stmt->close();
    
    echo json_encode([
        'exists' => true,
        'message' => 'Song found',
        'song' => [
            'title' => $song_info['title'],
            'duration' => $song_info['duration'],
            'genre' => $song_info['genre'],
            'release_date' => $song_info['release_date'],
            'rank' => $song_info['rank'],
            'image' => $song_info['image'],
            'artist' => $song_info['artist_name'],
            'album' => $song_info['album_name']
        ]
    ]);
} else {
    echo json_encode([
        'exists' => false,
        'message' => 'Song not found'
    ]);
}

$stmt->close();
$conn->close();
?>