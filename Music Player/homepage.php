<?php
//CENK EREN 20220702032
session_start();
$user_id = $_SESSION['user_id'];

$servername = "localhost";
$dbusername = "root";
$dbpassword = "12345678";
$dbname = "CENK_EREN";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

$playLists = array();
$playLists_des = array();
$playList_image = array();
$Song_name = array();
$Artist_name = array();
$topArtists = array();
$Listeners = array();
$CountryNames = array();

$sql = "SELECT name FROM USERS WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$playlistSql = "SELECT title, description, image 
                FROM PLAYLISTS 
                WHERE user_id = ?";

$playlistsStmt = $conn->prepare($playlistSql);
$playlistsStmt->bind_param("i", $user_id);
$playlistsStmt->execute();
$songsResult = $playlistsStmt->get_result();

while($row = $songsResult->fetch_assoc()) {
    $playLists[] = $row['title'];
    $playLists_des[] = $row['description'];
    $playList_image[] = $row['image'];
}

$historySql = "SELECT DISTINCT SONGS.title, ARTISTS.name AS artist_name
    FROM PLAY_HISTORY 
    JOIN SONGS ON PLAY_HISTORY.song_id = SONGS.song_id
    JOIN ALBUMS ON SONGS.album_id = ALBUMS.album_id
    JOIN ARTISTS ON ALBUMS.artist_id = ARTISTS.artist_id
    WHERE PLAY_HISTORY.user_id = ?";

$historyStmt = $conn->prepare($historySql);
$historyStmt->bind_param("i", $user_id);
$historyStmt->execute();
$historyResult = $historyStmt->get_result();

$Song_name = [];
$Artist_name = [];

while($row = $historyResult->fetch_assoc()) {
    $Song_name[] = $row['title'];
    $Artist_name[] = $row['artist_name'];
}

$Song_name = array_reverse($Song_name);
$Artist_name = array_reverse($Artist_name);


$topArtistsSql = "SELECT ARTISTS.name, ARTISTS.listeners, c.country_name
    FROM ARTISTS 
    JOIN USERS u ON ARTISTS.country_id = u.country_id
    JOIN COUNTRY c ON u.country_id = c.country_id
    WHERE u.user_id = ?
    ORDER BY ARTISTS.listeners DESC
    LIMIT 5;";

$topArtistsStmt = $conn->prepare($topArtistsSql);
$topArtistsStmt->bind_param("i", $user_id);
$topArtistsStmt->execute();
$topArtistsResult = $topArtistsStmt->get_result();

while($row = $topArtistsResult->fetch_assoc()) {
    $topArtists[] = $row['name'];
    $Listeners[] = $row['listeners'];
    $CountryNames[] = $row['country_name'];
}

$topSongsSql = "SELECT title FROM SONGS ORDER BY `rank` ASC LIMIT 5;";
$topSongssStmt = $conn->prepare($topSongsSql);
$topSongssStmt->execute();
$topSongResult = $topSongssStmt->get_result();

while($row = $topSongResult->fetch_assoc()) {
    $topSongs[] = $row['title'];
}


$mostListenedGenresSql = "SELECT SONGS.genre, COUNT(PLAY_HISTORY.play_id) AS listen_count
    FROM PLAY_HISTORY
    JOIN SONGS ON PLAY_HISTORY.song_id = SONGS.song_id
    GROUP BY SONGS.genre
    ORDER BY listen_count DESC
    LIMIT 5;
";

$mostListenedGenresStmt = $conn->prepare($mostListenedGenresSql);
$mostListenedGenresStmt->execute();
$mostListenedGenresResult = $mostListenedGenresStmt->get_result();

$topGenres = [];
while($row = $mostListenedGenresResult->fetch_assoc()) {
    $topGenres[] = $row['genre'];
}

header('Content-Type: application/json');
echo json_encode([
    'userId' => $user_id,
    'username' => $user['name'],
    'playLists' => $playLists,
    'description' => $playLists_des,
    'image' => $playList_image,
    'title' => $Song_name,
    'artist_name' => $Artist_name,
    'topArtists' => $topArtists,
    'listeners' => $Listeners,
    'country_name' => $CountryNames,
    'topSongs' => $topSongs,
    'topGenres' => $topGenres
]);

$stmt->close();
$playlistsStmt->close();
$historyStmt->close();
$topArtistsStmt->close();
$conn->close();
?>