<?php
session_start();
require '../php/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        c.id AS car_id,
        c.make,
        c.model,
        c.year,
        c.car_type,
        c.description,
        c.daily_rate,
        c.location,
        c.transmission,
        c.seats,
        c.features,
        c.available_from,
        c.available_until,
        c.is_active,
        ci.image_path,
        IFNULL(b.total_bookings, 0) AS bookings, 
        IFNULL(SUM(b.total_price), 0) AS earnings
    FROM cars c
    LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
    LEFT JOIN (
        SELECT car_id, COUNT(*) AS total_bookings, SUM(total_price) AS total_price
        FROM bookings 
        WHERE status = 'completed' -- Only count completed bookings for earnings
        GROUP BY car_id
    ) b ON c.id = b.car_id
    WHERE c.user_id = ?
    GROUP BY c.id, ci.image_path;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

header('Content-Type: application/json');
echo json_encode($cars);
?>
