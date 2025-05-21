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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $carId = $_POST['car_id'] ?? null;
    
    if (!$carId) {
        echo json_encode(['success' => false, 'message' => 'Car ID is required']);
        exit;
    }
    
    // Check if car exists and belongs to user - using 'id' column
    $stmt = $db->prepare("SELECT id FROM cars WHERE id = ? AND user_id = ?");
    $stmt->execute([$carId, $userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Car not found or you do not have permission to edit it']);
        exit;
    }
    
    $requiredFields = ['make', 'model', 'year', 'car_type', 'description', 'daily_rate', 'location', 'transmission', 'seats'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
            exit;
        }
    }
    
    $make = htmlspecialchars($_POST['make']);
    $model = htmlspecialchars($_POST['model']);
    $year = (int)$_POST['year'];
    $carType = htmlspecialchars($_POST['car_type']);
    $description = htmlspecialchars($_POST['description']);
    $dailyRate = (float)$_POST['daily_rate'];
    $location = htmlspecialchars($_POST['location']);
    $transmission = htmlspecialchars($_POST['transmission']);
    $seats = htmlspecialchars($_POST['seats']);
    
    $availableFrom = $_POST['available_from'] ? $_POST['available_from'] : date('Y-m-d');
    $availableUntil = !empty($_POST['available_until']) ? $_POST['available_until'] : NULL;
    
    $features = [];
    $possibleFeatures = [
        'air-condition',
        'navigation-system',
        'heated-seats',
        'apple-carplay',
        'bluetooth',
        'leather-seats',
        'camera',
        'android',
        'cruise-control',
        'sunroof',
        'keyless',
        'sound-system'
    ];
    
    foreach ($possibleFeatures as $feature) {
        if (isset($_POST[$feature])) {
            $features[] = $feature;
        }
    }
    
    $featuresJson = json_encode($features);
    
    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("UPDATE cars SET 
                            make = ?, model = ?, year = ?, car_type = ?, description = ?, 
                            daily_rate = ?, location = ?, transmission = ?, seats = ?, 
                            features = ?, available_from = ?, available_until = ?, 
                            updated_at = NOW() 
                            WHERE id = ? AND user_id = ?");
        
        $stmt->execute([
            $make, $model, $year, $carType, $description,
            $dailyRate, $location, $transmission, $seats,
            $featuresJson, $availableFrom, $availableUntil,
            $carId, $userId
        ]);
        
        if (isset($_POST['imagesToDelete'])) {
            $imagesToDelete = json_decode($_POST['imagesToDelete'], true);
            foreach ($imagesToDelete as $imageId) {

                $stmt = $db->prepare("SELECT image_path FROM car_images WHERE id = ? AND car_id = ?");
                $stmt->execute([$imageId, $carId]);
                $imageInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($imageInfo) {

                    $stmt = $db->prepare("DELETE FROM car_images WHERE id = ? AND car_id = ?");
                    $stmt->execute([$imageId, $carId]);
                    
                    $filePath = __DIR__ . '/car-images/' . $carId . '/' . $imageInfo['image_path'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }
        
        $carImageDir = __DIR__ . '/car-images/' . $carId . '/';
        if (!file_exists($carImageDir)) {
            mkdir($carImageDir, 0777, true);
        }
        
        $newImageCount = 0;
        foreach ($_FILES as $key => $file) {
            if (strpos($key, 'carImage') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid() . '.' . $fileExtension;
                $filePath = $carImageDir . $newFilename;
                
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $stmt = $db->prepare("SELECT COUNT(*) as count FROM car_images WHERE car_id = ? AND is_primary = 1");
                    $stmt->execute([$carId]);
                    $primaryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    $isPrimary = ($primaryCount == 0 && $newImageCount == 0) ? 1 : 0;
                    
                    $stmt = $db->prepare("INSERT INTO car_images (car_id, image_path, is_primary, created_at) 
                                        VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$carId, $newFilename, $isPrimary]);
                    $newImageCount++;
                }
            }
        }
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM car_images WHERE car_id = ?");
        $stmt->execute([$carId]);
        $totalImages = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($totalImages < 3) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Please ensure you have at least 3 images']);
            exit;
        }
        
        $documentDir = __DIR__ . '/documents/' . $carId . '/';
        if (!file_exists($documentDir)) {
            mkdir($documentDir, 0777, true);
        }
        
        if (isset($_FILES['orImage']) && $_FILES['orImage']['error'] === UPLOAD_ERR_OK) {
            $stmt = $db->prepare("SELECT image_path FROM car_documents WHERE car_id = ? AND document_type = 'OR'");
            $stmt->execute([$carId]);
            $existingOr = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingOr) {
                $oldFilePath = $documentDir . $existingOr['image_path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                
                $stmt = $db->prepare("DELETE FROM car_documents WHERE car_id = ? AND document_type = 'OR'");
                $stmt->execute([$carId]);
            }
            
            $orFile = $_FILES['orImage'];
            $orExtension = pathinfo($orFile['name'], PATHINFO_EXTENSION);
            $orFilename = 'OR_' . $carId . '_' . time() . '.' . $orExtension;
            $orFilePath = $documentDir . $orFilename;
            
            if (move_uploaded_file($orFile['tmp_name'], $orFilePath)) {
                $stmt = $db->prepare("INSERT INTO car_documents (car_id, document_type, image_path, created_at) 
                                    VALUES (?, 'OR', ?, NOW())");
                $stmt->execute([$carId, $orFilename]);
            }
        }
        
        // Handle CR document update
        if (isset($_FILES['crImage']) && $_FILES['crImage']['error'] === UPLOAD_ERR_OK) {
            $stmt = $db->prepare("SELECT image_path FROM car_documents WHERE car_id = ? AND document_type = 'CR'");
            $stmt->execute([$carId]);
            $existingCr = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingCr) {
                $oldFilePath = $documentDir . $existingCr['image_path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                
                $stmt = $db->prepare("DELETE FROM car_documents WHERE car_id = ? AND document_type = 'CR'");
                $stmt->execute([$carId]);
            }
            
            // Upload new CR document
            $crFile = $_FILES['crImage'];
            $crExtension = pathinfo($crFile['name'], PATHINFO_EXTENSION);
            $crFilename = 'CR_' . $carId . '_' . time() . '.' . $crExtension;
            $crFilePath = $documentDir . $crFilename;
            
            if (move_uploaded_file($crFile['tmp_name'], $crFilePath)) {
                $stmt = $db->prepare("INSERT INTO car_documents (car_id, document_type, image_path, created_at) 
                                    VALUES (?, 'CR', ?, NOW())");
                $stmt->execute([$carId, $crFilename]);
            }
        }
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Car listing updated successfully',
            'car_id' => $carId
        ]);
        
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>