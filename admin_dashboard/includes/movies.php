<?php

// This file contains helper functions for adding, retrieving, updating, and deleting movie records.

// Add a new movie to the database.
function addMovieHandler($db, $data, $files): array {
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $genre = trim($data['genre']);
    $language = trim($data['language']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $posterPath = '';
    $trailer_url = trim($data['trailer_url']);

    // Upload poster image.
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
        // Insert movie.
        $stmt = $db->prepare("INSERT INTO movies (title, release_year, rating, genre, language, length, description, poster, trailer_url)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $release_year, $rating, $genre, $language, $length, $description, $posterPath, $trailer_url]);

        $movie_id = $db->lastInsertId();

        // Add actor links.
        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

        // Add director links.
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

// Retrieve all movies.
function getMovies($db) {
    $stmt = $db->prepare("SELECT * FROM movies ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Delete a movie and related links.
function deleteMovie($db, $movieId): array {
    try {
        $stmt = $db->prepare("DELETE FROM actorAppearIn WHERE movie_id = ?");
        $stmt->execute([$movieId]);

        $stmt = $db->prepare("DELETE FROM directorDirects WHERE movie_id = ?");
        $stmt->execute([$movieId]);

        $stmt = $db->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);

        return ["Movie deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// Update movie information.
function editMovieHandler($db, $data, $files): array {
    $movie_id = intval($data['movie_id']);
    $title = trim($data['title']);
    $release_year = trim($data['release_year']);
    $rating = trim($data['rating']);
    $genre = trim($data['genre']);
    $language = trim($data['language']);
    $length = trim($data['length']);
    $description = trim($data['description']);
    $trailer_url = trim($data['trailer_url'] ?? '');
    $posterPath = null;

    // Upload new poster.
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
        if ($posterPath) {
            $stmt = $db->prepare("UPDATE movies SET title=?, release_year=?, rating=?, genre=?, language=?, length=?, description=?, poster=?, trailer_url=? WHERE id=?");
            $stmt->execute([$title, $release_year, $rating, $genre, $language, $length, $description, $posterPath, $trailer_url, $movie_id]);
        } else {
            $stmt = $db->prepare("UPDATE movies SET title=?, release_year=?, rating=?, genre=?, language=?, length=?, description=?, trailer_url=? WHERE id=?");
            $stmt->execute([$title, $release_year, $rating, $genre, $language, $length, $description, $trailer_url, $movie_id]);
        }

        // Reset actor links.
        $stmt = $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?");
        $stmt->execute([$movie_id]);

        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                if (trim($actor_id) !== '') {
                    $stmt->execute([$actor_id, $movie_id]);
                }
            }
        }

        // Reset director links.
        $stmt = $db->prepare("DELETE FROM directorDirects WHERE movie_id=?");
        $stmt->execute([$movie_id]);

        if (!empty($data['directors'])) {
            $stmt = $db->prepare("INSERT INTO directorDirects (director_id, movie_id) VALUES (?, ?)");
            foreach ($data['directors'] as $director_id) {
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

// Retrieve a single movie by ID.
function getMovieById($db, $movieId) {
    $stmt = $db->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
