<?php
//CENK EREN 20220702032
$names = file('txt_files/names.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$genres = file('txt_files/genres.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$artist_names = file('txt_files/artist_names.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
shuffle($artist_names);
$bios = file('txt_files/bios.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$numbers = file('txt_files/numbers.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
shuffle($numbers);

$song_names1 = file('txt_files/song_names1.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$song_names2= file('txt_files/song_names2.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$album_names1 = file('txt_files/album_names1.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$album_names2= file('txt_files/album_names2.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$playlist_names1 = file('txt_files/playlist_names1.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$playlist_names2= file('txt_files/playlist_names2.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$descriptions= file('txt_files/description.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$country_names = file('txt_files/country_names.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$country_shorts = file('txt_files/country_shorts.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$num_users = 100;
$num_artists = 100;
$num_songs = 1000;
$num_albums = 300;
$num_playlist = 300;
$num_playlist_songs = 2000;
$num_play_history = 500;
$num_country = 10;

$user_ids = range(1, 100);
shuffle($user_ids);


$output_file = fopen("insert_values.sql", "w");

for ($i = 0; $i < $num_country; $i++) {
    $country_name = $country_names[$i];
    $country_code = $country_shorts[$i];
    $sql = "INSERT INTO COUNTRY (country_name, country_code) VALUES ('$country_name', '$country_code');\n";


    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_users; $i++) {
    $name = $names[array_rand($names)];
    $username = $name . rand(1, 999);
    $email = strtolower($username) . '@' . (rand(0, 1) ? 'gmail.com' : 'hotmail.com');
    $password = $name . '123';
    $country_id = rand(1, 9);
    $age = rand(18, 60);
    $subscription_type = rand(0, 1) ? 'Premium' : 'Free';
    $top_genre = $genres[array_rand($genres)];
    $num_songs_liked = rand(0, 200);
    $most_played_artist = $artist_names[array_rand($artist_names)];
    $follower_num = rand(0, 1000);
    $image = 'https://picsum.photos/id/' .rand(100, 999) .'/200';

    $sql = "INSERT INTO USERS (country_id, age, name, username, email, password, follower_num, subscription_type, top_genre, num_songs_liked, most_played_artist, image) VALUES ($country_id, $age, '$name', '$username', '$email', '$password', $follower_num, '$subscription_type', '$top_genre', $num_songs_liked, '$most_played_artist', '$image');\n";

    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_artists; $i++) {
    $name = $artist_names[$i];
    $country_id = rand(1, 9);
    $genre = $genres[array_rand($genres)];
    $total_num_music = rand(1, 29);
    $total_albums = rand(1, 3);
    $listeners = rand(0, 1000);
    $image = 'https://picsum.photos/id/' .rand(100, 999) .'/200';
    $bio = $name . ' ' . $bios[array_rand($bios)];
    $sql = "INSERT INTO ARTISTS (country_id, name, genre, total_num_music, total_albums, listeners, image, bio) VALUES ($country_id, '$name', '$genre', $total_num_music, $total_albums, $listeners, '$image', '$bio');\n";

    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_albums; $i++) {
    $name = $album_names1[array_rand($album_names1)] . ' ' . $album_names2[array_rand($album_names2)];
    $artist_id = rand(1, 100);
    $genre = $genres[array_rand($genres)];
    $release_date = rand(1980, 2025) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $music_number = rand(1, 10);
    $image = 'https://picsum.photos/id/' .rand(100, 999) .'/200';
    $sql = "INSERT INTO ALBUMS (name, artist_id, music_number, genre, release_date, image) VALUES ('$name', $artist_id, $music_number, '$genre', '$release_date', '$image');\n";


    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_songs; $i++) {
    $title = $song_names1[array_rand($song_names1)] . ' ' . $song_names2[array_rand($song_names2)];
    $rank = $numbers[$i];
    $album_id = rand(1, 299);
    $genre = $genres[array_rand($genres)];
    $duration = rand(60, 300);
    $release_date = rand(1980, 2025) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $image = 'https://picsum.photos/id/' .rand(100, 999) .'/200';
    $sql = "INSERT INTO SONGS (title, `rank`, album_id, genre, duration, release_date, image) VALUES ('$title', $rank, $album_id, '$genre', $duration, '$release_date', '$image');\n";


    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_playlist; $i++) {
    $title = $playlist_names1[array_rand($playlist_names1)] . ' ' . $playlist_names2[array_rand($playlist_names2)];
    $user_id = $user_ids[$i % count($user_ids)];
    $description = $descriptions[array_rand($descriptions)];
    $date_created = rand(1980, 2025) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $image = 'https://picsum.photos/id/' .rand(100, 999) .'/200';

    $sql = "INSERT INTO PLAYLISTS (title, user_id, description, date_created, image) VALUES ('$title', $user_id, '$description', '$date_created', '$image');\n";
    
    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_playlist_songs; $i++) {
    $playlist_id = rand(1, 299);
    $song_id = rand(1, 999);
    $date_added = rand(1980, 2025) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $sql = "INSERT INTO PLAYLIST_SONGS (playlist_id, song_id, date_added) VALUES ($playlist_id, $song_id, '$date_added');\n";


    fwrite($output_file, $sql);
}

for ($i = 0; $i < $num_play_history; $i++) {
    $user_id = $user_ids[$i % count($user_ids)];
    $song_id = rand(1, 1000);
    $playtime = rand(1,300);
    $sql = "INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES ($user_id, $song_id, $playtime);\n";


    fwrite($output_file, $sql);
}
fclose($output_file);
?>
