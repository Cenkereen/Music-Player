<?php
// CENK EREN 20220702032
session_start();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$artist_name = $data['artist'] ?? '';
$action = $data['action'] ?? '';

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($action === "follow" || $action === "unfollow") {
    $sql = ($action === "follow")
        ? "UPDATE ARTISTS SET listeners = listeners + 1 WHERE name = ?"
        : "UPDATE ARTISTS SET listeners = GREATEST(listeners - 1, 0) WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $artist_name);
    $stmt->execute();
    $stmt->close();

    // Get updated listener count
    $stmt = $conn->prepare("SELECT listeners FROM ARTISTS WHERE name = ?");
    $stmt->bind_param("s", $artist_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $listeners = $result->fetch_assoc()['listeners'];
    echo json_encode(['success' => true, 'listeners' => $listeners]);
    $stmt->close();
    $conn->close();
    exit;
}

$sql = "SELECT ARTISTS.artist_id, ARTISTS.name, ARTISTS.listeners, ARTISTS.image, ARTISTS.bio, COUNTRY.country_name, GROUP_CONCAT(DISTINCT ALBUMS.name) as albums, GROUP_CONCAT(DISTINCT SONGS.title) as songs
        FROM ARTISTS 
        JOIN COUNTRY ON ARTISTS.country_id = COUNTRY.country_id
        LEFT JOIN ALBUMS ON ARTISTS.artist_id = ALBUMS.artist_id
        LEFT JOIN SONGS ON ALBUMS.album_id = SONGS.album_id
        WHERE ARTISTS.name = ?
        GROUP BY ARTISTS.artist_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $artist_name);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;

if ($exists) {
    $artist_info = $result->fetch_assoc();
    echo json_encode([
        'exists' => true,
        'message' => 'Artist found',
        'artist' => [
            'name' => $artist_info['name'],
            'listeners' => $artist_info['listeners'],
            'country' => $artist_info['country_name'],
            'image' => $artist_info['image'],
            'bio' => $artist_info['bio'],
            'albums' => explode(',', $artist_info['albums'] ?? ''),
            'songs' => explode(',', $artist_info['songs'] ?? '')
        ]
    ]);
} else {
    echo json_encode([
        'exists' => false,
        'message' => 'Artist not found'
    ]);
}

$stmt->close();
$conn->close();
?>
