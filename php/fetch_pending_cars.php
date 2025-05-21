<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'chatapp';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to fetch pending cars with user information
$sql = "SELECT 
            c.id as car_id,
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
            c.verified,
            c.created_at,
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            u.img as user_img
        FROM cars c
        INNER JOIN users u ON c.user_id = u.user_id
        WHERE c.verified = 'pending'";

if (!empty($search)) {
    $sql .= " AND (u.first_name LIKE :search 
              OR u.last_name LIKE :search 
              OR c.make LIKE :search 
              OR c.model LIKE :search)";
}

$sql .= " ORDER BY c.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);

    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
    }

    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $cars]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
