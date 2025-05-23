<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
include_once "php/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veehive</title>
    <link rel="stylesheet" href="/style/navigation.css">
    <!-- <link rel="stylesheet" href="/style/user.css"> -->

    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />

    <link rel="stylesheet" href="../style/footer.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- <link rel="stylesheet" href="../style/view-datails.css"> -->

    <script src="javascript/router.js"></script>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            /* background-color: rgba(0, 0, 0, 0.5); */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 200000;
            display: none;
        }

        /* Modal Box */
        .modal-box {
            background: #fff;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 50%;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: gray;
        }

        .modal-body,
        .profile-form {
            padding: 1rem;
            overflow-y: auto;
            flex-grow: 1;
        }

        .profile-form {
            display: flex;
            flex-direction: column;
        }

        .modal-body h3 {
            text-align: center;
            margin-bottom: 1rem;
        }

        .profile-pic {
            text-align: center;
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .profile-pic img {
            width: 90px;
            height: 90px;
            background: #ddd;
            border-radius: 50%;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.25rem;
        }

        .form-group .title {
            display: block;
            margin-bottom: 0.25rem;
            color:
                #2054dc;
            font-size: 17px;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input:focus-within {
            border: 1.5px solid #2d79f3;
        }

        .modal-footer {
            padding: 1rem;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .save-button {
            background:
                #2054dc;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .save-button:hover {
            background: rgb(43, 97, 233);
        }

        .form-group img {
            width: 50px;
            height: 30px;
            border-radius: 4px;
            position: absolute;
            right: 5px;
            bottom: 5px;
        }
    </style>
</head>

<body class="">
    <nav>
        <div class="logo">
            <a href="/landingPage" onclick="route()">
                <h1>Veehive</h1>
            </a>
        </div>
        <ul class="relative" id="menuList">
            <?php if ($isLoggedIn): ?>
                <li><a href="/" onclick="route()">Find Cars</a></li>
                <li><a href="/Cars" onclick="route()">List Your Car</a></li>
                <!-- <li><a href="/message" onclick="route()">Chat</a></li> -->
            <?php else: ?>
                <li><a href="/landingPage">Home</a></li>
                <li><a href="/" onclick="route()">Find Cars</a></li>
                <li><a href="/login" onclick="route()">List Your Car</a></li>
            <?php endif; ?>
        </ul>
        <div class="right-side-container">
            <?php if ($isLoggedIn): ?>
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM users WHERE user_id = {$_SESSION['user_id']}");
                $row = mysqli_fetch_assoc($sql);
                ?>
                <div class="profile-container" id="profileBtn">
                    <div class="profile">
                        <img src="php/images/<?php echo $row['img']; ?>" alt="Image">
                    </div>
                    <div class="dropdown-content" id="myDropdown">
                        <div class="profile">
                            <img src="php/images/<?php echo $row['img']; ?>" alt="Image">
                            <span><?php echo $row['email'] ?></span>
                        </div>
                        <a onclick="openProfileModal()" class="button-dropdown">My profile</a>
                        <a href="php/logout.php?logout_id=<?php echo $row['user_id']; ?>" class="button-dropdown">Sign out</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="button-container">
                    <button class="login" onclick="window.location.href='/login';" onclick="route()">
                        Login
                    </button>
                    <button class="signup" onclick="window.location.href='/signup';" onclick="route()">Signup</button>
                </div>
            <?php endif; ?>

            <div class="menu-icon">
                <i class="fa-solid fa-bars" onclick="toggleMenu()"></i>
            </div>
        </div>
    </nav>

    <div class="main-container" id="main-page">

    </div>

    <div popover id="myPopover" class="wrapper">
        <section class="users">
            <header>
                <h1>INBOX</h1>
            </header>
            <div class="search">
                <span class="text">Select an user to start chat</span>
                <input type="text" placeholder="Enter name to search...">
                <a href="#">
                    <button><i class="fas fa-search"></i></button>
                </a>
            </div>
            <div class="users-list">

            </div>
        </section>

    </div>

    <div class="modal-overlay" id="accountModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Your Account</h2>
                <button onclick="closeProfileModal()">×</button>
            </div>

            <form class="profile-form" id="profileForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <h3 id="userName"></h3>

                    <div class="profile-pic">
                        <img id="profileImage" src="" alt="Profile Picture">
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_image" accept="image/*" onchange="previewImage(this, 'profileImage')">
                    </div>

                    <div class="form-group">
                        <label class="title">Personal Information</label>
                    </div>

                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="firstName" required>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="lastName" required>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" id="dateOfBirth">
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="email" readonly>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="tel" name="phone" id="phone">
                    </div>

                    <div class="form-group">
                        <label class="title">Driver's Information</label>
                    </div>

                    <div class="form-group">
                        <label>Driver's License Number</label>
                        <input type="text" name="driver_license_number" id="driverLicenseNumber">
                    </div>

                    <div class="form-group">
                        <img id="licenseFrontImage" />
                        <label>License Front Photo</label>
                        <input type="file" name="license_front" accept="image/*" onchange="previewImage(this, 'licenseFrontImage')">
                    </div>

                    <div class="form-group">
                        <img id="licenseBackImage" />
                        <label>License Back Photo</label>
                        <input type="file" name="license_back" accept="image/*" onchange="previewImage(this, 'licenseBackImage')">
                    </div>

                    <div class="form-group">
                        <img id="licenseWithOwnerImage" />
                        <label>License Photo with Owner</label>
                        <input type="file" name="license_with_owner" accept="image/*" onchange="previewImage(this, 'licenseWithOwnerImage')">
                    </div>

                    <div id="successMessage" class="success-message" style="display: none;"></div>
                    <div id="errorMessage" class="error-message" style="display: flex;"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-button" id="saveButton">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentUserId = <?php echo $_SESSION['user_id']; ?>;


        function openProfileModal() {
            document.getElementById("accountModal").style.display = "flex";
            loadUserProfile();
        }

        function closeProfileModal() {
            document.getElementById("accountModal").style.display = "none";
            clearMessages();
        }

        function previewImage(input, imgId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function loadUserProfile() {
            try {
                showLoading(true);

                const formData = new FormData();
                formData.append('action', 'get_profile');
                formData.append('user_id', currentUserId);

                const response = await fetch('/php/user_profile_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success && result.data) {
                    populateForm(result.data);
                } else {
                    showError('Failed to load profile data: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                showError('Error loading profile: ' + error.message);
            } finally {
                showLoading(false);
            }
        }

        function populateForm(userData) {
            document.getElementById('userName').textContent =
                (userData.fname || '') + ' ' + (userData.lname || '');

            document.getElementById('firstName').value = userData.fname || '';
            document.getElementById('lastName').value = userData.lname || '';
            document.getElementById('email').value = userData.email || '';
            document.getElementById('phone').value = userData.phone || '';
            document.getElementById('dateOfBirth').value = userData.date_of_birth || '';
            document.getElementById('driverLicenseNumber').value = userData.driver_license_number || '';

            if (userData.profile_image) {
                document.getElementById('profileImage').src = 'php/images/' + userData.profile_image;
            }

            if (userData.license_front_photo) {
                document.getElementById('licenseFrontImage').src = 'php/uploads/licenses/' + userData.license_front_photo;
            }

            if (userData.license_back_photo) {
                document.getElementById('licenseBackImage').src = 'php/uploads/licenses/' + userData.license_back_photo;
            }

            if (userData.license_with_owner_photo) {
                document.getElementById('licenseWithOwnerImage').src = 'php/uploads/licenses/' + userData.license_with_owner_photo;
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                showLoading(true);
                clearMessages();

                const formData = new FormData(this);
                formData.append('action', 'update_profile');
                formData.append('user_id', currentUserId);

                const response = await fetch('/php/user_profile_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showSuccess('Profile updated successfully!');
                    setTimeout(() => {
                        loadUserProfile();
                    }, 1000);
                } else {
                    showError('Failed to update profile: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                showError('Error updating profile: ' + error.message);
            } finally {
                showLoading(false);
            }
        });

        function showLoading(isLoading) {
            const modalBox = document.querySelector('.modal-box');
            const saveButton = document.getElementById('saveButton');

            if (isLoading) {
                modalBox.classList.add('loading');
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
            } else {
                modalBox.classList.remove('loading');
                saveButton.disabled = false;
                saveButton.textContent = 'Save Changes';
            }
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';

            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 5000);
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function clearMessages() {
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
        }

        document.getElementById('accountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProfileModal();
            }
        });
    </script>

    <script>
        let menuList = document.getElementById("menuList");
        menuList.style.maxHeight = "0px";

        function toggleMenu() {
            if (menuList.style.maxHeight == "0px") {
                menuList.style.maxHeight = "300px";
            } else {
                menuList.style.maxHeight = "0px";
            }
        }

        const profileBtn = document.getElementById("profileBtn");
        const dropdown = document.getElementById("myDropdown");

        profileBtn.addEventListener("click", function() {
            dropdown.classList.toggle("show");
        });

        window.addEventListener("click", function(event) {
            if (!profileBtn.contains(event.target)) {
                dropdown.classList.remove("show");
            }
        });
    </script>

    <script src="../javascript/users.js"></script>
    <script src="../javascript/router."></script>
    <script src="https://kit.fontawesjsome.com/f8e1a90484.js" crossorigin="anonymous"></script>

</body>

</html>