<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$car_id) {
    echo "Car not found";
    exit;
}

// $car_query = "SELECT c.*, u.fname as host_fname, u.lname as host_lname, u.img as host_image,
//             YEAR(u.created_at) as member_since
//             FROM cars c 
//             JOIN users u ON c.user_id = u.user_id 
//             WHERE c.id = ?";
$car_query = "SELECT 
                c.*, 
                u.fname AS host_fname, 
                u.lname AS host_lname, 
                u.img AS host_image,
                u.unique_id,
                YEAR(u.created_at) AS member_since
            FROM cars c 
            JOIN users u ON c.user_id = u.user_id 
            WHERE c.id = ?";

$stmt = $conn->prepare($car_query);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Car not found";
    exit;
}

$car = $result->fetch_assoc();

$features = json_decode($car['features'], true);
if (!is_array($features)) {
    $features = [];
}

$images_query = "SELECT image_path FROM car_images WHERE car_id = ? ORDER BY is_primary DESC";
$stmt = $conn->prepare($images_query);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$images_result = $stmt->get_result();

$images = array();
while ($row = $images_result->fetch_assoc()) {
    $images[] = $row['image_path'];
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> | Car Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style/view-datails.css">
</head>

<body>
    <div class="car-detail-container">
        <div class="car-header">
            <h1 class="car-title"><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></h1>
            <div class="car-rating">
                <div class="stars">
                    <i class="fa-solid fa-star"><span>4.7 (19 reviews)</span></i>

                </div>
                <div class="location-info">
                    <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($car['location']) ?>
                </div>
            </div>
        </div>

        <div class="car-details">
            <div class="car-details-container">
                <div class="car-gallery-container">
                    <div class="main-image-container">
                        <?php if (count($images) > 0): ?>
                            <!-- <img src="/php/car-images/<?= htmlspecialchars($car_id) ?>/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>"> -->
                            <img id="main-car-image" src="/php/car-images/<?= htmlspecialchars($car_id) ?>/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">
                        <?php else: ?>
                            <img id="main-car-image" src="/images/default-car.jpg" alt="Default car image">
                        <?php endif; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                        <div class="thumbnails-container">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                                    <img src="/php/car-images/<?= htmlspecialchars($car_id) ?>/<?= htmlspecialchars($image) ?>"
                                        alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> thumbnail <?= $index + 1 ?>"
                                        onclick="changeMainImage(this.src, <?= $index ?>)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="car-info">
                    <div class="host-info">
                        <img src="/php/images/<?= htmlspecialchars($car['host_image']) ?>" alt="Host" class="host-image">
                        <div class="host-details">
                            <div class="host-name">Hosted by <?= htmlspecialchars($car['host_fname'] . ' ' . $car['host_lname']) ?></div>
                            <div class="member-since">Member since <?= htmlspecialchars($car['member_since']) ?></div>
                            <div class="report-container">
                                <i class="fa-regular fa-flag"></i><span>&nbsp; &nbsp; &nbsp;Report</span>
                            </div>
                            <div class="contact-host">
                                <a href="/chat?user_id=<?php echo htmlspecialchars($car['unique_id']); ?>">
                                    <i class="fa-regular fa-comment"></i>
                                    <span>Contact Host</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="car-description">
                        <p><?= htmlspecialchars($car['description']) ?></p>
                    </div>

                    <div class="car-features">
                        <h3 class="feature-title">Car Features</h3>
                        <div class="features-list">
                            <div class="feature-item">
                                <i class="fa-solid fa-calendar"></i>
                                <span><?= htmlspecialchars($car['year']) ?></span>
                            </div>
                            <div class="feature-item">
                                <i class="fa-solid fa-users"></i>
                                <span><?= htmlspecialchars($car['seats']) ?> seats</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa-solid fa-gas-pump"></i>
                                <!-- <span><?= htmlspecialchars($car['fuel_type']) ?></span> -->
                                <span>Gasoline</span>
                            </div>
                            <div class="feature-item">
                                <i class="fa-solid fa-gear"></i>
                                <span><?= htmlspecialchars($car['transmission']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="additional-features">
                        <h3 class="feature-title">Additional Features</h3>
                        <div class="features-list">
                            <?php foreach ($features as $feature): ?>
                                <div class="feature-item">
                                    <i class="fa-solid fa-check"></i>
                                    <span><?= htmlspecialchars($feature) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="booking-panel">
                <div class="price">₱<?= htmlspecialchars($car['daily_rate']) ?> / day</div>
                <form action="/php/book-car.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= htmlspecialchars($car_id) ?>">
                    <div class="form-group">
                        <label for="pickup_date">Pick-up Date</label>
                        <input type="date" id="pickup_date" name="pickup_date" required>
                    </div>
                    <div class="form-group">
                        <label for="return_date">Return Date</label>
                        <input type="date" id="return_date" name="return_date" required>
                    </div>
                    <div id="price-calculation">
                        <div class="total-container" id="total-container" style="display: none;">
                            <div class="flex">
                                <span id="rate-calculation">₱<?= htmlspecialchars($car['daily_rate']) ?> x <span id="days-count">0</span> days</span>
                                <span id="subtotal">₱0</span>
                            </div>
                            <div class="flex">
                                <span class="total">Total</span>
                                <span class="total-amount" id="total-amount">₱0</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="book-button" onclick="openModal()">Reserve Now</button>

                    <div class="bookOverlay">
                        <div class="bookModal">
                            <h2>Terms and Conditions</h2>
                            <p>You are about to book this vehicle. Please read our Terms and Conditions before proceeding.</p>

                            <div class="bookContent">
                                <h4>Rental Terms and Conditions of Veehive</h4>

                                <ol>
                                    <li>
                                        <strong>1. Agreement to Terms</strong> By renting a vehicle from Veehive, you agree to abide by these Terms and Conditions. Please read them carefully before proceeding.
                                    </li>

                                    <li>
                                        <strong>2. Rental Eligibility</strong>
                                        <ul>
                                            <li>Renters must be at least 18 years old.</li>
                                            <li>A valid driver's license is required at the time of rental.</li>
                                            <li>An additional form of identification maybe requested by the owner.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>3. Rental Period</strong>
                                        <ul>
                                            <li>The rental period begins at the time the vehicle is picked up and ends when the vehicle is returned to the designated location.</li>
                                            <li>Late returns may incur additional charges as per the wwners's late return policy.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>4. Rental Period</strong>
                                        <ul>
                                            <li>The renter agrees to use the vehicle only for lawful purposes.</li>
                                            <li>The vehicle must not be used for racing, towing, or any illegal activity.</li>
                                            <li>Smoking and the transport of hazardous materials are prohibited inside the vehicle.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>5. Payment and Fees</strong>
                                        <ul>
                                            <li>Payment must be made in full at the start of the rental period unless otherwise agreed.</li>
                                            <li>Additional charges may apply for fuel, cleaning, damages, or traffic violations incurred during the rental period.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>6. Insurance and liability</strong>
                                        <ul>
                                            <li>The renter is responsible for any damage to the vehicle not covered by insurance.</li>
                                            <li>The renter must report any accident or damage to the vehicle immediately to the Risk Management Department.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>7. Vehicle Condition and Maintenance</strong>
                                        <ul>
                                            <li>The renter is responsible for returning the vehicle in the same condition as it was provided.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>8. Vehicle Condition and Maintenance</strong>
                                        <ul>
                                            <li>Cancellations must be made at least 6 hours in advance for a rebooking.</li>
                                            <li>Late cancellations or no-shows is forfeited and will no longer be valid for rebooking.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>9. Termination of Rental</strong>
                                        <ul>
                                            <li>The Company reserves the right to terminate the rental agreement at any time for violations of these Terms and Conditions.</li>
                                            <li>In the event of termination, the renter must return the vehicle immediately.</li>
                                        </ul>
                                    </li>

                                    <li>
                                        <strong>10. Governing Law</strong>
                                        <ul>
                                            <li>These Terms and Conditions shall be governed by the laws of the Republic of the Philippines.</li>
                                            <li>Any disputes will be subject to the exclusive jurisdiction of the courts of Republic of the Philippines.</li>
                                        </ul>
                                    </li>
                                </ol>
                            </div>
                            <div class="bookCheckbox">
                                <div>
                                    <input type="checkbox" id="termsCheckbox" required>
                                </div>
                                <div>
                                    <label for="termsCheckbox">I have read and agree to the Terms and Conditions</label>
                                </div>
                            </div>
                            <div class="bookButtons">
                                <button type="submit" id="submitBtn" class="bookConfirm" disabled>Confirm</button>
                                <div class="bookCancel" onclick="closeModal()">Cancel</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const checkbox = document.getElementById('termsCheckbox');
        const submitBtn = document.getElementById('submitBtn');

        checkbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });

        openModal = () => {
            document.querySelector('.bookOverlay').style.display = 'flex';
        }

        closeModal = () => {
            document.querySelector('.bookOverlay').style.display = 'none';
        }

        function changeMainImage(src, index) {
            document.getElementById('main-car-image').src = src;

            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumb => {
                thumb.classList.remove('active');
            });

            thumbnails[index].classList.add('active');
        }

        // keyboard navigation for images
        document.addEventListener('keydown', function(event) {
            const thumbnails = document.querySelectorAll('.thumbnail');
            if (thumbnails.length <= 1) return;

            const currentActive = document.querySelector('.thumbnail.active');
            let currentIndex = parseInt(currentActive.getAttribute('data-index'));

            if (event.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % thumbnails.length;
            } else if (event.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
            } else {
                return;
            }

            const imgSrc = thumbnails[currentIndex].querySelector('img').src;
            changeMainImage(imgSrc, currentIndex);
        });

        
        function recordAgreement(callback) {
            const formData = new FormData();
            formData.append('record_agreement', 1);
            formData.append('version', '1.0');

            fetch('/php/book-car.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Agreement response:', data);
                    if (data.success) {
                        if (callback) callback();
                    } else {
                        showBookingMessage('Failed to record your agreement: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error recording agreement:', error);
                    // Continue with booking even if agreement recording fails
                    if (callback) callback();
                });
        }

        window.carDailyRate = <?= json_encode($car['daily_rate']) ?>;

        (function() {
            function initPriceCalculator() {
                const moduleId = 'price-calculation';

                if (!document.getElementById(moduleId)) return;

                const pickupDateInput = document.getElementById('pickup_date');
                const returnDateInput = document.getElementById('return_date');
                const totalContainer = document.getElementById('total-container');
                const daysCountElement = document.getElementById('days-count');
                const subtotalElement = document.getElementById('subtotal');
                const totalAmountElement = document.getElementById('total-amount');
                const dailyRate = window.carDailyRate || 0;

                function updatePriceCalculation() {
                    if (pickupDateInput.value && returnDateInput.value) {
                        const pickupDate = new Date(pickupDateInput.value);
                        const returnDate = new Date(returnDateInput.value);

                        if (returnDate > pickupDate) {
                            const diffTime = Math.abs(returnDate - pickupDate);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            daysCountElement.textContent = diffDays;
                            const subtotal = diffDays * dailyRate;

                            document.getElementById('booking-form').dataset.totalPrice = subtotal;

                            subtotalElement.textContent = '₱' + subtotal.toLocaleString();
                            totalAmountElement.textContent = '₱' + subtotal.toLocaleString();
                            totalContainer.style.display = 'block';

                            if (pickupDateInput.value && returnDateInput.value) {
                                checkCarAvailability();
                            }
                        } else {
                            totalContainer.style.display = 'none';
                        }
                    } else {
                        totalContainer.style.display = 'none';
                    }
                }

                pickupDateInput.addEventListener('change', updatePriceCalculation);
                returnDateInput.addEventListener('change', updatePriceCalculation);
                updatePriceCalculation();

                return function cleanup() {
                    pickupDateInput.removeEventListener('change', updatePriceCalculation);
                    returnDateInput.removeEventListener('change', updatePriceCalculation);
                };
            }

            function validateBookingForm() {
                const pickupDateInput = document.getElementById('pickup_date');
                const returnDateInput = document.getElementById('return_date');

                if (!pickupDateInput.value || !returnDateInput.value) {
                    showBookingMessage('Please select both pickup and return dates', 'error');
                    return false;
                }

                const pickupDate = new Date(pickupDateInput.value);
                const returnDate = new Date(returnDateInput.value);

                if (returnDate <= pickupDate) {
                    showBookingMessage('Return date must be after pickup date', 'error');
                    return false;
                }

                const bookingForm = document.getElementById('booking-form');
                const totalPrice = parseFloat(bookingForm.dataset.totalPrice || 0);

                if (totalPrice <= 0) {
                    showBookingMessage('Please select valid booking dates to calculate price', 'error');
                    return false;
                }

                return true;
            }

            function checkCarAvailability(callback) {
                const carId = document.querySelector('input[name="car_id"]').value;
                const pickupDate = document.getElementById('pickup_date').value;
                const returnDate = document.getElementById('return_date').value;

                showBookingMessage('Checking car availability...', 'info');

                // AJAX request to check availability
                fetch(`/php/book-car.php?check_availability=1&car_id=${carId}&pickup_date=${pickupDate}&return_date=${returnDate}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.available) {
                                if (callback) callback();
                            } else {
                                showBookingMessage('This car is not available for the selected dates', 'error');
                            }
                        } else {
                            showBookingMessage(data.message || 'Error checking availability', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showBookingMessage('An error occurred while checking availability', 'error');
                    });
            }

            function openModal() {
                if (validateBookingForm()) {
                    checkCarAvailability(function() {
                        document.querySelector('.bookOverlay').style.display = 'flex';
                    });
                }
            }

            function closeModal() {
                document.querySelector('.bookOverlay').style.display = 'none';
            }

            function initBookingForm() {
                const bookingForm = document.querySelector('.booking-panel form');
                if (!bookingForm) return;

                bookingForm.id = 'booking-form';

                // Remove the default form submission to prevent conflicts
                bookingForm.onsubmit = function(e) {
                    e.preventDefault();
                };

                // Add click handler for Reserve Now button
                const reserveButton = bookingForm.querySelector('.book-button');
                reserveButton.type = 'button';
                reserveButton.onclick = openModal;

                // Add listener for the confirm button in the modal
                const confirmButton = document.getElementById('submitBtn');
                confirmButton.addEventListener('click', function() {
                    submitBooking();
                });

                // Add checkbox listener to enable/disable confirm button
                const termsCheckbox = document.getElementById('termsCheckbox');
                termsCheckbox.addEventListener('change', function() {
                    document.getElementById('submitBtn').disabled = !this.checked;
                });

                document.querySelector('.bookOverlay').style.display = 'none';
            }

            function submitBooking() {
                const bookingForm = document.getElementById('booking-form');
                const pickupDateInput = document.getElementById('pickup_date');
                const returnDateInput = document.getElementById('return_date');
                const totalPrice = parseFloat(bookingForm.dataset.totalPrice || 0);
                const termsCheckbox = document.getElementById('termsCheckbox');

                const submitButton = document.getElementById('submitBtn');
                const originalButtonText = submitButton.textContent;
                submitButton.textContent = 'Processing...';
                submitButton.disabled = true;

                // Prepare form data
                const formData = new FormData();
                formData.append('car_id', document.querySelector('input[name="car_id"]').value);
                formData.append('pickup_date', pickupDateInput.value);
                formData.append('return_date', returnDateInput.value);
                formData.append('total_price', totalPrice);
                formData.append('has_agreed', termsCheckbox.checked);

                // Debug what's being sent
                console.log('Sending booking data:', {
                    car_id: document.querySelector('input[name="car_id"]').value,
                    pickup_date: pickupDateInput.value,
                    return_date: returnDateInput.value,
                    total_price: totalPrice,
                    has_agreed: termsCheckbox.checked
                });

                recordAgreement(function() {
                    fetch('/php/book-car.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Server response:', data);
                            closeModal();

                            if (data.success) {
                                showBookingMessage(data.message, 'success');

                                pickupDateInput.value = '';
                                returnDateInput.value = '';

                                document.getElementById('total-container').style.display = 'none';

                                pickupDateInput.disabled = true;
                                returnDateInput.disabled = true;
                                document.querySelector('.book-button').textContent = 'Booked!';
                                document.querySelector('.book-button').disabled = true;

                                setTimeout(() => {
                                    window.location.href = '/booking-confirmation?id=' + data.booking_id;
                                }, 2000);
                            } else {
                                showBookingMessage(data.message, 'error');
                                submitButton.textContent = originalButtonText;
                                submitButton.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            closeModal();
                            showBookingMessage('An error occurred. Please try again.', 'error');
                            submitButton.textContent = originalButtonText;
                            submitButton.disabled = false;
                        });
                });
            }

            function showBookingMessage(message, type) {
                const bookingForm = document.getElementById('booking-form');
                let messageContainer = document.getElementById('booking-message');

                if (!messageContainer) {
                    messageContainer = document.createElement('div');
                    messageContainer.id = 'booking-message';
                    bookingForm.insertBefore(messageContainer, bookingForm.firstChild);
                }

                if (type === 'success') {
                    messageContainer.className = 'success-message';
                } else if (type === 'error') {
                    messageContainer.className = 'error-message';
                } else if (type === 'info') {
                    messageContainer.className = 'info-message';
                }

                messageContainer.textContent = message;

                if (type !== 'error') {
                    setTimeout(() => {
                        messageContainer.style.opacity = '0';
                        setTimeout(() => {
                            if (messageContainer.parentNode) {
                                messageContainer.parentNode.removeChild(messageContainer);
                            }
                        }, 500);
                    }, 1000);
                }
            }

            window.openModal = openModal;
            window.closeModal = closeModal;

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    initPriceCalculator();
                    initBookingForm();
                });
            } else {
                initPriceCalculator();
                initBookingForm();
            }
        })();
    </script>
</body>

</html>