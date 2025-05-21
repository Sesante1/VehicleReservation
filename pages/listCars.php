<?php
session_start();
include_once "../php/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<!-- <body> -->
    <div class="flex">
        <div>
            <div class="profile-nav">
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
                $row = mysqli_fetch_assoc($sql);
                ?>
                <div class="profile-container">
                    <div class="profile">
                        <img src="php/images/<?php echo $row['img']; ?>" alt="Image">
                        <p><?php echo $row['fname'] . " " . $row['lname']; ?>
                            <br> <span><?php echo $row['email'] ?></span>
                        </p>

                    </div>
                </div>
                <div class="btn-box">
                    <a class="btn active" id="btn1" onclick="showTab(1)"><i class="fa-solid fa-calendar-week"></i>My Bookings</a>
                    <a class="btn" id="btn2" onclick="showTab(2)"><i class="fa-solid fa-car-side"></i>My Car</a>
                    <a class="btn" id="btn3" onclick="showTab(3)"><i class="fa-solid fa-calendar-week"></i>Booking Requests</a>
                    <a class="btn" id="btn4" onclick="showTab(4)"><i class="fa-solid fa-check-to-slot"></i>Completed</a>
                    <a class="btn" id="btn5" onclick="showTab(5)"><i class="fa-solid fa-xmark"></i>Cancelled</a>
                    <!-- <i class="fa-solid fa-user"></i> -->
                </div>
            </div>

            <!-- <div class="quick-link-card">
                <h4>Quick Links</h4>
                <a class="quick-link"><i class="fas fa-cog"></i> Account Settings</a>
                <a class="quick-link"><i class="fas fa-credit-card"></i> Payment Methods</a>
                <a class="quick-link"><i class="fas fa-question-circle"></i> Help Center</a>
            </div> -->
        </div>

        <div class="myCar content_box">
            <div id="content1" class="content active">
                <h3>My Bookings</h3>

                <div class="scroll-box" id="my-booking-list">
                    <!-- <div class="booking-card">
                        <img src="php/car-images/5/67fd3c38b67ff.avif" alt="Tesla Model 3" class="booking-car-image">
                        <div class="booking-details">
                            <h3>Tesla Model 3 <span class="status">Confirmed</span></h3>
                            <div class="booking-dates"><strong>Dates:</strong> Sep 15, 2023 - Sep 20, 2023</div>
                            <div class="booking-price"><strong>Total Price:</strong> $425</div>
                            <div class="booking-actions">
                                <a href="#">Contact Host</a>
                                <div>
                                    <a href="#">View Car Details</a>
                                    <span class="cancel-booking">Cancel Booking</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>

            </div>

            <div id="content2" class="content">
                <h3>My Cars</h3>
                <a href="/listCar"><button class="add-car">List a New Car</button></a>
                <div class="car-flex scroll-box" id="my-car-list"></div>
            </div>

            <div id="content3" class="content">
                <h3>Pending Request</h3>
                <div class="scroll-box" id="booking-request-list"></div>
            </div>

            <div id="content4" class="content">
                <h3>Completed</h3>
                <div class="scroll-box" id="completed-booking-list"></div>
            </div>

            <div id="content5" class="content">
                <h3>Cancelled</h3>
                <div class="scroll-box" id="cancelled-booking-list"></div>
            </div>

        </div>

        <!-- Modal -->
        <div class="overlay" id="carStatusModal">
            <div class="modal">
                <button class="close-btn" onclick="closeModal()">&times;</button>
                <h2 id="modalTitle"></h2>
                <p class="modal-description" id="modalDescription"></p>
                <div class="modal-buttons">
                    <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button class="confirmb-tn" id="modalConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Modal decline booking -->
        <div class="modal-overlay" id="declineModal">
            <div class="decline-modal">
                <button class="close-icon" onclick="closeDeclineModal()">&times;</button>
                <h3>Decline Booking Request</h3>
                <p>Are you sure you want to decline this booking request?<br>
                    Please provide a reason for the customer.</p>

                <select class="reason-select">
                    <option disabled selected>Select a reason...</option>
                    <option>Not available on those dates</option>
                    <option>Pricing issue</option>
                    <option>Vehicle maintenance</option>
                    <option>Other</option>
                </select>

                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeDeclineModal()">Cancel</button>
                    <button class="btn-decline declineBook">Decline</button>
                </div>
            </div>
        </div>

        <!-- Modal cancel booking -->
        <div class="modal-overlay" id="cancel-booking">
            <div class="decline-modal">
                <button class="close-icon" onclick="closeCancelBookingModal()">&times;</button>
                <h3>Cancel Booking Request</h3>
                <p>Are you sure you want to cancel this booking request?<br>
                    Please provide a reason for the customer.</p>

                <select class="reason-select">
                    <option disabled selected>Select a reason...</option>
                    <option>Not available on those dates</option>
                    <option>Pricing issue</option>
                    <option>Vehicle maintenance</option>
                    <option>Other</option>
                </select>

                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeCancelBookingModal()">Cancel</button>
                    <button class="btn-decline cancelBook">Decline</button>
                </div>
            </div>
        </div>

        <script src="../javascript/list-cars.js"></script>
<!-- </body> -->

</html>