<?php
function addMovieHandler($db, $data, $files): array {
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $posterPath = '';

    // Handle poster upload
    if (isset($files['poster']) && $files['poster']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $files['poster']['type'];
        $fileSize = $files['poster']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize < 10 * 1024 * 1024) {
            $targetDir = 'images/';
            $fileName = time() . '_' . basename($files['poster']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($files['poster']['tmp_name'], $targetFile)) {
                $posterPath = $targetFile;
            } else {
                return ["", "Failed to upload poster image."];
            }
        } else {
            return ["", "Invalid file type or size. Only JPEG, PNG, GIF under 10MB allowed."];
        }
    }

    try {
        // Insert movie
        $stmt = $db->prepare("INSERT INTO movies (title, release_year, rating, length, description, poster) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $release_year, $rating, $length, $description, $posterPath]);
        $movie_id = $db->lastInsertId();

        // Link actors
        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

        // Link directors
        if (!empty($data['directors'])) {
            $stmt = $db->prepare("INSERT INTO directorDirects (director_id, movie_id) VALUES (?, ?)");
            foreach ($data['directors'] as $director_id) {
                $stmt->execute([$director_id, $movie_id]);
            }
        }

        return ["Movie added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function getMovies($db) {
    $stmt = $db->prepare("SELECT * FROM movies ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteMovie($db, $movieId): array {
    try {
        // Optionally, delete related actor/director links first
        $stmt = $db->prepare("DELETE FROM actorAppearIn WHERE movie_id = ?");
        $stmt->execute([$movieId]);

        $stmt = $db->prepare("DELETE FROM directorDirects WHERE movie_id = ?");
        $stmt->execute([$movieId]);

        // Delete the movie itself
        $stmt = $db->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);

        return ["Movie deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function editMovieHandler($db, $data, $files): array {
    $movie_id = intval($data['movie_id']);
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $posterPath = null;

    // Handle new poster upload (optional)
    if (isset($files['poster']) && $files['poster']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $files['poster']['type'];
        $fileSize = $files['poster']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize < 10 * 1024 * 1024) {
            $targetDir = 'images/';
            $fileName = time() . '_' . basename($files['poster']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($files['poster']['tmp_name'], $targetFile)) {
                $posterPath = $targetFile;
            } else {
                return ["", "Failed to upload poster image."];
            }
        } else {
            return ["", "Invalid file type or size. Only JPEG, PNG, GIF under 10MB allowed."];
        }
    }

    try {
        // Update movie info
        if ($posterPath) {
            $stmt = $db->prepare("UPDATE movies SET title=?, release_year=?, rating=?, length=?, description=?, poster=? WHERE id=?");
            $stmt->execute([$title, $release_year, $rating, $length, $description, $posterPath, $movie_id]);
        } else {
            $stmt = $db->prepare("UPDATE movies SET title=?, release_year=?, rating=?, length=?, description=? WHERE id=?");
            $stmt->execute([$title, $release_year, $rating, $length, $description, $movie_id]);
        }

        // Update actor links
        $stmt = $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?");
        $stmt->execute([$movie_id]);

        if (!empty($data['actors'])) {
    $actorIds = explode(',', $data['actors']);
    $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
    foreach ($actorIds as $actor_id) {
        if (trim($actor_id) !== '') {
            $stmt->execute([$actor_id, $movie_id]);
        }
    }
}
 

        // Update director links
        $stmt = $db->prepare("DELETE FROM directorDirects WHERE movie_id=?");
        $stmt->execute([$movie_id]);

       if (!empty($data['directors'])) {
    $directorIds = explode(',', $data['directors']);
    $stmt = $db->prepare("INSERT INTO directorDirects (director_id, movie_id) VALUES (?, ?)");
    foreach ($directorIds as $director_id) {
        if (trim($director_id) !== '') {
            $stmt->execute([$director_id, $movie_id]);
        }
    }
}


        return ["Movie updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

