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



$screeningRooms = getScreeningRooms($db);
