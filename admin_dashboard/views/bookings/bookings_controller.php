<?php
require_once __DIR__ . '/../../../backend/connection.php';
require_once __DIR__ . '/bookings_functions.php';
require_once __DIR__ . '/../users/users_functions.php';
require_once __DIR__ . '/../screenings/screenings_functions.php';

// Load all data needed by the view
$bookings = getBookings($db);
$users = getUsers($db);
$screenings = getScreenings($db);

// Prepare seats per screening
$seatsByScreening = [];
$occupiedSeatsByScreening = [];

foreach ($screenings as $s) {
    $stmt = $db->prepare("
    SELECT `id`, `row_number`, `seat_number`
    FROM `seats`
    WHERE `screening_room_id` = ?
    ORDER BY `row_number`, `seat_number`
");
    $stmt->execute([$s['screening_room_id']]);
    $seatsByScreening[$s['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtOcc = $db->prepare("
        SELECT seat_id
        FROM booking_seats
        WHERE screening_id = ?
    ");
    $stmtOcc->execute([$s['id']]);
    $occupiedSeatsByScreening[$s['id']] = array_column($stmtOcc->fetchAll(PDO::FETCH_ASSOC), 'seat_id');
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_booking'])) {
        [$success, $error] = addBooking($db, $_POST);
    }

    if (isset($_POST['edit_booking'])) {
        [$success, $error] = editBooking($db, $_POST);
    }

    if (isset($_POST['delete_booking'])) {
        [$success, $error] = deleteBooking($db, $_POST['delete_booking']);
    }

    header("Location: /cinema-website/admin_dashboard/admin_dashboard.php?view=bookings&message=" . urlencode($success ?: $error));
    exit;
}
