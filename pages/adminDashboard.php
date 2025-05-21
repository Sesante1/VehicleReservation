<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatapp";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $car_id = (int)$_POST['car_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'decline') {
        $status = 'declined';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE cars SET verified = ? WHERE id = ?");
        $result = $stmt->execute([$status, $car_id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Car status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update car status']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_car_details'])) {
    header('Content-Type: application/json');

    $car_id = (int)$_GET['car_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT c.*, u.fname, u.lname, u.email, u.img as user_img
            FROM cars c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.id = ?
        ");
        $stmt->execute([$car_id]);
        $car = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$car) {
            echo json_encode(['success' => false, 'message' => 'Car not found']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC");
        $stmt->execute([$car_id]);
        $car_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM car_documents WHERE car_id = ?");
        $stmt->execute([$car_id]);
        $car_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $car['features'] = json_decode($car['features'], true);

        echo json_encode([
            'success' => true,
            'car' => $car,
            'images' => $car_images,
            'documents' => $car_documents
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}


try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.make, c.model, c.year, c.created_at, c.verified,
        u.fname, u.lname, u.img as user_img
        FROM cars c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.verified = 'pending'
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $pending_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pending_cars = [];
    $error_message = "Error fetching cars: " . $e->getMessage();
}
?>

<!-- MAIN -->
<main>
    <div class="head-title">
        <div class="left">
            <h1>Admin Dashboard</h1>
            <ul class="breadcrumb">
                <li>
                    <a href="/testing">Dashboard</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>
                    <a class="active">Home</a>
                </li>
            </ul>
        </div>
    </div>

    <ul class="box-info">
        <li>
            <!-- <i class='bx bxs-calendar-check'></i> -->
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3>1020</h3>
                <p>Total users</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3>2834</h3>
                <p>Pending users</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-dollar-circle'></i>
            <span class="text">
                <h3>2543</h3>
                <p>Pending Cars</p>
            </span>
        </li>
    </ul>


    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Pending Cars</h3>
                <form action="#" id="searchForm">
                    <div class="form-input">
                        <input type="search" placeholder="Search..." id="searchInput">
                        <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                    </div>
                </form>
                <i class='bx bx-filter'></i>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>Car</th>
                        <th>Year</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="carsTableBody">
                    <?php if (empty($pending_cars)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666; padding: 20px;">
                                No pending cars found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_cars as $car): ?>
                            <tr onclick="openCarModal(<?php echo $car['id']; ?>)" data-car-id="<?php echo $car['id']; ?>">
                                <td>
                                    <img src="<?php echo !empty($car['user_img']) ? 'php/images/' . htmlspecialchars($car['user_img']) : 'php/images/default-profile.jpg'; ?>" alt="Profile">
                                    <p><?php echo htmlspecialchars($car['fname'] . ' ' . $car['lname']); ?></p>
                                </td>
                                <td><?php echo htmlspecialchars($car['make']); ?></td>
                                <td><?php echo htmlspecialchars($car['year']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($car['created_at'])); ?></td>
                                <td>
                                    <span class="status <?php echo strtolower($car['verified']); ?>">
                                        <?php echo ucfirst($car['verified']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Car Details Modal -->
    <div id="carModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle">Car Details</h2>
            </div>
            <div id="modalBody" class="loading">
                Loading car details...
            </div>
        </div>
    </div>
</main>
<script>
    (function() {
        // Modal functionality
        let modal = document.getElementById('carModal');
        let modalBody = document.getElementById('modalBody');
        let modalTitle = document.getElementById('modalTitle');
        let closeBtn = document.getElementsByClassName('close')[0];

        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Open car details modal
        function openCarModal(carId) {
            modal.style.display = 'block';
            modalBody.innerHTML = '<div class="loading">Loading car details...</div>';

            fetch(`?get_car_details=1&car_id=${carId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCarDetails(data.car, data.images, data.documents);
                    } else {
                        modalBody.innerHTML = `<div class="error-message">Error: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = `<div class="error-message">Error loading car details: ${error.message}</div>`;
                });
        }

        function displayCarDetails(car, images, documents) {
            modalTitle.textContent = `${car.make} ${car.model} (${car.year})`;

            let featuresHtml = '';
            if (car.features && Array.isArray(car.features)) {
                featuresHtml = car.features.join(', ');
            } else if (car.features) {
                featuresHtml = car.features;
            } else {
                featuresHtml = 'N/A';
            }

            let imagesHtml = '';
            if (images && images.length > 0) {
                imagesHtml = images.map(img => `
                    <div class="image-item">
                        <img src="${img.image_path}" alt="Car Image" onerror="this.src='php/images/no-image.jpg'">
                        ${img.is_primary ? '<div class="document-label">Primary Image</div>' : ''}
                    </div>
                `).join('');
            } else {
                imagesHtml = '<p>No images available</p>';
            }

            let documentsHtml = '';
            if (documents && documents.length > 0) {
                documentsHtml = documents.map(doc => `
                    <div class="document-item">
                        <img src="${doc.image_path}" alt="${doc.document_type} Document" onerror="this.src='php/images/no-document.jpg'">
                        <div class="document-label">${doc.document_type} Document</div>
                    </div>
                `).join('');
            } else {
                documentsHtml = '<p>No documents available</p>';
            }

            modalBody.innerHTML = `
                <div class="car-details-grid">
                    <div class="car-detail-item">
                        <div class="car-detail-label">Owner</div>
                        <div class="car-detail-value">${car.first_name} ${car.last_name}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Email</div>
                        <div class="car-detail-value">${car.email}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Phone</div>
                        <div class="car-detail-value">${car.phone || 'N/A'}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Car Type</div>
                        <div class="car-detail-value">${car.car_type}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Daily Rate</div>
                        <div class="car-detail-value">$${car.daily_rate}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Location</div>
                        <div class="car-detail-value">${car.location}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Transmission</div>
                        <div class="car-detail-value">${car.transmission}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Seats</div>
                        <div class="car-detail-value">${car.seats}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Available From</div>
                        <div class="car-detail-value">${car.available_from}</div>
                    </div>
                    <div class="car-detail-item">
                        <div class="car-detail-label">Available Until</div>
                        <div class="car-detail-value">${car.available_until || 'N/A'}</div>
                    </div>
                    <div class="car-detail-item" style="grid-column: 1 / -1;">
                        <div class="car-detail-label">Description</div>
                        <div class="car-detail-value">${car.description}</div>
                    </div>
                    <div class="car-detail-item" style="grid-column: 1 / -1;">
                        <div class="car-detail-label">Features</div>
                        <div class="car-detail-value">${featuresHtml}</div>
                    </div>
                </div>

                <div class="images-section">
                    <h3>Car Images</h3>
                    <div class="images-grid">
                        ${imagesHtml}
                    </div>
                </div>

                <div class="documents-section">
                    <h3>Car Documents</h3>
                    <div class="documents-grid">
                        ${documentsHtml}
                    </div>
                </div>

                ${car.verified === 'pending' ? `
                <div class="action-buttons">
                    <button class="btn btn-approve" onclick="approveDeclineCar(${car.id}, 'approve')">
                        <i class='bx bx-check'></i> Approve
                    </button>
                    <button class="btn btn-decline" onclick="approveDeclineCar(${car.id}, 'decline')">
                        <i class='bx bx-x'></i> Decline
                    </button>
                </div>
                ` : ''}
            `;
        }

        // Approve/Decline car
        function approveDeclineCar(carId, action) {
            if (!confirm(`Are you sure you want to ${action} this car?`)) {
                return;
            }

            const actionButtons = document.querySelector('.action-buttons');
            actionButtons.innerHTML = '<div class="loading">Processing...</div>';

            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&car_id=${carId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);

                        modal.style.display = 'none';
                        location.reload();
                    } else {
                        alert(`Error: ${data.message}`);

                        actionButtons.innerHTML = `
                        <button class="btn btn-approve" onclick="approveDeclineCar(${carId}, 'approve')">
                            <i class='bx bx-check'></i> Approve
                        </button>
                        <button class="btn btn-decline" onclick="approveDeclineCar(${carId}, 'decline')">
                            <i class='bx bx-x'></i> Decline
                        </button>
                    `;
                    }
                })
                .catch(error => {
                    alert(`Error: ${error.message}`);

                    actionButtons.innerHTML = `
                    <button class="btn btn-approve" onclick="approveDeclineCar(${carId}, 'approve')">
                        <i class='bx bx-check'></i> Approve
                    </button>
                    <button class="btn btn-decline" onclick="approveDeclineCar(${carId}, 'decline')">
                        <i class='bx bx-x'></i> Decline
                    </button>
                `;
                });
        }

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            performSearch();
        });

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#carsTableBody tr');

            rows.forEach(row => {
                const owner = row.querySelector('td:first-child p').textContent.toLowerCase();
                const car = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                if (owner.includes(searchTerm) || car.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    })();
</script>
<!-- MAIN -->