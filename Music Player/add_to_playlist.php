<?php
//CENK EREN 20220702032
session_start();
$user_id = $_SESSION['user_id'];


$data = json_decode(file_get_contents('php://input'), true);
$song_name = $data['song'];
$playlist_name = $data['playlist'];

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


$checkSongSql = "SELECT s.song_id FROM SONGS s WHERE s.title = ?";

$checkSongStmt = $conn->prepare($checkSongSql);
$checkSongStmt->bind_param("s", $song_name);
$checkSongStmt->execute();
$songResult = $checkSongStmt->get_result();

if ($songResult->num_rows > 0) {
    $song_id = $songResult->fetch_assoc()['song_id'];
    
    $playlistSql = "SELECT playlist_id FROM PLAYLISTS WHERE title = ? AND user_id = ?";
    $playlistStmt = $conn->prepare($playlistSql);
    $playlistStmt->bind_param("si", $playlist_name, $user_id);
    $playlistStmt->execute();
    $playlistResult = $playlistStmt->get_result();
    $playlist_id = $playlistResult->fetch_assoc()['playlist_id'];

    $checkExistsSql = "SELECT COUNT(*) as count 
                       FROM PLAYLIST_SONGS 
                       WHERE playlist_id = ? AND song_id = ?";
    $checkExistsStmt = $conn->prepare($checkExistsSql);
    $checkExistsStmt->bind_param("ii", $playlist_id, $song_id);
    $checkExistsStmt->execute();
    $exists = $checkExistsStmt->get_result()->fetch_assoc()['count'] > 0;

    if (!$exists) {
        $addSql = "INSERT INTO PLAYLIST_SONGS (playlist_id, song_id) VALUES (?, ?)";
        $addStmt = $conn->prepare($addSql);
        $addStmt->bind_param("ii", $playlist_id, $song_id);
        $success = $addStmt->execute();
        $message = $success ? "Song added successfully" : "Error adding song";
        $addStmt->close();
    } else {
        $message = "Song already exists in playlist";
        $success = false;
    }
    $checkExistsStmt->close();
    $playlistStmt->close();
} else {
    $message = "Song not found";
    $success = false;
}

$checkSongStmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'message' => $message
]);
?>