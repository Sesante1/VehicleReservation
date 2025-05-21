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
} catch(PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST method allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['car_id']) || !isset($input['action'])) {
    echo json_encode(['error' => 'Car ID and action required']);
    exit;
}

$car_id = $input['car_id'];
$action = $input['action'];
$admin_notes = isset($input['admin_notes']) ? $input['admin_notes'] : '';

// Validate action
if (!in_array($action, ['approve', 'decline'])) {
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Update car verification status
    if ($action === 'approve') {
        $verified_status = 'approved';
        $is_active = 1;
    } else {
        $verified_status = 'declined';
        $is_active = 0;
    }
    
    $sql = "UPDATE cars SET 
                verified = :verified_status,
                is_active = :is_active,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = :car_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':verified_status', $verified_status);
    $stmt->bindParam(':is_active', $is_active);
    $stmt->bindParam(':car_id', $car_id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Car not found or already processed');
    }
    
    // Log the admin action (optional - you can create an admin_actions table)
    /*
    $sql = "INSERT INTO admin_actions (car_id, action, admin_notes, created_at) 
            VALUES (:car_id, :action, :admin_notes, CURRENT_TIMESTAMP)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':car_id', $car_id);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':admin_notes', $admin_notes);
    $stmt->execute();
    */
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Car has been $action" . ($action === 'approve' ? 'd' : 'd') . " successfully"
    ]);
    
} catch(Exception $e) {
    $pdo->rollback();
    echo json_encode(['error' => 'Failed to update car: ' . $e->getMessage()]);
}
?>