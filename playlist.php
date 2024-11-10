<?php
include 'connection.php';

// Initialize messages array
$messages = [];

// Handle Create New Playlist
if (isset($_POST['create_playlist'])) {
    $playlist_name = trim($_POST['playlist_name']);
    $stmt = $conn->prepare("INSERT INTO playlists (playlist_name) VALUES (?)");
    $stmt->bind_param("s", $playlist_name);
    if ($stmt->execute()) {
        $messages[] = "Playlist created successfully!";
    } else {
        $messages[] = "Error creating playlist.";
    }
    $stmt->close();
}

// Handle Add Song to Playlist
if (isset($_POST['add_song'])) {
    $playlist_id = $_POST['playlist_id'];
    $track_id = $_POST['track_id'];
    
    // Check if song already exists in playlist
    $check_stmt = $conn->prepare("SELECT * FROM playlist_tracks WHERE playlist_id = ? AND track_id = ?");
    $check_stmt->bind_param("ii", $playlist_id, $track_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO playlist_tracks (playlist_id, track_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $playlist_id, $track_id);
        if ($stmt->execute()) {
            $messages[] = "Song added to playlist!";
        } else {
            $messages[] = "Error adding song.";
        }
        $stmt->close();
    } else {
        $messages[] = "Song already exists in playlist.";
    }
    $check_stmt->close();
}

// Handle Remove Song from Playlist
if (isset($_POST['remove_song'])) {
    $playlist_id = $_POST['playlist_id'];
    $track_id = $_POST['track_id'];
    
    $stmt = $conn->prepare("DELETE FROM playlist_tracks WHERE playlist_id = ? AND track_id = ?");
    $stmt->bind_param("ii", $playlist_id, $track_id);
    if ($stmt->execute()) {
        $messages[] = "Song removed from playlist!";
    } else {
        $messages[] = "Error removing song.";
    }
    $stmt->close();
}

// Handle Delete Playlist
if (isset($_POST['delete_playlist'])) {
    $playlist_id = $_POST['playlist_id'];
    
    // First delete all songs from the playlist
    $stmt1 = $conn->prepare("DELETE FROM playlist_tracks WHERE playlist_id = ?");
    $stmt1->bind_param("i", $playlist_id);
    $stmt1->execute();
    $stmt1->close();
    
    // Then delete the playlist itself
    $stmt2 = $conn->prepare("DELETE FROM playlists WHERE playlist_id = ?");
    $stmt2->bind_param("i", $playlist_id);
    if ($stmt2->execute()) {
        $messages[] = "Playlist deleted successfully!";
    } else {
        $messages[] = "Error deleting playlist.";
    }
    $stmt2->close();
}

// Handle Update Playlist Name
if (isset($_POST['update_playlist'])) {
    $playlist_id = $_POST['playlist_id'];
    $new_name = trim($_POST['new_playlist_name']);
    
    $stmt = $conn->prepare("UPDATE playlists SET playlist_name = ? WHERE playlist_id = ?");
    $stmt->bind_param("si", $new_name, $playlist_id);
    if ($stmt->execute()) {
        $messages[] = "Playlist name updated successfully!";
    } else {
        $messages[] = "Error updating playlist name.";
    }
    $stmt->close();
}

// Fetch all playlists
$playlists_query = "SELECT * FROM playlists";
$playlists_result = $conn->query($playlists_query);

// Fetch all available tracks
$tracks_query = "SELECT * FROM music_tracks";
$tracks_result = $conn->query($tracks_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Playlists</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2d0a4d, #4a0a5e, #690b6f);
            background-attachment: fixed;
            color: #fff;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #FFD700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        .messages {
            margin-bottom: 20px;
        }

        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .section {
            background-color: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .playlist-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .playlist-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .playlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        form {
            margin-bottom: 15px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            background-color: #2d0a4d;
            color: white;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 1em;
            padding-right: 30px;
        }

        select option {
            background-color: #2d0a4d;
            color: white;
            padding: 12px;
        }

        select:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
        }

        select option:hover,
        select option:focus,
        select option:active,
        select option:checked {
            background-color: #4a0a5e;
        }

        button {
            background-color: rgba(255, 215, 0, 0.2);
            color: #FFD700;
            border: 1px solid rgba(255, 215, 0, 0.5);
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 5px;
        }

        button:hover {
            background-color: rgba(255, 215, 0, 0.3);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        .song-list {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        .song-list li {
            padding: 8px;
            margin: 5px 0;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .song-list li form {
            margin: 0;
        }

        .song-list li button {
            width: auto;
            margin: 0 0 0 10px;
            padding: 4px 8px;
            font-size: 0.9em;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #FFD700;
            border: 1px solid rgba(255, 215, 0, 0.5);
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background-color: rgba(255, 215, 0, 0.2);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .playlist-container {
                grid-template-columns: 1fr;
            }

            .song-list li {
                flex-direction: column;
                align-items: flex-start;
            }

            .song-list li button {
                margin: 5px 0 0 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="lists.php" class="back-btn">← Back to Music List</a>
        
        <div class="header">
            <h1>Manage Your Playlists</h1>
        </div>

        <?php if (!empty($messages)): ?>
            <div class="messages">
                <?php foreach ($messages as $message): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Create New Playlist Section -->
        <div class="section">
            <h2>Create New Playlist</h2>
            <form method="POST">
                <input type="text" name="playlist_name" placeholder="Enter playlist name" required>
                <button type="submit" name="create_playlist">Create Playlist</button>
            </form>
        </div>

        <!-- Existing Playlists Section -->
        <div class="section">
            <h2>Your Playlists</h2>
            <div class="playlist-container">
                <?php while ($playlist = $playlists_result->fetch_assoc()): ?>
                    <div class="playlist-card">
                        <h3><?php echo htmlspecialchars($playlist['playlist_name']); ?></h3>
                        
                        <!-- Update Playlist Name -->
                        <form method="POST">
                            <input type="hidden" name="playlist_id" value="<?php echo $playlist['playlist_id']; ?>">
                            <input type="text" name="new_playlist_name" placeholder="New playlist name">
                            <button type="submit" name="update_playlist">Update Name</button>
                        </form>

                        <!-- Add Song to Playlist -->
                        <form method="POST">
                            <input type="hidden" name="playlist_id" value="<?php echo $playlist['playlist_id']; ?>">
                            <select name="track_id" required>
                                <option value="">Select a song</option>
                                <?php 
                                $tracks_result->data_seek(0);
                                while ($track = $tracks_result->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $track['track_id']; ?>">
                                        <?php echo htmlspecialchars($track['track_name']); ?> - 
                                        <?php echo htmlspecialchars($track['artist_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" name="add_song">Add Song</button>
                        </form>

                        <!-- Display Playlist Songs -->
                        <?php
                        $playlist_songs_query = $conn->prepare(
                            "SELECT mt.track_name, mt.artist_name, mt.track_id 
                             FROM music_tracks mt 
                             JOIN playlist_tracks pt ON mt.track_id = pt.track_id 
                             WHERE pt.playlist_id = ?"
                        );
                        $playlist_songs_query->bind_param("i", $playlist['playlist_id']);
                        $playlist_songs_query->execute();
                        $playlist_songs = $playlist_songs_query->get_result();
                        ?>
                        
                        <h4>Songs:</h4>
                        <ul class="song-list">
                            <?php while ($song = $playlist_songs->fetch_assoc()): ?>
                                <li>
                                    <span>
                                        <?php echo htmlspecialchars($song['track_name']); ?> - 
                                        <?php echo htmlspecialchars($song['artist_name']); ?>
                                    </span>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="playlist_id" value="<?php echo $playlist['playlist_id']; ?>">
                                        <input type="hidden" name="track_id" value="<?php echo $song['track_id']; ?>">
                                        <button type="submit" name="remove_song">Remove</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>

                        <!-- Delete Playlist -->
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this playlist?');">
                            <input type="hidden" name="playlist_id" value="<?php echo $playlist['playlist_id']; ?>">
                            <button type="submit" name="delete_playlist">Delete Playlist</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>