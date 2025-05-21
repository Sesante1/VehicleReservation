<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

if (!isset($_GET['car_id'])) {
    echo json_encode(['error' => 'Car ID not provided']);
    exit;
}

$car_id = $_GET['car_id'];

try {
    // Get car details with user information
    $sql = "SELECT 
                c.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.img as user_img
            FROM cars c
            INNER JOIN users u ON c.user_id = u.user_id
            WHERE c.id = :car_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':car_id', $car_id);
    $stmt->execute();
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$car) {
        echo json_encode(['error' => 'Car not found']);
        exit;
    }
    
    // Get car images
    $sql = "SELECT image_path, is_primary FROM car_images WHERE car_id = :car_id ORDER BY is_primary DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':car_id', $car_id);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get car documents (OR and CR)
    $sql = "SELECT document_type, image_path FROM car_documents WHERE car_id = :car_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':car_id', $car_id);
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'car' => $car,
        'images' => $images,
        'documents' => $documents
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>