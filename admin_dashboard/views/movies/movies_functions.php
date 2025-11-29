<?php

// ---------------------------------------------------------
// ADD MOVIE
// ---------------------------------------------------------
function addMovieHandler($db, $data, $files): array {
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $genre = trim($data['genre']);
    $language = trim($data['language']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $trailer_url = trim($data['trailer_url']);
    $posterPath = '';

    // Handle poster upload
    if (isset($files['poster']) && $files['poster']['error'] === 0) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $files['poster']['type'];
        $fileSize = $files['poster']['size'];

        if (!in_array($fileType, $allowedTypes) || $fileSize > 10 * 1024 * 1024) {
            return ["", "Invalid file type or size. Only JPG, PNG, GIF under 10MB allowed."];
        }

        $targetDir  = __DIR__ . '/../../../images/';
        $publicPath = '/cinema-website/images/';
        $fileName   = time() . '_' . basename($files['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (!move_uploaded_file($files['poster']['tmp_name'], $targetFile)) {
            return ["", "Failed to upload poster image."];
        }

        // Store browser-friendly path
        $posterPath = $publicPath . $fileName;
    }

    // Insert movie
    try {
        $stmt = $db->prepare("
            INSERT INTO movies (title, release_year, rating, genre, language, length, description, poster, trailer_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $release_year, $rating, $genre, $language, $length, $description, $posterPath, $trailer_url]);

        $movie_id = $db->lastInsertId();

        // Actors
        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

        // Directors
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

// ---------------------------------------------------------
// GET ALL MOVIES
// ---------------------------------------------------------
function getMovies($db) {
    $stmt = $db->query("SELECT * FROM movies ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ---------------------------------------------------------
// DELETE MOVIE
// ---------------------------------------------------------
function deleteMovie($db, $movieId): array {
    try {
        $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?")->execute([$movieId]);
        $db->prepare("DELETE FROM directorDirects WHERE movie_id=?")->execute([$movieId]);
        $db->prepare("DELETE FROM movies WHERE id=?")->execute([$movieId]);

        return ["Movie deleted successfully!", ""];
    }
    catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}


// ---------------------------------------------------------
// EDIT MOVIE
// ---------------------------------------------------------
function editMovieHandler($db, $data, $files): array {

    $movie_id = intval($data['movie_id']);
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $genre = trim($data['genre']);
    $language = trim($data['language']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $trailer_url = trim($data['trailer_url']);
    $posterPath = null;

    // NEW POSTER UPLOAD
    if (isset($files['poster']) && $files['poster']['error'] === 0) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $files['poster']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            return ["", "Invalid poster file type."];
        }

        $targetDir  = __DIR__ . '/../../../images/';
        $publicPath = '/cinema-website/images/';   // FIXED to match addMovieHandler
        $fileName   = time() . '_' . basename($files['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (!move_uploaded_file($files['poster']['tmp_name'], $targetFile)) {
            return ["", "Failed to upload poster image."];
        }

        $posterPath = $publicPath . $fileName;
    }

    // UPDATE MOVIE
    try {
        if ($posterPath) {
            $stmt = $db->prepare("
                UPDATE movies 
                SET title=?, release_year=?, rating=?, genre=?, language=?, length=?, description=?, poster=?, trailer_url=?
                WHERE id=?
            ");
            $params = [$title, $release_year, $rating, $genre, $language, $length, $description, $posterPath, $trailer_url, $movie_id];
        } else {
            $stmt = $db->prepare("
                UPDATE movies 
                SET title=?, release_year=?, rating=?, genre=?, language=?, length=?, description=?, trailer_url=?
                WHERE id=?
            ");
            $params = [$title, $release_year, $rating, $genre, $language, $length, $description, $trailer_url, $movie_id];
        }

        $stmt->execute($params);

        // Reset actors
        $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?")->execute([$movie_id]);
        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

        // Reset directors
        $db->prepare("DELETE FROM directorDirects WHERE movie_id=?")->execute([$movie_id]);
        if (!empty($data['directors'])) {
            $stmt = $db->prepare("INSERT INTO directorDirects (director_id, movie_id) VALUES (?, ?)");
            foreach ($data['directors'] as $director_id) {
                $stmt->execute([$director_id, $movie_id]);
            }
        }

        return ["Movie updated successfully!", ""];

    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// ---------------------------------------------------------
// GET MOVIE BY ID
// ---------------------------------------------------------
function getMovieById($db, $movieId) {
    $stmt = $db->prepare("SELECT * FROM movies WHERE id=?");
    $stmt->execute([$movieId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
