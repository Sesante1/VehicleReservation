<?php
require_once '../vendor/autoload.php';
include_once "config.php";

use Google\Client;
use Google\Service\Oauth2;

$client = new Client();
$client->setClientId('your_client_id.apps.googleusercontent.com');
$client->setClientSecret('client_secret');
$client->setRedirectUri('http://localhost:3000/signup');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Get user info from Google
    $google_oauth = new Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    $email = mysqli_real_escape_string($conn, $user_info->email);
    $full_name = mysqli_real_escape_string($conn, $user_info->name);
    $img_url = mysqli_real_escape_string($conn, $user_info->picture);

    $name_parts = explode(' ', $full_name, 2);
    $fname = $name_parts[0];
    $lname = isset($name_parts[1]) ? $name_parts[1] : '';

    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

    if (mysqli_num_rows($check_user) == 0) {
        $ran_id = rand(time(), 100000000);
        $status = "Active now";
        $verified = 1;
        $img_content = file_get_contents($img_url);

        if ($img_content !== false) {
            $image_extension = pathinfo(parse_url($img_url, PHP_URL_PATH), PATHINFO_EXTENSION);

            if (!$image_extension) {
                $image_extension = 'jpg';
            }

            $image_name = time() . "_profile." . $image_extension;

            $image_path = __DIR__ . '/images/' . $image_name;
            file_put_contents($image_path, $img_content);

            $relative_path = $image_name;

            $insert = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, img, status, verified)
        VALUES ({$ran_id}, '{$fname}', '{$lname}', '{$email}', '{$relative_path}', '{$status}', {$verified})");

            if (!$insert) {
                echo "Error: " . mysqli_error($conn);
                exit;
            }
        } else {
            echo "Failed to download image from URL.";
            exit;
        }
    }

    $get_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    if ($get_user && mysqli_num_rows($get_user) > 0) {
        $user = mysqli_fetch_assoc($get_user);
        $_SESSION['unique_id'] = $user['unique_id'];
        $_SESSION['user_id'] = $user['user_id'];
        echo "<script>window.location.href = '/';</script>";
        exit;
    }
}

$authUrl = $client->createAuthUrl();
