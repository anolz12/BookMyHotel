<?php
session_start();
include 'config.php';

// Check if payment was successful
if (!isset($_SESSION['payment_success'])) {
    header('Location: rooms.php');
    exit();
}

$payment_data = $_SESSION['payment_success'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - BookMyHotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/site.css">
    <style>
        .success-icon {
            font-size: 80px;
            color: #28a745;
        }
        .invoice-box {
            border: 2px dashed #2ec1ac;
            border-radius: 10px;
            padding: 30px;
            background: #f8f9fa;
        }
    </style>
</head>
<body class="bg-light">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-3 h-font" href="index.php">BookMyHotel</a>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="rooms.php" class="nav-link">Rooms</a></li>
                    <li class="nav-item"><a href="facilities.php" class="nav-link">Facilities</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>
                </ul>
                <div class="d-flex">
                    <span class="nav-link me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="btn btn-outline-dark shadow-none">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="success-icon mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="h-font text-success mb-3">Payment Successful!</h1>
                <p class="lead mb-4">Thank you for your booking. Your reservation has been confirmed.</p>
                
                <div class="invoice-box text-start mb-4">
                    <h4 class="h-font mb-4">Booking Confirmation</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Booking ID:</strong> #<?php echo $payment_data['booking_id']; ?></p>
                            <p><strong>Invoice No:</strong> <?php echo $payment_data['invoice_number']; ?></p>
                            <p><strong>Room Type:</strong> 
                                <?php 
                                $room_names = [
                                    'deluxe' => 'Deluxe King Room',
                                    'luxury' => 'Luxury Suite',
                                    'family' => 'Family Room',
                                    'standard' => 'Standard Room',
                                    'executive' => 'Executive Suite',
                                    'business' => 'Business Room'
                                ];
                                echo $room_names[$payment_data['room_type']] ?? ucfirst($payment_data['room_type']);
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($payment_data['check_in'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($payment_data['check_out'])); ?></p>
                            <p><strong>Amount Paid:</strong> <span class="h5 text-success">AED <?php echo $payment_data['amount']; ?></span></p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Next Steps</h6>
                    <p class="mb-2">A confirmation email has been sent to your registered email address.</p>
                    <p class="mb-0">Please present your booking ID and ID proof at check-in.</p>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-primary me-3">
                        <i class="bi bi-house"></i> Back to Home
                    </a>
                    <a href="rooms.php" class="btn custom-bg">
                        <i class="bi bi-plus-circle"></i> Book Another Room
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary ms-3">
                        <i class="bi bi-printer"></i> Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FOOTER -->
    <footer class="footer text-center py-4 mt-5">
        <p class="mb-0">&copy; 2026 BookMyHotel. All Rights Reserved.</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Clear payment success data after displaying
unset($_SESSION['payment_success']);
?>
