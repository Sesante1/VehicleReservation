<?php
session_start();

require '../php/config.php'; 

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        b.id AS booking_id,
        b.start_date,
        b.end_date,
        b.total_price,
        b.status AS booking_status,
        c.id AS car_id,
        c.make,
        c.model,
        c.year,
        c.user_id AS owner_id,
        u.unique_id AS owner_unique_id,  
        ci.image_path
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    JOIN users u ON c.user_id = u.user_id 
    LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
    WHERE b.user_id = ?
    AND b.status IN ('completed')
    ORDER BY b.created_at DESC
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
