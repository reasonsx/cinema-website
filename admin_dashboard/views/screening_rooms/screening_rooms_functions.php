<?php
// This file contains helper functions for managing screening rooms and their seats.

// Fetch all seats belonging to a screening room
function getSeatsByRoom(PDO $db, int $roomId): array {
    $stmt = $db->prepare("SELECT id, `row_number`, seat_number FROM seats WHERE screening_room_id = ? ORDER BY `row_number`, seat_number");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get capacity (total number of seats) for a screening room
function getRoomCapacity($db, $roomId) {
    $stmt = $db->prepare("SELECT COUNT(*) as total_seats FROM seats WHERE screening_room_id = ?");
    $stmt->execute([$roomId]);
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total_seats'];
}

// Get all screening rooms including calculated capacity
function getScreeningRooms($db) {
    $stmt = $db->prepare("SELECT * FROM screening_rooms ORDER BY id");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rooms as &$room) {
        $room['capacity'] = getRoomCapacity($db, $room['id']);
    }

    return $rooms;
}

// Edit a screening room including updating seats via grid or manual mode
function editScreeningRoom($db, $data) {
    try {
        $roomId = (int)$data['room_id'];
        $name = trim($data['name']);

        $stmt = $db->prepare("UPDATE screening_rooms SET name = ? WHERE id = ?");
        $stmt->execute([$name, $roomId]);

        $rowsInput = trim($data['rows'] ?? '');
        $seatsPerRowInput = trim($data['seats_per_row'] ?? '');
        $manualSeatsInput = trim($data['seats_text'] ?? '');
        $mode = $data['seat_edit_mode'] ?? '';

        if ($mode === 'grid' && $rowsInput !== '' && $seatsPerRowInput !== '') {
            $rows = (int)$rowsInput;
            $seatsPerRow = (int)$seatsPerRowInput;

            $db->prepare("DELETE FROM seats WHERE screening_room_id = ?")->execute([$roomId]);
            $stmtInsert = $db->prepare("INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES (?, ?, ?)");

            for ($r = 1; $r <= $rows; $r++) {
                $rowLetter = chr(64 + $r);
                for ($s = 1; $s <= $seatsPerRow; $s++) {
                    $stmtInsert->execute([$roomId, $rowLetter, $s]);
                }
            }
        } elseif ($mode === 'manual' && $manualSeatsInput !== '') {
            $db->prepare("DELETE FROM seats WHERE screening_room_id = ?")->execute([$roomId]);
            $stmtInsert = $db->prepare("INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES (?, ?, ?)");

            $seats = preg_split('/\s+/', $manualSeatsInput);
            foreach ($seats as $seat) {
                $seat = trim($seat);
                if ($seat === '') continue;

                $row = preg_replace('/[0-9]/', '', strtoupper($seat));
                $num = preg_replace('/[^0-9]/', '', $seat);

                if ($row && $num) {
                    $stmtInsert->execute([$roomId, $row, (int)$num]);
                }
            }
        }

        return ['Screening room updated successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error updating screening room: ' . $e->getMessage()];
    }
}

// Add a new screening room and optional seats via grid/manual mode
function addScreeningRoom($db, $data) {
    try {
        $name = trim($data['name']);
        if (!$name) throw new Exception("Room name is required");

        $stmt = $db->prepare("INSERT INTO screening_rooms (name) VALUES (?)");
        $stmt->execute([$name]);
        $roomId = $db->lastInsertId();

        $rows = (int)($data['rows'] ?? 0);
        $seatsPerRow = (int)($data['seats_per_row'] ?? 0);
        $manualSeats = trim($data['seats_text'] ?? '');
        $mode = $data['seat_edit_mode'] ?? '';

        if ($mode === 'grid' && $rows && $seatsPerRow) {
            $stmtInsert = $db->prepare("INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES (?, ?, ?)");
            for ($r = 1; $r <= $rows; $r++) {
                $rowLetter = chr(64 + $r);
                for ($s = 1; $s <= $seatsPerRow; $s++) {
                    $stmtInsert->execute([$roomId, $rowLetter, $s]);
                }
            }
        } elseif ($mode === 'manual' && $manualSeats) {
            $stmtInsert = $db->prepare("INSERT INTO seats (screening_room_id, `row_number`, seat_number) VALUES (?, ?, ?)");
            $seats = preg_split('/\s+/', $manualSeats);
            foreach ($seats as $seat) {
                $seat = trim($seat);
                if ($seat === '') continue;
                $row = preg_replace('/[0-9]/', '', strtoupper($seat));
                $num = preg_replace('/[^0-9]/', '', $seat);
                if ($row && $num) $stmtInsert->execute([$roomId, $row, (int)$num]);
            }
        }

        return ['Screening room added successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error adding screening room: ' . $e->getMessage()];
    }
}

// Delete a screening room and its seats
function deleteScreeningRoom($db, $roomId) {
    try {
        $roomId = (int)$roomId;
        if (!$roomId) throw new Exception("Invalid room ID");

        $db->prepare("DELETE FROM seats WHERE screening_room_id = ?")->execute([$roomId]);
        $db->prepare("DELETE FROM screening_rooms WHERE id = ?")->execute([$roomId]);

        return ['Screening room deleted successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error deleting screening room: ' . $e->getMessage()];
    }
}

$screeningRooms = getScreeningRooms($db);
