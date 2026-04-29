<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_required'] = 'Please login before completing your booking.';
    $_SESSION['intended_booking_url'] = $_SERVER['REQUEST_URI'];
    header('Location: index.php?login_required=1');
    exit();
}

// Get room details from URL parameters
$room_type = $_GET['room_type'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$adults = $_GET['adults'] ?? 1;
$children = $_GET['children'] ?? 0;

// Validate required parameters
if (empty($room_type) || empty($check_in) || empty($check_out)) {
    $check_in = date('Y-m-d');
    $check_out = date('Y-m-d', strtotime('+1 day'));
}

// Calculate total amount
try {
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
} catch (Exception $e) {
    header('Location: rooms.php');
    exit();
}

if ($check_out_date <= $check_in_date) {
    header('Location: rooms.php');
    exit();
}

$nights = $check_in_date->diff($check_out_date)->days;

$room_prices = [
    'deluxe' => 720,
    'luxury' => 580,
    'family' => 250,
    'standard' => 200,
    'executive' => 320,
    'business' => 500
];

if (!isset($room_prices[$room_type])) {
    header('Location: rooms.php');
    exit();
}

$total_amount = $room_prices[$room_type] * $nights;

// Store booking details in session for payment page
$_SESSION['booking_details'] = [
    'room_type' => $room_type,
    'check_in' => $check_in,
    'check_out' => $check_out,
    'adults' => $adults,
    'children' => $children,
    'nights' => $nights,
    'total_amount' => $total_amount
];

// Redirect to payment page
header('Location: payment.php');
exit();
?>
