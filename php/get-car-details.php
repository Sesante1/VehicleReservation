<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

$host = 'localhost';
$dbname = 'chatapp';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$carId = $_GET['id'] ?? null;

if (!$carId) {
    echo json_encode(['success' => false, 'message' => 'Car ID is required']);
    exit;
}

try {
    // Get car details - using 'id' column name to match your table structure
    $stmt = $db->prepare("SELECT * FROM cars WHERE id = ? AND user_id = ?");
    $stmt->execute([$carId, $_SESSION['user_id']]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$car) {
        echo json_encode(['success' => false, 'message' => 'Car not found or you do not have permission to edit it']);
        exit;
    }
    
    // Get car images - using 'car_id' column for foreign key reference
    $stmt = $db->prepare("SELECT id as image_id, image_path, is_primary FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, created_at ASC");
    $stmt->execute([$carId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get car documents - using 'car_id' column for foreign key reference
    $stmt = $db->prepare("SELECT id as document_id, document_type, image_path FROM car_documents WHERE car_id = ? ORDER BY document_type");
    $stmt->execute([$carId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add the car id as car_id for frontend compatibility
    $car['car_id'] = $car['id'];
    $car['images'] = $images;
    $car['documents'] = $documents;
    
    echo json_encode([
        'success' => true,
        'car' => $car
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>