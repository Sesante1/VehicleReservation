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
        ci.image_path,
        u.fname,
        u.lname,
        u.img AS user_image
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    JOIN users u ON b.user_id = u.user_id 
    LEFT JOIN car_images ci ON c.id = ci.car_id AND ci.is_primary = 1
    WHERE c.user_id = ? AND b.status = 'pending'
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        'booking_id' => $row['booking_id'],
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date'],
        'total_price' => $row['total_price'],
        'status' => ucfirst($row['booking_status']),
        'make' => $row['make'],
        'model' => $row['model'],
        'year' => $row['year'],
        'car_id' => $row['car_id'],
        'image_path' => $row['image_path'],
        'owner_id' => $row['owner_id'],
        'owner_unique_id' => $row['owner_unique_id'],
        'user_name' => $row['fname'] . ' ' . strtoupper(substr($row['lname'], 0, 1)) . '.',
        'user_image' => $row['user_image']
    ];
}

header('Content-Type: application/json');
echo json_encode($bookings);
?>
