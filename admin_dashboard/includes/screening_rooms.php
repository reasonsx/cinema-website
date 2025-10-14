<?php
function getSeatsByRoom($db, $roomId) {
    // Add backticks around `row_number`
    $stmt = $db->prepare("SELECT `row_number`, seat_number FROM seats WHERE screening_room_id = ? ORDER BY `row_number`, seat_number");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getRoomCapacity($db, $roomId) {
    $stmt = $db->prepare("SELECT COUNT(*) as total_seats FROM seats WHERE screening_room_id = ?");
    $stmt->execute([$roomId]);
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total_seats'];
}

function getScreeningRooms($db) {
    $stmt = $db->prepare("SELECT * FROM screening_rooms ORDER BY id");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add dynamic capacity for each room
    foreach ($rooms as &$room) {
        $room['capacity'] = getRoomCapacity($db, $room['id']);
    }

    return $rooms;
}

function editScreeningRoom($db, $data) {
    try {
        $roomId = (int)$data['room_id'];
        $name = trim($data['name']);

        // Update room name only
        $stmt = $db->prepare("UPDATE screening_rooms SET name = ? WHERE id = ?");
        $stmt->execute([$name, $roomId]);

        // Seat inputs
        $rowsInput = isset($data['rows']) ? trim($data['rows']) : '';
        $seatsPerRowInput = isset($data['seats_per_row']) ? trim($data['seats_per_row']) : '';
        $manualSeatsInput = isset($data['seats_text']) ? trim($data['seats_text']) : '';
        $mode = $data['seat_edit_mode'] ?? '';

        if ($mode === 'grid' && $rowsInput !== '' && $seatsPerRowInput !== '') {
            // GRID MODE
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
            // MANUAL MODE
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


$screeningRooms = getScreeningRooms($db);
