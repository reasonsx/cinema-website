<?php

function getScreenings($db) {
    $stmt = $db->query("
        SELECT s.id, s.movie_id, s.screening_room_id, s.start_time, s.end_time,
               m.title AS movie_title, r.name AS room_name
        FROM screenings s
        JOIN movies m ON s.movie_id = m.id
        JOIN screening_rooms r ON s.screening_room_id = r.id
        ORDER BY s.start_time ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Check for conflicting screenings in the same room and time.
 */
function hasScreeningConflict($db, $roomId, $start, $end, $excludeId = null): bool {
    $query = "
        SELECT COUNT(*) FROM screenings
        WHERE screening_room_id = ?
          AND (
              (start_time <= ? AND end_time > ?) OR
              (start_time < ? AND end_time >= ?) OR
              (start_time >= ? AND end_time <= ?)
          )
    ";

    // If editing, exclude the current screening
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

function addScreening($db, $data) {
    try {
        // Fetch movie length
        $stmt = $db->prepare("SELECT length FROM movies WHERE id = ?");
        $stmt->execute([$data['movie_id']]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movie) {
            return ['', 'Movie not found.'];
        }

        $movieLength = $movie['length']; // in minutes

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);
        $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;

        // Check movie duration
        if ($duration < $movieLength) {
            return ['', 'End time must be at least the length of the movie (' . $movieLength . ' minutes).'];
        }

        // Check for conflicts in the same room
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM screenings 
            WHERE screening_room_id = ? 
              AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))
        ");
        $stmt->execute([$data['screening_room_id'], $data['start_time'], $data['start_time'], $data['end_time'], $data['end_time']]);
        if ($stmt->fetchColumn() > 0) {
            return ['', 'There is already a screening in this room at that time.'];
        }

        // Insert screening
        $stmt = $db->prepare("INSERT INTO screenings (movie_id, screening_room_id, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['movie_id'], $data['screening_room_id'], $data['start_time'], $data['end_time']]);

        return ['Screening added successfully!', ''];

    } catch (Exception $e) {
        return ['', 'Error adding screening: ' . $e->getMessage()];
    }
}

function editScreening($db, $data) {
    try {
        // Fetch movie length
        $stmt = $db->prepare("SELECT length FROM movies WHERE id = ?");
        $stmt->execute([$data['movie_id']]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movie) {
            return ['', 'Movie not found.'];
        }

        $movieLength = $movie['length'];

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);
        $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;

        // Check movie duration
        if ($duration < $movieLength) {
            return ['', 'End time must be at least the length of the movie (' . $movieLength . ' minutes).'];
        }

        // Check for conflicts in the same room (excluding this screening)
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM screenings 
            WHERE screening_room_id = ? 
              AND id != ?
              AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))
        ");
        $stmt->execute([$data['screening_room_id'], $data['screening_id'], $data['start_time'], $data['start_time'], $data['end_time'], $data['end_time']]);
        if ($stmt->fetchColumn() > 0) {
            return ['', 'There is already a screening in this room at that time.'];
        }

        // Update screening
        $stmt = $db->prepare("UPDATE screenings SET movie_id = ?, screening_room_id = ?, start_time = ?, end_time = ? WHERE id = ?");
        $stmt->execute([$data['movie_id'], $data['screening_room_id'], $data['start_time'], $data['end_time'], $data['screening_id']]);

        return ['Screening updated successfully!', ''];

    } catch (Exception $e) {
        return ['', 'Error updating screening: ' . $e->getMessage()];
    }
}


function deleteScreening($db, $id) {
    try {
        $stmt = $db->prepare("DELETE FROM screenings WHERE id = ?");
        $stmt->execute([$id]);
        return ['Screening deleted successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error deleting screening: ' . $e->getMessage()];
    }
}
