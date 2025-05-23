<?php
require_once '../vendor/autoload.php';
include_once "config.php";
session_start();

use Google\Client;
use Google\Service\Oauth2;

$client = new Client();
$client->setClientId('your-client-id.apps.googleusercontent.com');
$client->setClientSecret('your-client-secret');
$client->setRedirectUri('http://localhost:3000/login');
$client->addScope('email');
$client->addScope('profile');

$err = "";

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    $email = mysqli_real_escape_string($conn, $user_info->email);

    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if ($check_user && mysqli_num_rows($check_user) > 0) {
        $user = mysqli_fetch_assoc($check_user);
        $_SESSION['unique_id'] = $user['unique_id'];
        $_SESSION['user_id'] = $user['user_id'];

        echo "<script>window.location.href = '/';</script>";
        exit;
    } else {
        echo "No account found. Please sign up first.";
        echo "<script>setTimeout(() => window.location.href = '/signup', 3000);</script>";
        // exit;
    }
} else {
    $authUrl = $client->createAuthUrl();
    // echo "<a href='" . htmlspecialchars($authUrl) . "'>Login with Google</a>";
}
