<?php
// This file contains helper functions for managing screenings.

// Fetch all screenings with movie and room details
function getScreenings(PDO $db): array {
    $stmt = $db->query("
        SELECT * FROM view_screenings_full
        ORDER BY start_time ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if a screening conflicts with an existing one in the same room
function hasScreeningConflict(PDO $db, int $roomId, string $start, string $end, ?int $excludeId = null): bool {
    $query = "
        SELECT COUNT(*) FROM screenings
        WHERE screening_room_id = ?
          AND (
              (start_time <= ? AND end_time > ?) OR
              (start_time < ? AND end_time >= ?) OR
              (start_time >= ? AND end_time <= ?)
          )
    ";

    if ($excludeId) {
        $query .= " AND id != ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$roomId, $start, $start, $end, $end, $start, $end, $excludeId]);
    } else {
        $stmt = $db->prepare($query);
        $stmt->execute([$roomId, $start, $start, $end, $end, $start, $end]);
    }

    return $stmt->fetchColumn() > 0;
}

// Add a new screening
function addScreening(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("SELECT length FROM movies WHERE id = ?");
        $stmt->execute([$data['movie_id']]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movie) return ['', 'Movie not found.'];

        $movieLength = (int)$movie['length'];

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);
        $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;

        if ($duration < $movieLength) {
            return ['', 'End time must be at least the length of the movie (' . $movieLength . ' minutes).'];
        }

        if (hasScreeningConflict($db, (int)$data['screening_room_id'], $data['start_time'], $data['end_time'])) {
            return ['', 'There is already a screening in this room at that time.'];
        }

        $stmt = $db->prepare("INSERT INTO screenings (movie_id, screening_room_id, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['movie_id'], $data['screening_room_id'], $data['start_time'], $data['end_time']]);

        return ['Screening added successfully!', ''];

    } catch (Exception $e) {
        return ['', 'Error adding screening: ' . $e->getMessage()];
    }
}

// Edit an existing screening
function editScreening(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("SELECT length FROM movies WHERE id = ?");
        $stmt->execute([$data['movie_id']]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movie) return ['', 'Movie not found.'];

        $movieLength = (int)$movie['length'];

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);
        $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;

        if ($duration < $movieLength) {
            return ['', 'End time must be at least the length of the movie (' . $movieLength . ' minutes).'];
        }

        if (hasScreeningConflict($db, (int)$data['screening_room_id'], $data['start_time'], $data['end_time'], (int)$data['screening_id'])) {
            return ['', 'There is already a screening in this room at that time.'];
        }

        $stmt = $db->prepare("UPDATE screenings SET movie_id = ?, screening_room_id = ?, start_time = ?, end_time = ? WHERE id = ?");
        $stmt->execute([$data['movie_id'], $data['screening_room_id'], $data['start_time'], $data['end_time'], $data['screening_id']]);

        return ['Screening updated successfully!', ''];

    } catch (Exception $e) {
        return ['', 'Error updating screening: ' . $e->getMessage()];
    }
}

// Delete a screening
function deleteScreening(PDO $db, int $id): array {
    try {
        $stmt = $db->prepare("DELETE FROM screenings WHERE id = ?");
        $stmt->execute([$id]);
        return ['Screening deleted successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error deleting screening: ' . $e->getMessage()];
    }
}

// Get a specific screening by ID
function getScreeningById(PDO $db, int $screeningId): ?array {
    $stmt = $db->prepare("
        SELECT * FROM view_screenings_full
        WHERE id = ?
    ");
    $stmt->execute([$screeningId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

$screenings = getScreenings($db);