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

function addScreening($db, $data) {
    try {
        $stmt = $db->prepare("INSERT INTO screenings (movie_id, screening_room_id, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['movie_id'], $data['screening_room_id'], $data['start_time'], $data['end_time']]);
        return ['Screening added successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error adding screening: ' . $e->getMessage()];
    }
}

function editScreening($db, $data) {
    try {
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
