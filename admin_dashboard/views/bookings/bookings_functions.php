<?php

// Booking data handlers:
// Provides functions to retrieve, create, update, and delete bookings,
// including seat assignments and related screening/movie/room details.


// Retrieve all bookings with full details (admin dashboard use)
function getBookings($db)
{
    // Fetch main booking info from the view
    $stmt = $db->prepare("SELECT * FROM view_full_bookings ORDER BY booking_id DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch seats for each booking
    foreach ($bookings as &$booking) {
        $stmtSeats = $db->prepare("
            SELECT seat_id, `row_number`, seat_number
            FROM booking_seats bs
            JOIN seats s ON bs.seat_id = s.id
            WHERE bs.booking_id = ?
            ORDER BY `row_number`, seat_number
        ");
        $stmtSeats->execute([$booking['booking_id']]);
        $booking['seats'] = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);
    }

    return $bookings;
}


// Add a new booking with selected seats (handles seats + transaction safety)
function addBooking(PDO $db, $data)
{
    try {
        $db->beginTransaction(); // START TRANSACTION

        $userId = (int)$data['user_id'];
        $screeningId = (int)$data['screening_id'];
        $seatIds = $data['seat_ids'] ?? [];

        if (is_string($seatIds)) $seatIds = explode(',', $seatIds);
        $seatIds = array_map('intval', $seatIds);
        $seatIds = array_filter($seatIds, fn($id) => $id > 0);
        $seatIds = array_unique($seatIds);

        if (!$userId || !$screeningId || empty($seatIds)) {
            throw new Exception("User, screening, and seats are required");
        }

        $stmt = $db->prepare("
            SELECT seat_price
            FROM screening_rooms r
            JOIN screenings s ON r.id = s.screening_room_id
            WHERE s.id = ?
        ");
        $stmt->execute([$screeningId]);
        $seatPrice = (float)$stmt->fetchColumn();
        $totalPrice = $seatPrice * count($seatIds);

        $stmt = $db->prepare("INSERT INTO bookings (user_id, screening_id, total_price) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $screeningId, $totalPrice]);
        $bookingId = $db->lastInsertId();

        $stmtInsert = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
        foreach ($seatIds as $seatId) {
            $stmtInsert->execute([$bookingId, $seatId, $screeningId]);
        }

        $db->commit(); // COMMIT TRANSACTION
        return ['Booking added successfully!', ''];
    } catch (Exception $e) {
        $db->rollBack(); // ROLLBACK if anything fails
        return ['', 'Error adding booking: ' . $e->getMessage()];
    }
}

// Edit an existing booking and replace its seats
function editBooking(PDO $db, $data)
{
    try {
        $db->beginTransaction();

        $bookingId = (int)$data['booking_id'];
        $userId = (int)$data['user_id'];
        $screeningId = (int)$data['screening_id'];
        $seatIds = $data['seat_ids'] ?? [];

        if (is_string($seatIds)) $seatIds = explode(',', $seatIds);
        $seatIds = array_map('intval', $seatIds);
        $seatIds = array_filter($seatIds, fn($id) => $id > 0);
        $seatIds = array_unique($seatIds);

        if (!$bookingId || !$userId || !$screeningId || empty($seatIds)) {
            throw new Exception("Booking ID, user, screening, and seats are required");
        }

        $stmt = $db->prepare("
            SELECT seat_price
            FROM screening_rooms r
            JOIN screenings s ON r.id = s.screening_room_id
            WHERE s.id = ?
        ");
        $stmt->execute([$screeningId]);
        $seatPrice = (float)$stmt->fetchColumn();
        $totalPrice = $seatPrice * count($seatIds);

        $stmt = $db->prepare("UPDATE bookings SET user_id = ?, screening_id = ?, total_price = ? WHERE id = ?");
        $stmt->execute([$userId, $screeningId, $totalPrice, $bookingId]);

        $db->prepare("DELETE FROM booking_seats WHERE booking_id = ?")->execute([$bookingId]);

        $stmtInsert = $db->prepare("INSERT INTO booking_seats (booking_id, seat_id, screening_id) VALUES (?, ?, ?)");
        foreach ($seatIds as $seatId) {
            $stmtInsert->execute([$bookingId, $seatId, $screeningId]);
        }

        $db->commit();
        return ['Booking updated successfully!', ''];
    } catch (Exception $e) {
        $db->rollBack();
        return ['', 'Error updating booking: ' . $e->getMessage()];
    }
}


// Delete a booking and its associated seats.
function deleteBooking(PDO $db, $bookingId)
{
    try {
        $db->beginTransaction();

        $bookingId = (int)$bookingId;
        if (!$bookingId) throw new Exception("Invalid booking ID");

        $db->prepare("DELETE FROM booking_seats WHERE booking_id = ?")->execute([$bookingId]);
        $db->prepare("DELETE FROM bookings WHERE id = ?")->execute([$bookingId]);

        $db->commit();
        return ['Booking deleted successfully!', ''];
    } catch (Exception $e) {
        $db->rollBack();
        return ['', 'Error deleting booking: ' . $e->getMessage()];
    }
}


// Retrieve bookings for a specific user.
function getBookingsByUserId(PDO $db, int $userId): array
{

    // Fetch all main booking info
    $stmt = $db->prepare("
        SELECT 
            b.id AS booking_id,
            b.total_price,
            s.start_time,
            s.end_time,
            m.title AS movie_title,
            r.name AS room_name
        FROM bookings b
        JOIN screenings s ON b.screening_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN screening_rooms r ON r.id = s.screening_room_id
        WHERE b.user_id = ?
        ORDER BY s.start_time DESC
    ");
    $stmt->execute([$userId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch seats for each booking
    foreach ($bookings as &$booking) {

        $stmtSeats = $db->prepare("
            SELECT 
                s.row_number,
                s.seat_number
            FROM booking_seats bs
            JOIN seats s ON bs.seat_id = s.id
            WHERE bs.booking_id = ?
            ORDER BY s.row_number, s.seat_number
        ");
        $stmtSeats->execute([$booking['booking_id']]);
        $booking['seats'] = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);

        // Add ticket count convenience
        $booking['ticket_count'] = count($booking['seats']);
    }

    return $bookings;
}

$bookings = getBookings($db);
