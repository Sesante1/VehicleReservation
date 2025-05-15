<?php
session_start();
require_once '../php/config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /');
    exit;
}

$booking_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.*, c.make, c.model, c.year, c.location, c.seats, c.transmission
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // header('Location: /');
    echo "No booking found or you do not have permission to view this booking.";
    exit;
}

$booking = $result->fetch_assoc();

// confirmation number format
$confirmation_number = 'BK-' . str_pad($booking_id, 4, '0', STR_PAD_LEFT);

$payment_method = "Visa";
$payment_last_four = "••••";
$payment_time = date('h:i A', strtotime($booking['created_at']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation | Car Rental</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .confirmation-container {
            max-width: 750px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .confirmation-header {
            background-color: #25c16a;
            color: white;
            text-align: center;
            padding: 25px 20px;
        }
        
        .confirmation-header .check-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .confirmation-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .confirmation-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .car-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        
        .car-title {
            font-size: 22px;
            font-weight: 600;
            margin: 0;
        }
        
        .car-location {
            font-size: 15px;
            color: #555;
            margin-top: 5px;
        }
        
        .confirmation-number {
            font-size: 14px;
            color: #555;
            text-align: right;
        }
        
        .confirmation-number strong {
            display: block;
            font-size: 18px;
            color: #333;
            margin-top: 5px;
        }
        
        .booking-details {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-section {
            margin-bottom: 10px;
        }
        
        .detail-section h3 {
            font-size: 15px;
            color: #555;
            margin: 0 0 10px 0;
            font-weight: 500;
        }
        
        .detail-section .content {
            display: flex;
            align-items: center;
            font-size: 16px;
        }
        
        .detail-section .content i {
            margin-right: 10px;
            color: #666;
            width: 20px;
        }
        
        .detail-section .price {
            font-size: 20px;
            font-weight: 600;
        }
        
        .detail-section .payment-method {
            display: block;
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .important-info {
            padding: 25px 20px;
        }
        
        .important-info h3 {
            font-size: 18px;
            margin: 0 0 15px 0;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        
        .info-item i {
            color: #25c16a;
            margin-right: 10px;
            margin-top: 3px;
        }
        
        .info-item p {
            margin: 0;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .action-buttons {
            display: flex;
            padding: 0 20px 25px;
            gap: 15px;
        }
        
        .btn {
            flex: 1;
            padding: 12px 15px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .btn-secondary {
            background-color: #f2f2f2;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e5e5e5;
        }
        
        .btn-primary {
            background-color: #1a73e8;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0d62d0;
        }
    </style>
</head>
<body>
    <!-- <?php include 'header.php'; ?> -->
    
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="check-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1>Booking Confirmed!</h1>
            <p>Your reservation has been successfully processed.</p>
        </div>
        
        <div class="car-header">
            <div>
                <h2 class="car-title"><?= htmlspecialchars($booking['make'] . ' ' . $booking['model']) ?></h2>
                <div class="car-location"><?= htmlspecialchars($booking['location']) ?></div>
            </div>
            <div class="confirmation-number">
                Confirmation #
                <strong><?= htmlspecialchars($confirmation_number) ?></strong>
            </div>
        </div>
        
        <div class="booking-details">
            <div class="detail-section">
                <h3>Trip Dates</h3>
                <div class="content">
                    <i class="fa-regular fa-calendar"></i>
                    <?php 
                        $start_date = date('D, M j, Y', strtotime($booking['start_date']));
                        $end_date = date('D, M j, Y', strtotime($booking['end_date']));
                        echo htmlspecialchars($start_date . ' - ' . $end_date);
                    ?>
                </div>
                <div class="content" style="margin-top: 5px; font-size: 14px;">
                    <i class="fa-regular fa-clock"></i>
                    Pick-up & return at 10:00 AM
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Vehicle</h3>
                <div class="content">
                    <i class="fa-solid fa-car"></i>
                    <?= htmlspecialchars($booking['year'] . ' ' . $booking['make'] . ' ' . $booking['model']) ?>
                </div>
                <div class="content" style="margin-top: 5px; font-size: 14px;">
                    <i class="fa-solid fa-gears"></i>
                    <?= htmlspecialchars($booking['transmission']) ?> • <?= htmlspecialchars($booking['seats']) ?> seats
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Pick-up & Return Location</h3>
                <div class="content">
                    <i class="fa-solid fa-location-dot"></i>
                    <?= htmlspecialchars($booking['location']) ?>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Payment</h3>
                <div class="content">
                    <i class="fa-regular fa-credit-card"></i>
                    <div>
                        <span class="price">Total: ₱<?= number_format($booking['total_price'], 0) ?></span>
                        <span class="payment-method"><?= htmlspecialchars($payment_method) ?> <?= htmlspecialchars($payment_last_four) ?> 4242</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="important-info">
            <h3>Important Information</h3>
            
            <div class="info-item">
                <i class="fa-solid fa-circle-check"></i>
                <p>Please arrive on time for your scheduled pick-up</p>
            </div>
            
            <div class="info-item">
                <i class="fa-solid fa-circle-check"></i>
                <p>Bring your driver's license and the credit card used for payment</p>
            </div>
            
            <div class="info-item">
                <i class="fa-solid fa-circle-check"></i>
                <p>The car should be returned with the same fuel level as at pick-up</p>
            </div>
            
            <div class="info-item">
                <i class="fa-solid fa-circle-check"></i>
                <p>Contact the owner directly through the app for any questions</p>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="/" class="btn btn-secondary">Return to Homepage</a>
            <a href="/my-bookings.php" class="btn btn-primary">View My Bookings</a>
        </div>
    </div>
    
    <!-- <?php include 'footer.php'; ?> -->
</body>
</html>