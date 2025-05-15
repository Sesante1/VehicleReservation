<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to list a car']);
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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_SESSION['user_id'];

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

        $stmt = $db->prepare("INSERT INTO cars (user_id, make, model, year, car_type, description, daily_rate, 
                                            location, transmission, seats, features, available_from, available_until, 
                                            created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $userId,
            $make,
            $model,
            $year,
            $carType,
            $description,
            $dailyRate,
            $location,
            $transmission,
            $seats,
            $featuresJson,
            $availableFrom,
            $availableUntil
        ]);

        $carId = $db->lastInsertId();

        $imageCount = 0;
        $imagePaths = [];

        $uploadDir = __DIR__ . '/car-images/' . $carId . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES as $key => $file) {
            if (strpos($key, 'carImage') === 0 && $file['error'] === UPLOAD_ERR_OK) {
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid() . '.' . $fileExtension;

                $filePath = $uploadDir . $newFilename;
                $dbPath = $newFilename;

                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $stmt = $db->prepare("INSERT INTO car_images (car_id, image_path, is_primary, created_at) 
                                VALUES (?, ?, ?, NOW())");
                    $isPrimary = ($imageCount === 0) ? 1 : 0;
                    $stmt->execute([$carId, $dbPath, $isPrimary]);

                    $imageCount++;
                    $imagePaths[] = $dbPath;
                }
            }
        }

        if ($imageCount < 3) {
            // Rollback transaction if not enough images
            $db->rollBack();

            // Delete any uploaded images
            foreach ($imagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            echo json_encode(['success' => false, 'message' => 'Please upload at least 3 images']);
            exit;
        }

        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Car listed successfully',
            'car_id' => $carId
        ]);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
