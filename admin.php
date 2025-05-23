<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
include_once "php/config.php";
?>

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

// Handle car approval/decline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['car_id'])) {
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

// Handle user approval/decline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['user_id'])) {
	header('Content-Type: application/json');

	$user_id = (int)$_POST['user_id'];
	$action = $_POST['action'];

	if ($action === 'approve_user') {
		$status = 'verified';
	} elseif ($action === 'decline_user') {
		$status = 'declined';
	} else {
		echo json_encode(['success' => false, 'message' => 'Invalid action']);
		exit;
	}

	try {
		$stmt = $pdo->prepare("UPDATE users SET verified = ? WHERE user_id = ?");
		$result = $stmt->execute([$status, $user_id]);

		if ($result) {
			echo json_encode(['success' => true, 'message' => 'User status updated successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
		}
	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
	}
	exit;
}

// Get car details
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

// Get user details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_user_details'])) {
	header('Content-Type: application/json');

	$user_id = (int)$_GET['user_id'];

	try {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
		$stmt->execute([$user_id]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$user) {
			echo json_encode(['success' => false, 'message' => 'User not found']);
			exit;
		}

		// Get driver license photos
		$stmt = $pdo->prepare("SELECT * FROM user_driver_licenses WHERE user_id = ?");
		$stmt->execute([$user_id]);
		$license_data = $stmt->fetch(PDO::FETCH_ASSOC);

		echo json_encode([
			'success' => true,
			'user' => $user,
			'license' => $license_data
		]);
	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
	}
	exit;
}

// Fetch pending cars
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

// Fetch pending users
try {
	$stmt = $pdo->prepare("
        SELECT user_id, fname, lname, email, img, phone, created_at, verified, date_of_birth, driver_license_number
        FROM users
        WHERE verified = 'Pending'
        ORDER BY created_at DESC
    ");
	$stmt->execute();
	$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$pending_users = [];
	$user_error_message = "Error fetching users: " . $e->getMessage();
}

// Fetch all users
try {
	$stmt = $pdo->prepare("
        SELECT user_id, fname, lname, email, img, phone, created_at, verified, date_of_birth, driver_license_number
        FROM users
        ORDER BY created_at DESC
    ");
	$stmt->execute();
	$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$all_users = [];
	$user_error_message = "Error fetching users: " . $e->getMessage();
}

// Get user counts for dashboard
try {
	$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users");
	$stmt->execute();
	$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

	$stmt = $pdo->prepare("SELECT COUNT(*) as pending_users FROM users WHERE verified IS NULL OR verified = 'pending'");
	$stmt->execute();
	$pending_users_count = $stmt->fetch(PDO::FETCH_ASSOC)['pending_users'];

	$stmt = $pdo->prepare("SELECT COUNT(*) as pending_cars FROM cars WHERE verified = 'pending'");
	$stmt->execute();
	$pending_cars_count = $stmt->fetch(PDO::FETCH_ASSOC)['pending_cars'];
} catch (PDOException $e) {
	$total_users = 0;
	$pending_users_count = 0;
	$pending_cars_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style/admin.css">
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<title>Admin Veehive</title>
</head>

<body>
	<section id="sidebar">
		<a href="#" class="brand">
			<span class="text">Veehive</span>
		</a>
		<ul class="side-menu top tabs">
			<li class="active tab-item" data-tab="dashboard">
				<a>
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li class="tab-item" data-tab="users">
				<a>
					<i class='bx bxs-user'></i>
					<span class="text">Pending Users</span>
				</a>
			</li>
			<li class="tab-item" data-tab="totalUsers">
				<a>
					<i class='bx bxs-user'></i>
					<span class="text">Users</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="#">
					<i class='bx bxs-cog'></i>
					<span class="text">Settings</span>
				</a>
			</li>
			<li>
				<a href="php/logout.php?logout_id=<?php echo $_SESSION['user_id']; ?>" class="logout">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>

	<section id="content">
		<nav>
			<i class='bx bx-menu'></i>
			<div class="profile-container">
				<input type="checkbox" id="switch-mode" hidden>
				<label for="switch-mode" class="switch-mode"></label>
				<a href="#" class="notification">
					<i class='bx bxs-bell'></i>
					<span class="num">8</span>
				</a>
				<a href="#" class="profile">
					<img src="php/images/1747052100_profile.jpg">
				</a>
			</div>
		</nav>

		<div id="dashboard" class="tab-content active">
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
						<i class='bx bxs-group'></i>
						<span class="text">
							<h3><?php echo $total_users; ?></h3>
							<p>Total users</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-user-check'></i>
						<span class="text">
							<h3><?php echo $pending_users_count; ?></h3>
							<p>Pending users</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-car'></i>
						<span class="text">
							<h3><?php echo $pending_cars_count; ?></h3>
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
										<td colspan="5" style="text-align: center; color: #666; padding: 20px;">
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
		</div>

		<div id="users" class="tab-content">
			<main>
				<div class="head-title">
					<div class="left">
						<h1>Pending Users</h1>
						<ul class="breadcrumb">
							<li>
								<a href="/testing">Users</a>
							</li>
							<li><i class='bx bx-chevron-right'></i></li>
							<li>
								<a class="active">Pending</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Pending Users</h3>
							<form action="#" id="userSearchForm">
								<div class="form-input">
									<input type="search" placeholder="Search..." id="userSearchInput">
									<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
								</div>
							</form>
							<i class='bx bx-filter'></i>
						</div>

						<?php if (isset($user_error_message)): ?>
							<div class="error-message"><?php echo htmlspecialchars($user_error_message); ?></div>
						<?php endif; ?>

						<table>
							<thead>
								<tr>
									<th>User</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Created at</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody id="usersTableBody">
								<?php if (empty($pending_users)): ?>
									<tr>
										<td colspan="5" style="text-align: center; color: #666; padding: 20px;">
											No pending users found
										</td>
									</tr>
								<?php else: ?>
									<?php foreach ($pending_users as $user): ?>
										<tr onclick="openUserModal(<?php echo $user['user_id']; ?>)" data-user-id="<?php echo $user['user_id']; ?>">
											<td>
												<img src="<?php echo !empty($user['img']) ? 'php/images/' . htmlspecialchars($user['img']) : 'php/images/default-profile.jpg'; ?>" alt="Profile">
												<p><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></p>
											</td>
											<td><?php echo htmlspecialchars($user['email']); ?></td>
											<td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
											<td><?php echo date('d-m-Y', strtotime($user['created_at'])); ?></td>
											<td>
												<span class="status <?php echo strtolower($user['verified'] ?? 'Pending'); ?>">
													<?php echo ucfirst($user['verified'] ?? 'Pending'); ?>
												</span>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>


			</main>
		</div>

		<!-- User Details Modal -->
		<div id="userModal" class="modal">
			<div class="modal-content" style="font-family: 'Poppins', sans-serif;">
				<span class="close" id="userModalClose">&times;</span>
				<div class="modal-header">
					<h2 id="userModalTitle">User Details</h2>
				</div>
				<div id="userModalBody" class="loading">
					Loading user details...
				</div>
			</div>
		</div>

		<div id="totalUsers" class="tab-content">
			<main>
				<div class="head-title">
					<div class="left">
						<h1>Pending Users</h1>
						<ul class="breadcrumb">
							<li>
								<a href="/testing">Users</a>
							</li>
							<li><i class='bx bx-chevron-right'></i></li>
							<li>
								<a class="active">Pending</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Users</h3>
							<form action="#" id="userSearchForm">
								<div class="form-input">
									<input type="search" placeholder="Search..." id="userSearchInput">
									<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
								</div>
							</form>
							<i class='bx bx-filter'></i>
						</div>

						<?php if (isset($user_error_message)): ?>
							<div class="error-message"><?php echo htmlspecialchars($user_error_message); ?></div>
						<?php endif; ?>

						<table>
							<thead>
								<tr>
									<th>User</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Created at</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody id="usersTableBody">
								<?php if (empty($all_users)): ?>
									<tr>
										<td colspan="5" style="text-align: center; color: #666; padding: 20px;">
											No pending users found
										</td>
									</tr>
								<?php else: ?>
									<?php foreach ($all_users as $users): ?>
										<tr onclick="openUserModal(<?php echo $users['user_id']; ?>)" data-user-id="<?php echo $users['user_id']; ?>">
											<td>
												<img src="<?php echo !empty($users['img']) ? 'php/images/' . htmlspecialchars($users['img']) : 'php/images/default-profile.jpg'; ?>" alt="Profile">
												<p><?php echo htmlspecialchars($users['fname'] . ' ' . $users['lname']); ?></p>
											</td>
											<td><?php echo htmlspecialchars($users['email']); ?></td>
											<td><?php echo htmlspecialchars($users['phone'] ?? 'N/A'); ?></td>
											<td><?php echo date('d-m-Y', strtotime($users['created_at'])); ?></td>
											<td>
												<span class="status <?php echo strtolower($users['verified'] ?? 'Pending'); ?>">
													<?php echo ucfirst($users['verified'] ?? 'Pending'); ?>
												</span>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</main>
		</div>
	</section>

	<script>
		const tabItems = document.querySelectorAll('.tab-item');
		const tabContents = document.querySelectorAll('.tab-content');

		tabItems.forEach(tab => {
			tab.addEventListener('click', () => {

				tabItems.forEach(t => t.classList.remove('active'));
				tabContents.forEach(c => c.classList.remove('active'));

				tab.classList.add('active');
				const tabName = tab.getAttribute('data-tab');
				document.getElementById(tabName).classList.add('active');
			});
		});
	</script>

	<script src="javascript/admin.js"></script>
	<script src="javascript/script.js" defer></script>
</body>

</html>