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

//     $car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
//     $pickup_date = filter_input(INPUT_POST, 'pickup_date', FILTER_SANITIZE_STRING);
//     $return_date = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
//     $total_price = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
//     $user_id = $_SESSION['user_id'];
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


ob_start();
session_start();
require_once '../php/config.php';

header('Content-Type: application/json');

// Check car availability endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_availability'])) {
    $car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
    $pickup_date = filter_input(INPUT_GET, 'pickup_date', FILTER_SANITIZE_STRING);
    $return_date = filter_input(INPUT_GET, 'return_date', FILTER_SANITIZE_STRING);
    
    if (!$car_id || !$pickup_date || !$return_date) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    $pickup_date = date('Y-m-d', strtotime($pickup_date));
    $return_date = date('Y-m-d', strtotime($return_date));
    
    $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'available' => $isAvailable,
        'message' => $isAvailable ? 'Car is available for the selected dates' : 'This car is not available for the selected dates'
    ]);
    exit;
}

// Check user verification status endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_verification'])) {
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in'
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    ob_clean();
    echo json_encode([
        'success' => true,
        'verified_status' => $user['verified'],
        'is_verified' => ($user['verified'] === 'verified')
    ]);
    exit;
}

// Record agreement endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_agreement'])) {
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in to agree to terms'
        ]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING) ?: '1.0';
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    try {
        $stmt = $conn->prepare("
            SELECT id FROM user_agreements 
            WHERE user_id = ? AND version = ?
        ");
        $stmt->bind_param("is", $user_id, $version);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Agreement already recorded',
                'existing' => true
            ]);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO user_agreements (user_id, version, ip_address) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iss", $user_id, $version, $ip_address);
            $stmt->execute();
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Agreement recorded successfully',
                'agreement_id' => $conn->insert_id
            ]);
        }
        exit;
    } catch (Exception $e) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to record agreement: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Booking process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in to book a car'
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT verified FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    if ($user['verified'] !== 'verified') {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Please complete your account setup and verification before booking a car.',
            'verification_required' => true,
            'verification_status' => $user['verified']
        ]);
        exit;
    }

    $car_id = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);
    $pickup_date = filter_input(INPUT_POST, 'pickup_date', FILTER_SANITIZE_STRING);
    $return_date = filter_input(INPUT_POST, 'return_date', FILTER_SANITIZE_STRING);
    $total_price = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
    $has_agreed = filter_input(INPUT_POST, 'has_agreed', FILTER_VALIDATE_BOOLEAN);

    if (!$car_id) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid car selected']);
        exit;
    }

    if (!$pickup_date) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid pickup date']);
        exit;
    }

    if (!$return_date) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid return date']);
        exit;
    }

    if (!$total_price) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid total price']);
        exit;
    }
    
    if (!$has_agreed) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'You must agree to the terms and conditions']);
        exit;
    }

    $pickup_date = date('Y-m-d', strtotime($pickup_date));
    $return_date = date('Y-m-d', strtotime($return_date));

    $isAvailable = checkCarAvailability($conn, $car_id, $pickup_date, $return_date);
    
    if (!$isAvailable) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'This car is not available for the selected dates']);
        exit;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn->begin_transaction();

    try {

        $stmt = $conn->prepare("
            INSERT INTO bookings (car_id, user_id, start_date, end_date, total_price, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iissd", $car_id, $user_id, $pickup_date, $return_date, $total_price);
        $stmt->execute();

        $booking_id = $conn->insert_id;

        $stmt = $conn->prepare("UPDATE cars SET status = 'Booked' WHERE id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();

        $version = '1.0';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        $stmt = $conn->prepare("
            SELECT id FROM user_agreements 
            WHERE user_id = ? AND version = ?
        ");
        $stmt->bind_param("is", $user_id, $version);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("
                INSERT INTO user_agreements (user_id, version, ip_address) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iss", $user_id, $version, $ip_address);
            $stmt->execute();
        }

        $conn->commit();

        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Car booked successfully!',
            'booking_id' => $booking_id
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while booking the car: ' . $e->getMessage()
        ]);
    }

    exit;
}

function checkCarAvailability($conn, $car_id, $pickup_date, $return_date) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as booking_count 
        FROM bookings 
        WHERE car_id = ? 
        AND status != 'cancelled' 
        AND ((start_date <= ? AND end_date >= ?) 
            OR (start_date <= ? AND end_date >= ?) 
            OR (start_date >= ? AND end_date <= ?))
    ");
    $stmt->bind_param("issssss", $car_id, $return_date, $pickup_date, $pickup_date, $pickup_date, $pickup_date, $return_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['booking_count'] == 0;
}

// If no valid endpoint is hit, redirect to home
header('Location: /');
exit;

?>
