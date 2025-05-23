<?php
session_start();

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "chatapp";

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    echo "Database connection error" . mysqli_connect_error();
}

class UserProfileManager
{
    private $conn;
    private $uploadDir = 'uploads/';

    public function __construct($connection)
    {
        $this->conn = $connection;

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        if (!file_exists($this->uploadDir . 'profile/')) {
            mkdir($this->uploadDir . 'profile/', 0777, true);
        }
        if (!file_exists($this->uploadDir . 'licenses/')) {
            mkdir($this->uploadDir . 'licenses/', 0777, true);
        }
    }

    public function getUserProfile($userId)
    {
        $userId = mysqli_real_escape_string($this->conn, $userId);
        $sql = "SELECT 
                    u.user_id,
                    u.fname,
                    u.lname,
                    u.email,
                    u.phone,
                    u.img as profile_image,
                    u.date_of_birth,
                    u.driver_license_number,
                    u.status,
                    u.verified,
                    u.created_at,
                    u.updated_at,
                    dl.license_front_photo,
                    dl.license_back_photo,
                    dl.license_with_owner_photo
                FROM users u
                LEFT JOIN user_driver_licenses dl ON u.user_id = dl.user_id
                WHERE u.user_id = '$userId'";

        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            throw new Exception("Query error: " . mysqli_error($this->conn));
        }
        return mysqli_fetch_assoc($result);
    }

    private function handleFileUpload($file, $directory, $prefix = '')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.");
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception("File size too large. Maximum size is 5MB.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . uniqid() . '.' . $extension;
        $filepath = $this->uploadDir . $directory . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        } else {
            throw new Exception("Failed to upload file.");
        }
    }

    public function updateUserProfile($userId, $profileData, $files = [])
    {
        mysqli_begin_transaction($this->conn);

        try {
            $userIdEscaped = mysqli_real_escape_string($this->conn, $userId);
            $userFields = [];

            if (!empty($profileData['fname'])) {
                $firstName = mysqli_real_escape_string($this->conn, $profileData['fname']);
                $userFields[] = "fname = '$firstName'";
            }

            if (!empty($profileData['lname'])) {
                $lastName = mysqli_real_escape_string($this->conn, $profileData['lname']);
                $userFields[] = "lname = '$lastName'";
            }

            if (!empty($profileData['phone'])) {
                $phone = mysqli_real_escape_string($this->conn, $profileData['phone']);
                $userFields[] = "phone = '$phone'";
            }

            if (!empty($profileData['date_of_birth'])) {
                $dob = mysqli_real_escape_string($this->conn, $profileData['date_of_birth']);
                $userFields[] = "date_of_birth = '$dob'";
            }

            if (!empty($profileData['driver_license_number'])) {
                $dlNum = mysqli_real_escape_string($this->conn, $profileData['driver_license_number']);
                $userFields[] = "driver_license_number = '$dlNum'";
            }

            if (isset($files['profile_image'])) {
                $profileImagePath = $this->handleFileUpload($files['profile_image'], 'images', 'profile_');
                if ($profileImagePath) {
                    $userFields[] = "img = '" . mysqli_real_escape_string($this->conn, $profileImagePath) . "'";
                }
            }

            if (!empty($userFields)) {
                $userFields[] = "updated_at = CURRENT_TIMESTAMP";
                $sql = "UPDATE users SET " . implode(", ", $userFields) . " WHERE user_id = '$userIdEscaped'";
                if (!mysqli_query($this->conn, $sql)) {
                    throw new Exception("User update failed: " . mysqli_error($this->conn));
                }
            }

            $licensePhotos = [];

            if (isset($files['license_front'])) {
                $licensePhotos['license_front_photo'] = $this->handleFileUpload($files['license_front'], 'licenses', 'front_');
            }
            if (isset($files['license_back'])) {
                $licensePhotos['license_back_photo'] = $this->handleFileUpload($files['license_back'], 'licenses', 'back_');
            }
            if (isset($files['license_with_owner'])) {
                $licensePhotos['license_with_owner_photo'] = $this->handleFileUpload($files['license_with_owner'], 'licenses', 'owner_');
            }

            if (!empty($licensePhotos)) {
                $checkSql = "SELECT license_id FROM user_driver_licenses WHERE user_id = '$userIdEscaped'";
                $checkResult = mysqli_query($this->conn, $checkSql);
                $exists = mysqli_num_rows($checkResult) > 0;

                if ($exists) {
                    $licenseFields = [];
                    foreach ($licensePhotos as $field => $path) {
                        $escapedPath = mysqli_real_escape_string($this->conn, $path);
                        $licenseFields[] = "$field = '$escapedPath'";
                    }
                    $licenseFields[] = "updated_at = CURRENT_TIMESTAMP";
                    $updateSql = "UPDATE user_driver_licenses SET " . implode(", ", $licenseFields) . " WHERE user_id = '$userIdEscaped'";
                    if (!mysqli_query($this->conn, $updateSql)) {
                        throw new Exception("License update failed: " . mysqli_error($this->conn));
                    }
                } else {
                    $columns = ['user_id'];
                    $values = ["'$userIdEscaped'"];
                    foreach ($licensePhotos as $field => $path) {
                        $columns[] = $field;
                        $values[] = "'" . mysqli_real_escape_string($this->conn, $path) . "'";
                    }
                    $insertSql = "INSERT INTO user_driver_licenses (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
                    if (!mysqli_query($this->conn, $insertSql)) {
                        throw new Exception("License insert failed: " . mysqli_error($this->conn));
                    }
                }
            }

            mysqli_commit($this->conn);
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        if (!$conn) {
            throw new Exception("Database connection failed: " . mysqli_connect_error());
        }

        $manager = new UserProfileManager($conn);

        $userId = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new Exception("User ID is required");
        }

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'get_profile':
                    $data = $manager->getUserProfile($userId);
                    echo json_encode(['success' => true, 'data' => $data]);
                    break;

                case 'update_profile':
                    $profileData = [
                        'fname' => $_POST['fname'] ?? '',
                        'lname' => $_POST['lname'] ?? '',
                        'phone' => $_POST['phone'] ?? '',
                        'date_of_birth' => $_POST['date_of_birth'] ?? '',
                        'driver_license_number' => $_POST['driver_license_number'] ?? ''
                    ];
                    $files = [
                        'profile_image' => $_FILES['profile_image'] ?? null,
                        'license_front' => $_FILES['license_front'] ?? null,
                        'license_back' => $_FILES['license_back'] ?? null,
                        'license_with_owner' => $_FILES['license_with_owner'] ?? null
                    ];
                    $result = $manager->updateUserProfile($userId, $profileData, $files);
                    echo json_encode($result);
                    break;

                default:
                    throw new Exception("Invalid action");
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
