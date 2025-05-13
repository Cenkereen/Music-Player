<?php
//CENK EREN 20220702032
session_start();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);


$playlist_name = $data['name'];

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


try {
    $checkExistsSql = "SELECT COUNT(*) as count FROM PLAYLISTS WHERE title = ?";
    $checkExistsStmt = $conn->prepare($checkExistsSql);
    $checkExistsStmt->bind_param("s", $playlist_name);
    $checkExistsStmt->execute();
    $existsInDb = $checkExistsStmt->get_result()->fetch_assoc()['count'] > 0;
    $checkExistsStmt->close();

    if (!$existsInDb) {
        echo json_encode(['success' => false, 'message' => 'Playlist does not exist in database']);
        exit;
    }

    $checkUserSql = "SELECT COUNT(*) as count FROM PLAYLISTS WHERE title = ? AND user_id = ?";
    $checkUserStmt = $conn->prepare($checkUserSql);
    $checkUserStmt->bind_param("si", $playlist_name, $user_id);
    $checkUserStmt->execute();
    $userHasPlaylist = $checkUserStmt->get_result()->fetch_assoc()['count'] > 0;
    $checkUserStmt->close();

    if ($userHasPlaylist) {
        echo json_encode(['success' => false, 'message' => 'You already have this playlist']);
        exit;
    }

    $getDetailsSql = "SELECT description, image FROM PLAYLISTS WHERE title = ? LIMIT 1";
    $detailsStmt = $conn->prepare($getDetailsSql);
    $detailsStmt->bind_param("s", $playlist_name);
    $detailsStmt->execute();
    $details = $detailsStmt->get_result()->fetch_assoc();
    $detailsStmt->close();

    $sql = "INSERT INTO PLAYLISTS (title, user_id, description, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $playlist_name, $user_id, $details['description'], $details['image']);
    
    $success = $stmt->execute();
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Playlist added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add playlist']);
    }
    
    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>