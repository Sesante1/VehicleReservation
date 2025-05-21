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
    <!-- Login and Signup Css-->
    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />

    <!-- <link rel="stylesheet" href="../style/general.css"> -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- <link rel="stylesheet" href="../style/view-datails.css"> -->

    <script src="javascript/router.js"></script>
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
                <li><a href="/message" onclick="route()">Chat</a></li>
            <?php else: ?>
                <li><a href="/landingPage">Home</a></li>
                <li><a href="/" onclick="route()">Find Cars</a></li>
                <li><a href="/login" onclick="route()">List Your Car</a></li>
            <?php endif; ?>
            <!-- <li><button popovertarget="myPopover">Chat</button></li> -->
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
                        <a class="button-dropdown">My profile</a>
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
    
    <!-- <div id="debug-info" style="position: fixed; bottom: 10px; right: 10px; background: #000; color: #fff; padding: 10px; font-size: 12px;">
		<div>Current URL: <span id="current-url"></span></div>
		<div>Is Admin: <span id="is-admin"></span></div>
		<div>Context: <span id="context"></span></div>
	</div>

	<script>
		// Your router script goes here
		<?php 
        include 'javascript/router.js';
        ?>

		// Debug helper
		function updateDebugInfo() {
			document.getElementById('current-url').textContent = window.location.href;
			document.getElementById('is-admin').textContent = isAdmin;
			document.getElementById('context').textContent = isAdmin ? 'admin' : 'user';
		}

		// Update debug info on load and route changes
		document.addEventListener('DOMContentLoaded', updateDebugInfo);
		window.addEventListener('popstate', updateDebugInfo);

		// Test function to check .htaccess
		function testHtaccess() {
			console.log('Testing .htaccess redirects...');
			console.log('Current URL:', window.location.href);
			console.log('Pathname:', window.location.pathname);
			console.log('Should be handled by admin.php:', window.location.pathname.startsWith('/admin'));
		}

		testHtaccess();
	</script> -->

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