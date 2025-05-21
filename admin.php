<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style/admin.css">
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<title>Admin Veehive</title>
</head>

<body>
	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<span class="text">Veehive</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="/dashboard" onclick="route(event)">
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="/pendingUsers" onclick="route(event)">
					<i class='bx bxs-dashboard'></i>
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
				<a href="#" class="logout">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->

	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
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
		<!-- NAVBAR -->

		<div class="main-container" id="main-page">

		</div>
	</section>

	<!-- <div id="debug-info" style="position: fixed; bottom: 10px; right: 10px; background: #000; color: #fff; padding: 10px; font-size: 12px;">
		<div>Current URL: <span id="current-url"></span></div>
		<div>Is Admin: <span id="is-admin"></span></div>
		<div>Context: <span id="context"></span></div>
	</div>

	<script>
		<?php 
		include 'javascript/router.js'; 
		
		?>


		function updateDebugInfo() {
			document.getElementById('current-url').textContent = window.location.href;
			document.getElementById('is-admin').textContent = isAdmin;
			document.getElementById('context').textContent = isAdmin ? 'admin' : 'user';
		}


		document.addEventListener('DOMContentLoaded', updateDebugInfo);
		window.addEventListener('popstate', updateDebugInfo);

		function testHtaccess() {
			console.log('Testing .htaccess redirects...');
			console.log('Current URL:', window.location.href);
			console.log('Pathname:', window.location.pathname);
			console.log('Should be handled by admin.php:', window.location.pathname.startsWith('/admin'));
		}

		testHtaccess();
	</script> -->

	<script src="javascript/script.js" defer></script>
	<script src="../javascript/router.js" defer></script>
</body>

</html>