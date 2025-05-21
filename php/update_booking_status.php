<?php
header('Content-Type: application/json');

include '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $booking_id = $_POST['booking_id'] ?? null;
    $new_status = $_POST['status'] ?? null;

    if (!$booking_id || !$new_status) {
        echo json_encode(['success' => false, 'message' => 'Missing booking ID or status']);
        exit;
    }

    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>