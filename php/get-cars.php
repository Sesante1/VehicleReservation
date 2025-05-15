<?php
require '../php/config.php';

$sql = "SELECT 
            cars.id AS car_id,
            cars.make,
            cars.model,
            cars.daily_rate,
            cars.location,
            cars.transmission,
            cars.seats,
            car_images.image_path
        FROM cars
        LEFT JOIN car_images ON cars.id = car_images.car_id AND car_images.is_primary = 1
        WHERE cars.is_active = 1";

$result = mysqli_query($conn, $sql);

$cars = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($cars);
?>
