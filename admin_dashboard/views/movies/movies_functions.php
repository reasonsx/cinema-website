<?php

// HELPER FUNCTION: RESIZE IMAGE
function resizeImage($tmpFile, $targetFile, $fileType, $maxWidth = 1200, $maxHeight = 1800)
{
    list($width, $height) = getimagesize($tmpFile);

    // Only resize if larger than limits
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return move_uploaded_file($tmpFile, $targetFile);
    }

    // Maintain aspect ratio
    $ratio = $width / $height;
    if ($maxWidth / $maxHeight > $ratio) {
        $newHeight = $maxHeight;
        $newWidth = $maxHeight * $ratio;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    }

    $newWidth = (int)round($newWidth);
    $newHeight = (int)round($newHeight);

    // Load source image
    if ($fileType === 'image/jpeg' || $fileType === 'image/pjpeg') {
        $srcImage = imagecreatefromjpeg($tmpFile);
    } elseif ($fileType === 'image/png') {
        $srcImage = imagecreatefrompng($tmpFile);
    } elseif ($fileType === 'image/gif') {
        $srcImage = imagecreatefromgif($tmpFile);
    } else {
        return false;
    }

    // Create destination image
    $dstImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency
    if ($fileType === 'image/png' || $fileType === 'image/gif') {
        imagecolortransparent($dstImage, imagecolorallocatealpha($dstImage, 0, 0, 0, 127));
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
    }

    // Resample
    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0,
        $newWidth, $newHeight, $width, $height);

    // Save output
    if ($fileType === 'image/jpeg' || $fileType === 'image/pjpeg') {
        imagejpeg($dstImage, $targetFile, 85);
    } elseif ($fileType === 'image/png') {
        imagepng($dstImage, $targetFile, 6);
    } elseif ($fileType === 'image/gif') {
        imagegif($dstImage, $targetFile);
    }

    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return true;
}


// ADD MOVIE
function addMovieHandler($db, $data, $files): array
{
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
        // Validate image BEFORE resizing
        list($w, $h) = getimagesize($files['poster']['tmp_name']);

        $maxWidth = 1200;
        $maxHeight = 1800;

        // Aspect ratio check: portrait only (e.g. 2:3 = 0.66)
        $ratio = $w / $h;
        if ($ratio < 0.4 || $ratio > 0.8) {
            return ["", "Invalid poster shape. The poster must be a tall, portrait-style image (recommended ratio ~2:3)."];
        }

        // Resolution check
        if ($w > $maxWidth || $h > $maxHeight) {
            return ["", "Poster resolution too large. Max allowed size is {$maxWidth}×{$maxHeight}px."];
        }

        $targetDir = __DIR__ . '/../../../images/';
        $publicPath = '/cinema-website/images/';
        $fileName = time() . '_' . basename($files['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (!resizeImage($files['poster']['tmp_name'], $targetFile, $fileType)) {
            return ["", "Failed to process poster image."];
        }

        $posterPath = $publicPath . $fileName;
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO movies (title, release_year, rating, genre, language, length, description, poster, trailer_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $release_year, $rating, $genre, $language, $length, $description, $posterPath, $trailer_url]);

        $movie_id = $db->lastInsertId();

        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

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

// EDIT MOVIE
function editMovieHandler($db, $data, $files): array
{
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

    if (isset($files['poster']) && $files['poster']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $files['poster']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            return ["", "Invalid poster file type."];
        }
        // Validate image BEFORE resizing
        list($w, $h) = getimagesize($files['poster']['tmp_name']);

        $maxWidth = 1200;
        $maxHeight = 1800;

        // Aspect ratio check: portrait only (e.g. 2:3 = 0.66)
        $ratio = $w / $h;
        if ($ratio < 0.4 || $ratio > 0.8) {
            return ["", "Invalid poster shape. The poster must be a tall, portrait-style image (recommended ratio ~2:3)."];
        }

        // Resolution check
        if ($w > $maxWidth || $h > $maxHeight) {
            return ["", "Poster resolution too large. Max allowed size is {$maxWidth}×{$maxHeight}px."];
        }

        $targetDir = __DIR__ . '/../../../images/';
        $publicPath = '/cinema-website/images/';
        $fileName = time() . '_' . basename($files['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (!resizeImage($files['poster']['tmp_name'], $targetFile, $fileType)) {
            return ["", "Failed to process poster image."];
        }

        $posterPath = $publicPath . $fileName;
    }

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

        $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?")->execute([$movie_id]);
        if (!empty($data['actors'])) {
            $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
            foreach ($data['actors'] as $actor_id) {
                $stmt->execute([$actor_id, $movie_id]);
            }
        }

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

// GET ALL MOVIES
function getMovies($db)
{
    $stmt = $db->query("SELECT * FROM movies ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// DELETE MOVIE
function deleteMovie($db, $movieId): array
{
    try {
        $db->prepare("DELETE FROM actorAppearIn WHERE movie_id=?")->execute([$movieId]);
        $db->prepare("DELETE FROM directorDirects WHERE movie_id=?")->execute([$movieId]);
        $db->prepare("DELETE FROM movies WHERE id=?")->execute([$movieId]);

        return ["Movie deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// GET MOVIE BY ID
function getMovieById($db, $movieId)
{
    $stmt = $db->prepare("SELECT * FROM movies WHERE id=?");
    $stmt->execute([$movieId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
