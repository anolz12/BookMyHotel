<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    $_SESSION['contact_error'] = 'Please complete all fields before sending your message.';
    header('Location: contact.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Please enter a valid email address.';
    header('Location: contact.php');
    exit();
}

$_SESSION['contact_success'] = 'Thanks, ' . $name . '. Your message has been received and our team will contact you soon.';
header('Location: contact.php');
exit();
?>
