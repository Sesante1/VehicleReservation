<?php
// ob_start();
// session_start();
// require_once '../php/config.php';

// header('Content-Type: application/json');

// if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_availability'])) {
//     $car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
//     $pickup_date = filter_input(INPUT_GET, 'pickup_date', FILTER_SANITIZE_STRING);
//     $return_date = filter_input(INPUT_GET, 'return_date', FILTER_SANITIZE_STRING);
    
//     if (!$car_id || !$pickup_date || !$return_date) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
//         exit;
//     }
    
//     $pickup_date = date('Y-m-d', strtotime($pickup_date));
//     $return_date = date('Y-m-d', strtotime($return_date));
    
//     $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
    
//     ob_clean();
//     echo json_encode([
//         'success' => true,
//         'available' => $isAvailable,
//         'message' => $isAvailable ? 'Car is available for the selected dates' : 'This car is not available for the selected dates'
//     ]);
//     exit;
// }

// // Check user verification status endpoint
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_verification'])) {
//     if (!isset($_SESSION['user_id'])) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'You must be logged in'
//         ]);
//         exit;
//     }

//     $user_id = $_SESSION['user_id'];
//     $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $user = $result->fetch_assoc();

//     if (!$user) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'User not found'
//         ]);
//         exit;
//     }

//     ob_clean();
//     echo json_encode([
//         'success' => true,
//         'verified_status' => $user['verified'],
//         'is_verified' => ($user['verified'] === 'verified')
//     ]);
//     exit;
// }

// // Record agreement endpoint
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_agreement'])) {
//     if (!isset($_SESSION['user_id'])) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'You must be logged in to agree to terms'
//         ]);
//         exit;
//     }
    
//     $user_id = $_SESSION['user_id'];
//     $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING) ?: '1.0';
//     $ip_address = $_SERVER['REMOTE_ADDR'];
    
//     try {
//         $stmt = $conn->prepare("
//             SELECT id FROM user_agreements 
//             WHERE user_id = ? AND version = ?
//         ");
//         $stmt->bind_param("is", $user_id, $version);
//         $stmt->execute();
//         $result = $stmt->get_result();
        
//         if ($result->num_rows > 0) {
//             ob_clean();
//             echo json_encode([
//                 'success' => true,
//                 'message' => 'Agreement already recorded',
//                 'existing' => true
//             ]);
//         } else {
//             $stmt = $conn->prepare("
//                 INSERT INTO user_agreements (user_id, version, ip_address) 
//                 VALUES (?, ?, ?)
//             ");
//             $stmt->bind_param("iss", $user_id, $version, $ip_address);
//             $stmt->execute();
            
//             ob_clean();
//             echo json_encode([
//                 'success' => true,
//                 'message' => 'Agreement recorded successfully',
//                 'agreement_id' => $conn->insert_id
//             ]);
//         }
//         exit;
//     } catch (Exception $e) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'Failed to record agreement: ' . $e->getMessage()
//         ]);
//         exit;
//     }
// }

// // Booking process
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (!isset($_SESSION['user_id'])) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'You must be logged in to book a car'
//         ]);
//         exit;
//     }

//     $user_id = $_SESSION['user_id'];
//     $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $user = $result->fetch_assoc();

//     if (!$user) {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'User not found'
//         ]);
//         exit;
//     }

//     if ($user['verified'] !== 'verified') {
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'Please complete your account setup and verification before booking a car.',
//             'verification_required' => true,
//             'verification_status' => $user['verified']
//         ]);
//         exit;
//     }

//     $car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
//     $pickup_date = filter_input(INPUT_POST, 'pickup_date', FILTER_SANITIZE_STRING);
//     $return_date = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
//     $total_price = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
//     $has_agreed = filter_input(INPUT_POST, 'has_agreed', FILTER_VALIDATE_BOOLEAN);

//     if (!$car_id) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'Invalid car selected']);
//         exit;
//     }

//     if (!$pickup_date) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'Invalid pickup date']);
//         exit;
//     }

//     if (!$return_date) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'Invalid return date']);
//         exit;
//     }

//     if (!$total_price) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'Invalid total price']);
//         exit;
//     }
    
//     if (!$has_agreed) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'You must agree to the terms and conditions']);
//         exit;
//     }

//     $pickup_date = date('Y-m-d', strtotime($pickup_date));
//     $return_date = date('Y-m-d', strtotime($return_date));

//     $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
    
//     if (!$isAvailable) {
//         ob_clean();
//         echo json_encode(['success' => false, 'message' => 'This car is not available for the selected dates']);
//         exit;
//     }

//     mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//     $conn->begin_transaction();

//     try {

//         $stmt = $conn->prepare("
//             INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status) 
//             VALUES (?, ?, ?, ?, ?, 'pending')
//         ");
//         $stmt->bind_param("iissd", $car_id, $user_id, $pickup_date, $return_date, $total_price);
//         $stmt->execute();

//         $booking_id = $conn->insert_id;

//         $stmt = $conn->prepare("UPDATE cars SET status = 'Booked' WHERE id = ?");
//         $stmt->bind_param("i", $car_id);
//         $stmt->execute();

//         $version = '1.0';
//         $ip_address = $_SERVER['REMOTE_ADDR'];
        
//         $stmt = $conn->prepare("
//             SELECT id FROM user_agreements 
//             WHERE user_id = ? AND version = ?
//         ");
//         $stmt->bind_param("is", $user_id, $version);
//         $stmt->execute();
//         $result = $stmt->get_result();
        
//         if ($result->num_rows == 0) {
//             $stmt = $conn->prepare("
//                 INSERT INTO user_agreements (user_id, version, ip_address) 
//                 VALUES (?, ?, ?)
//             ");
//             $stmt->bind_param("iss", $user_id, $version, $ip_address);
//             $stmt->execute();
//         }

//         $conn->commit();

//         ob_clean();
//         echo json_encode([
//             'success' => true,
//             'message' => 'Car booked successfully!',
//             'booking_id' => $booking_id
//         ]);
//     } catch (Exception $e) {
//         $conn->rollback();
//         ob_clean();
//         echo json_encode([
//             'success' => false,
//             'message' => 'An error occurred while booking the car: ' . $e->getMessage()
//         ]);
//     }

//     exit;
// }

// function checkCarAvailability($conn, $car_id, $pickup_date, $return_date) {
//     $stmt = $conn->prepare("
//         SELECT COUNT(*) as booking_count 
//         FROM bookings 
//         WHERE car_id = ? 
//         AND status != 'cancelled' 
//         AND ((start_date <= ? AND end_date >= ?) 
//             OR (start_date <= ? AND end_date >= ?) 
//             OR (start_date >= ? AND end_date <= ?))
//     ");
//     $stmt->bind_param("issssss", $car_id, $return_date, $pickup_date, $pickup_date, $pickup_date, $pickup_date, $return_date);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $row = $result->fetch_assoc();
    
//     return $row['booking_count'] == 0;
// }

// header('Location: /');
// exit;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

function sendJsonResponse($data) {
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function handleError($message, $code = 500) {
    http_response_code($code);
    sendJsonResponse([
        'success' => false,
        'message' => $message,
        'error_code' => $code
    ]);
}

$config_paths = [
    __DIR__ . '/../php/config.php',
    __DIR__ . '/php/config.php',
    __DIR__ . '/config/config.php',
    __DIR__ . '/includes/config.php',
    dirname(__DIR__) . '/config.php'
];

$config_loaded = false;
foreach ($config_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $config_loaded = true;
        break;
    }
}

if (!$config_loaded) {
    handleError('Configuration file not found. Please check your project structure.', 500);
}

if (!isset($conn) || !$conn) {
    handleError('Database connection not available. Please check your database configuration.', 500);
}

try {
    $conn->ping();
} catch (Exception $e) {
    handleError('Database connection failed: ' . $e->getMessage(), 500);
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function getClientIpAddress() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_availability'])) {
    $car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
    $pickup_date = filter_input(INPUT_GET, 'pickup_date', FILTER_SANITIZE_STRING);
    $return_date = filter_input(INPUT_GET, 'return_date', FILTER_SANITIZE_STRING);
    
    if (!$car_id || !$pickup_date || !$return_date) {
        handleError('Missing required parameters: car_id, pickup_date, and return_date are required', 400);
    }

    $pickup_date = date('Y-m-d', strtotime($pickup_date));
    $return_date = date('Y-m-d', strtotime($return_date));
    
    if (!validateDate($pickup_date) || !validateDate($return_date)) {
        handleError('Invalid date format provided', 400);
    }
    
    if (strtotime($pickup_date) >= strtotime($return_date)) {
        handleError('Return date must be after pickup date', 400);
    }
    
    $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
    
    sendJsonResponse([
        'success' => true,
        'available' => $isAvailable,
        'message' => $isAvailable ? 'Car is available for the selected dates' : 'This car is not available for the selected dates'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_verification'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        handleError('You must be logged in', 401);
    }

    $user_id = intval($_SESSION['user_id']);
    
    try {
        $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            handleError('User not found', 404);
        }

        sendJsonResponse([
            'success' => true,
            'verified_status' => $user['verified'],
            'is_verified' => ($user['verified'] === 'verified')
        ]);
    } catch (Exception $e) {
        handleError('Database error: ' . $e->getMessage(), 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_agreement'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        handleError('You must be logged in to agree to terms', 401);
    }
    
    $user_id = intval($_SESSION['user_id']);
    $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING) ?: '1.0';
    $ip_address = getClientIpAddress();
    
    try {

        $stmt = $conn->prepare("SELECT id FROM user_agreements WHERE user_id = ? AND version = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("is", $user_id, $version);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            sendJsonResponse([
                'success' => true,
                'message' => 'Agreement already recorded',
                'existing' => true
            ]);
        }
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO user_agreements (user_id, version, ip_address, created_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("iss", $user_id, $version, $ip_address);
        $stmt->execute();
        $agreement_id = $conn->insert_id;
        $stmt->close();
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Agreement recorded successfully',
            'agreement_id' => $agreement_id
        ]);
        
    } catch (Exception $e) {
        handleError('Failed to record agreement: ' . $e->getMessage(), 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['record_agreement'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        handleError('You must be logged in to book a car', 401);
    }

    $user_id = intval($_SESSION['user_id']);
    
    try {
        $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            handleError('User not found', 404);
        }

        if ($user['verified'] !== 'verified') {
            sendJsonResponse([
                'success' => false,
                'message' => 'Please complete your account setup and verification before booking a car.',
                'verification_required' => true,
                'verification_status' => $user['verified']
            ]);
        }

        $car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
        $pickup_date = filter_input(INPUT_POST, 'pickup_date', FILTER_SANITIZE_STRING);
        $return_date = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
        $total_price = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
        $has_agreed = filter_input(INPUT_POST, 'has_agreed', FILTER_VALIDATE_BOOLEAN);

        if (!$car_id) {
            handleError('Invalid car selected', 400);
        }
        if (!$pickup_date) {
            handleError('Invalid pickup date', 400);
        }
        if (!$return_date) {
            handleError('Invalid return date', 400);
        }
        if (!$total_price || $total_price <= 0) {
            handleError('Invalid total price', 400);
        }
        if (!$has_agreed) {
            handleError('You must agree to the terms and conditions', 400);
        }

        $pickup_date = date('Y-m-d', strtotime($pickup_date));
        $return_date = date('Y-m-d', strtotime($return_date));
        
        if (!validateDate($pickup_date) || !validateDate($return_date)) {
            handleError('Invalid date format', 400);
        }
        
        if (strtotime($pickup_date) >= strtotime($return_date)) {
            handleError('Return date must be after pickup date', 400);
        }

        $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
        
        if (!$isAvailable) {
            handleError('This car is not available for the selected dates', 409);
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn->begin_transaction();

        try {
            // Insert booking
            $stmt = $conn->prepare("
                INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            if (!$stmt) {
                throw new Exception('Failed to prepare booking statement: ' . $conn->error);
            }
            
            $stmt->bind_param("iissd", $car_id, $user_id, $pickup_date, $return_date, $total_price);
            $stmt->execute();
            $booking_id = $conn->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("UPDATE cars SET status = 'Booked' WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare car update statement: ' . $conn->error);
            }
            
            $stmt->bind_param("i", $car_id);
            $stmt->execute();
            $stmt->close();

            $version = '1.0';
            $ip_address = getClientIpAddress();
            
            $stmt = $conn->prepare("SELECT id FROM user_agreements WHERE user_id = ? AND version = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare agreement check statement: ' . $conn->error);
            }
            
            $stmt->bind_param("is", $user_id, $version);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $stmt->close();
                $stmt = $conn->prepare("
                    INSERT INTO user_agreements (user_id, version, ip_address, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                if (!$stmt) {
                    throw new Exception('Failed to prepare agreement insert statement: ' . $conn->error);
                }
                
                $stmt->bind_param("iss", $user_id, $version, $ip_address);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt->close();
            }

            $conn->commit();

            sendJsonResponse([
                'success' => true,
                'message' => 'Car booked successfully!',
                'booking_id' => $booking_id
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }

    } catch (Exception $e) {
        handleError('An error occurred while booking the car: ' . $e->getMessage(), 500);
    }
}

// Function to check car availability
function checkCarAvailability($conn, $car_id, $pickup_date, $return_date) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as booking_count 
            FROM bookings 
            WHERE car_id = ? 
            AND status != 'cancelled' 
            AND (
                (start_date <= ? AND end_date >= ?) 
                OR (start_date <= ? AND end_date >= ?) 
                OR (start_date >= ? AND end_date <= ?)
            )
        ");
        
        if (!$stmt) {
            throw new Exception('Failed to prepare availability check statement: ' . $conn->error);
        }
        
        $stmt->bind_param("issssss", $car_id, $return_date, $pickup_date, $pickup_date, $pickup_date, $pickup_date, $return_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['booking_count'] == 0;
    } catch (Exception $e) {
        error_log('Car availability check failed: ' . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: /');
    exit;
}

http_response_code(405);
sendJsonResponse([
    'success' => false,
    'message' => 'Method not allowed',
    'allowed_methods' => ['GET', 'POST']
]);
?>
