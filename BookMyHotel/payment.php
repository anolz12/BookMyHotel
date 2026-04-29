<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get booking details from session or redirect
if (!isset($_SESSION['booking_details'])) {
    header('Location: rooms.php');
    exit();
}

$booking_details = $_SESSION['booking_details'];

// Handle payment submission
if (isset($_POST['process_payment'])) {
    $payment_method = $_POST['payment_method'] ?? 'credit_card';
    $card_name = $_POST['card_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiry_month = $_POST['expiry_month'] ?? '';
    $expiry_year = $_POST['expiry_year'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    
    // Validate card details (basic validation)
    $errors = [];

    if ($payment_method !== 'cash') {
        if (empty($card_name)) {
            $errors[] = "Cardholder name is required";
        }

        if (empty($card_number) || !preg_match('/^\d{16}$/', str_replace(' ', '', $card_number))) {
            $errors[] = "Valid 16-digit card number is required";
        }

        if (empty($cvv) || !preg_match('/^\d{3,4}$/', $cvv)) {
            $errors[] = "Valid CVV is required";
        }

        // Check if card is expired
        $current_month = date('n');
        $current_year = date('Y');

        if (empty($expiry_month) || empty($expiry_year) || $expiry_year < $current_year || ($expiry_year == $current_year && $expiry_month < $current_month)) {
            $errors[] = "Card has expired";
        }
    }
    
    if (empty($errors)) {
        // Insert booking into database (ACTUAL BOOKING CREATION HAPPENS HERE)
        $user_id = $_SESSION['user_id'];
        $room_type = $booking_details['room_type'];
        $check_in = $booking_details['check_in'];
        $check_out = $booking_details['check_out'];
        $adults = $booking_details['adults'];
        $children = $booking_details['children'];
        $total_amount = $booking_details['total_amount'];
        
        $sql = "INSERT INTO bookings (user_id, room_type, check_in, check_out, adults, children, total_amount, status, payment_method) 
                VALUES ('$user_id', '$room_type', '$check_in', '$check_out', '$adults', '$children', '$total_amount', 'confirmed', '$payment_method')";
        
        if ($conn->query($sql) === TRUE) {
            $booking_id = $conn->insert_id;
            
            // Generate invoice
            $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
            
            // Store payment success in session
            $_SESSION['payment_success'] = [
                'booking_id' => $booking_id,
                'invoice_number' => $invoice_number,
                'amount' => $total_amount,
                'room_type' => $room_type,
                'check_in' => $check_in,
                'check_out' => $check_out
            ];
            
            // Clear booking details
            unset($_SESSION['booking_details']);
            
            // Redirect to confirmation page
            header('Location: payment_success.php');
            exit();
        } else {
            $payment_error = "Error processing booking: " . $conn->error;
        }
    } else {
        $payment_error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - BookMyHotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        *{ font-family: "Poppins", sans-serif; }
        .h-font{ font-family: "Merienda", cursive; }
        .custom-bg{
            background-color:#2ec1ac;
            color: white;
        }
        .custom-bg:hover{
            background-color:#279e8c;
            color: white;
        }
        .payment-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .payment-icon {
            font-size: 40px;
            color: #2ec1ac;
        }
        .card-input {
            background: linear-gradient(to right, #f8f9fa, #fff);
            border: 1px solid #dee2e6;
        }
        .card-input:focus {
            border-color: #2ec1ac;
            box-shadow: 0 0 0 0.25rem rgba(46, 193, 172, 0.25);
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
        <div class="col-lg-10">
            <!-- Booking Summary -->
            <div class="card shadow mb-4">
                <div class="card-header custom-bg">
                    <h4 class="mb-0 text-white"><i class="bi bi-receipt"></i> Booking Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Room Details</h6>
                            <p class="mb-1"><strong>Room Type:</strong> 
                                <?php 
                                $room_names = [
                                    'deluxe' => 'Deluxe King Room',
                                    'luxury' => 'Luxury Suite',
                                    'family' => 'Family Room',
                                    'standard' => 'Standard Room',
                                    'executive' => 'Executive Suite',
                                    'business' => 'Business Room'
                                ];
                                echo $room_names[$booking_details['room_type']] ?? ucfirst($booking_details['room_type']);
                                ?>
                            </p>
                            <p class="mb-1"><strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($booking_details['check_in'])); ?></p>
                            <p class="mb-1"><strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($booking_details['check_out'])); ?></p>
                            <p class="mb-0"><strong>Duration:</strong> <?php echo $booking_details['nights']; ?> nights</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Guest Details</h6>
                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            <p class="mb-1"><strong>Guests:</strong> <?php echo $booking_details['adults']; ?> Adult(s), <?php echo $booking_details['children']; ?> Child(ren)</p>
                            <p class="mb-0"><strong>Total Amount:</strong> <span class="h5 text-success">AED <?php echo $booking_details['total_amount']; ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="card shadow">
                <div class="card-header custom-bg">
                    <h4 class="mb-0 text-white"><i class="bi bi-credit-card"></i> Payment Details</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($payment_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $payment_error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="paymentForm">
                        <!-- Payment Methods -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6>Select Payment Method</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                        <label class="form-check-label" for="creditCard">
                                            <i class="bi bi-credit-card-fill text-primary"></i> Credit/Debit Card
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="bi bi-paypal text-primary"></i> PayPal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash">
                                        <label class="form-check-label" for="cash">
                                            <i class="bi bi-cash text-success"></i> Pay at Hotel
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details -->
                        <div id="cardDetails">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="card_name" class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control card-input" id="card_name" name="card_name" 
                                           placeholder="Name on card" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control card-input" id="card_number" name="card_number" 
                                               placeholder="1234 5678 9012 3456" maxlength="19" required>
                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="expiry_month" class="form-label">Expiry Month</label>
                                    <select class="form-select card-input" id="expiry_month" name="expiry_month" required>
                                        <option value="">Month</option>
                                        <?php for($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>">
                                                <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="expiry_year" class="form-label">Expiry Year</label>
                                    <select class="form-select card-input" id="expiry_year" name="expiry_year" required>
                                        <option value="">Year</option>
                                        <?php 
                                        $current_year = date('Y');
                                        for($i = $current_year; $i <= $current_year + 10; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control card-input" id="cvv" name="cvv" 
                                               placeholder="123" maxlength="4" required>
                                        <span class="input-group-text" id="cvvHelp">
                                            <i class="bi bi-question-circle" title="3 or 4 digit security code"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-check mt-4 mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> 
                                and <a href="#" data-bs-toggle="modal" data-bs-target="#cancellationModal">Cancellation Policy</a>
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                            <a href="rooms.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Rooms
                            </a>
                            <button type="submit" name="process_payment" class="btn custom-bg btn-lg px-5">
                                <i class="bi bi-lock-fill"></i> Pay AED <?php echo $booking_details['total_amount']; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info mt-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check fs-4 me-3"></i>
                    <div>
                        <h6 class="mb-1">Secure Payment</h6>
                        <p class="mb-0 small">Your payment information is encrypted and secure. We don't store your credit card details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Booking Policy</h6>
                <ul>
                    <li>Check-in time: 2:00 PM | Check-out time: 12:00 PM</li>
                    <li>Early check-in and late check-out subject to availability</li>
                    <li>Valid government ID required at check-in</li>
                    <li>Room rates are subject to 5% tourism fee</li>
                </ul>
                
                <h6>Cancellation Policy</h6>
                <ul>
                    <li>Free cancellation up to 48 hours before check-in</li>
                    <li>50% charge for cancellation within 48 hours of check-in</li>
                    <li>No refund for no-shows</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Policy Modal -->
<div class="modal fade" id="cancellationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancellation Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You can cancel your booking free of charge up to 48 hours before your check-in date.</p>
                <p>If you cancel within 48 hours of check-in, 50% of the total amount will be charged.</p>
                <p>No-shows will be charged the full amount.</p>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p class="mb-0">© 2025 BookMyHotel - All Rights Reserved</p>
    <p class="small mb-0"><i class="bi bi-shield-check"></i> Secure Payment Gateway</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Format card number with spaces
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
    e.target.value = formatted.substring(0, 19);
});

// Show/hide card details based on payment method
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const cardDetails = document.getElementById('cardDetails');
        const payButton = document.querySelector('button[name="process_payment"]');
        const cardFields = cardDetails.querySelectorAll('input, select');
        
        if (this.value === 'cash') {
            cardDetails.style.display = 'none';
            cardFields.forEach(field => field.required = false);
            payButton.innerHTML = '<i class="bi bi-calendar-check"></i> Complete Booking';
        } else {
            cardDetails.style.display = 'block';
            cardFields.forEach(field => field.required = true);
            payButton.innerHTML = '<i class="bi bi-lock-fill"></i> Pay AED <?php echo $booking_details['total_amount']; ?>';
        }
    });
});

// Form validation
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const termsChecked = document.getElementById('terms').checked;
    if (!termsChecked) {
        e.preventDefault();
        alert('Please accept the Terms and Conditions to proceed.');
    }
});
</script>
</body>
</html>
