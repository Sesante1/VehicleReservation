<?php
session_start();
include '../php/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$bookingId = $data['booking_id'];
$reason = trim($data['reason']);
$userId = $_SESSION['user_id'];

$sql = "UPDATE bookings SET status = 'cancelled', cancel_reason = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $reason, $bookingId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
