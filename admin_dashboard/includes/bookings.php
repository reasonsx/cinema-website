<?php
function getBookings($db) {
    $stmt = $db->prepare("
        SELECT b.id, u.firstname,
            b.user_id,  u.lastname, u.email, s.id AS screening_id, s.start_time, s.end_time, m.title AS movie_title, r.name AS room_name, b.total_price
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN screenings s ON b.screening_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN screening_rooms r ON s.screening_room_id = r.id
        ORDER BY b.id DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookings as &$booking) {
        $stmtSeats = $db->prepare("
            SELECT seat_id, `row_number`, seat_number
            FROM booking_seats bs
            JOIN seats s ON bs.seat_id = s.id
            WHERE bs.booking_id = ?
            ORDER BY `row_number`, seat_number
        ");
        $stmtSeats->execute([$booking['id']]);
        $booking['seats'] = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);
    }

    return $bookings;
}

function addBooking($db, $data) {
    try {
        $userId = (int)$data['user_id'];
        $screeningId = (int)$data['screening_id'];
$seatIds = $data['seat_ids'] ?? '';
$seatIds = is_string($seatIds) ? explode(',', $seatIds) : $seatIds;
$seatIds = array_map('intval', $seatIds);
$seatIds = array_filter($seatIds, fn($id) => $id > 0);
$seatIds = array_unique($seatIds);

if (empty($seatIds)) throw new Exception("User, screening, and seats are required");



        if (!$userId || !$screeningId || empty($seatIds)) {
            throw new Exception("User, screening, and seats are required");
        }

        // Calculate total price
        $stmt = $db->prepare("SELECT seat_price FROM screening_rooms r JOIN screenings s ON r.id = s.screening_room_id WHERE s.id = ?");
        $stmt->execute([$screeningId]);
        $seatPrice = (float)$stmt->fetchColumn();
        $totalPrice = $seatPrice * count($seatIds);

        $stmt = $db->prepare("INSERT INTO bookings (user_id, screening_id, total_price) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $screeningId, $totalPrice]);
        $bookingId = $db->lastInsertId();

        $stmtInsert = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
        foreach ($seatIds as $seatId) {
            $stmtInsert->execute([$bookingId, (int)$seatId, $screeningId]);
        }

        return ['Booking added successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error adding booking: '.$e->getMessage()];
    }
}
function editBooking($db, $data) {
    try {
        $bookingId = (int)$data['booking_id'];
        $userId = (int)$data['user_id'];
        $screeningId = (int)$data['screening_id'];
        $seatIds = $data['seat_ids'] ?? '';

        // Normalize seat IDs
        $seatIds = is_string($seatIds) ? explode(',', $seatIds) : $seatIds;
        $seatIds = array_map('intval', $seatIds);
        $seatIds = array_filter($seatIds, fn($id) => $id > 0);
        $seatIds = array_unique($seatIds);

        if (!$bookingId || !$userId || !$screeningId || empty($seatIds)) {
            throw new Exception("Booking ID, user, screening, and seats are required");
        }

        // Calculate total price
        $stmt = $db->prepare("SELECT seat_price FROM screening_rooms r 
                              JOIN screenings s ON r.id = s.screening_room_id 
                              WHERE s.id = ?");
        $stmt->execute([$screeningId]);
        $seatPrice = (float)$stmt->fetchColumn();
        $totalPrice = $seatPrice * count($seatIds);

        // Update booking info
        $stmt = $db->prepare("UPDATE bookings SET user_id = ?, screening_id = ?, total_price = ? WHERE id = ?");
        $stmt->execute([$userId, $screeningId, $totalPrice, $bookingId]);

        // Delete old seats and insert new ones
        $db->prepare("DELETE FROM booking_seats WHERE booking_id = ?")->execute([$bookingId]);

        $stmtInsert = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
        foreach ($seatIds as $seatId) {
            $stmtInsert->execute([$bookingId, $seatId, $screeningId]);
        }

        return ['Booking updated successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error updating booking: ' . $e->getMessage()];
    }
}

function deleteBooking($db, $bookingId) {
    try {
        $bookingId = (int)$bookingId;
        if (!$bookingId) throw new Exception("Invalid booking ID");

        $db->prepare("DELETE FROM booking_seats WHERE booking_id = ?")->execute([$bookingId]);
        $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([$bookingId]);

        return ['Booking deleted successfully!', ''];
    } catch (Exception $e) {
        return ['', 'Error deleting booking: '.$e->getMessage()];
    }
}

$bookings = getBookings($db);
