<?php
session_start();
include 'config.php';

// Show booking success message
if (isset($_SESSION['booking_success'])) {
    $booking_success = $_SESSION['booking_success'];
    unset($_SESSION['booking_success']);
}

// Handle price calculation from search
$calculated_prices = [];
$search_params = [];
$search_error = '';

if (isset($_GET['check_in']) && isset($_GET['check_out'])) {
    $check_in = $_GET['check_in'];
    $check_out = $_GET['check_out'];
    $adults = max(1, (int)($_GET['adults'] ?? 1));
    $children = max(0, (int)($_GET['children'] ?? 0));

    try {
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;

        if ($check_out_date <= $check_in_date) {
            $search_error = 'Check-out date must be after check-in date.';
        } else {
            $search_params = [
                'check_in' => $check_in,
                'check_out' => $check_out,
                'nights' => $nights,
                'adults' => $adults,
                'children' => $children
            ];

            $room_prices = [
                'deluxe' => 720,
                'luxury' => 580,
                'family' => 250,
                'standard' => 200,
                'executive' => 320,
                'business' => 500
            ];

            foreach ($room_prices as $room_type => $price_per_night) {
                $calculated_prices[$room_type] = $price_per_night * $nights;
            }
        }
    } catch (Exception $e) {
        $search_error = 'Please choose valid travel dates.';
    }
}

$default_check_in = $search_params['check_in'] ?? date('Y-m-d');
$default_check_out = $search_params['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
$default_adults = $search_params['adults'] ?? 1;
$default_children = $search_params['children'] ?? 0;

function booking_url($room_type, $check_in, $check_out, $adults, $children) {
    return 'booking.php?' . http_build_query([
        'room_type' => $room_type,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'adults' => $adults,
        'children' => $children
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rooms - BookMyHotel</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/site.css">

<style>
    .room-card img{
        height: 250px;
        object-fit: cover;
    }
    .price-calculation {
        background: #f8f9fa;
        border-left: 4px solid #2ec1ac;
        padding: 15px;
        margin-bottom: 20px;
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
                <li class="nav-item"><a href="rooms.php" class="nav-link active fw-bold">Rooms</a></li>
                <li class="nav-item"><a href="facilities.php" class="nav-link">Facilities</a></li>
                <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="container page-hero text-center">
    <span class="section-kicker">Stay your way</span>
    <h2 class="fw-bold h-font mt-2">Our Rooms</h2>
    <p class="mb-4">Choose from city suites, family rooms, and business-friendly stays with clear pricing before you book.</p>
</div>

<!-- Display Booking Success Message -->
<?php if (isset($booking_success)): ?>
<div class="container">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo $booking_success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($search_error)): ?>
<div class="container">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($search_error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- Display Price Calculation if search was made -->
<?php if (!empty($calculated_prices)): ?>
<div class="container">
    <div class="price-calculation rounded">
        <p class="mb-1"><strong>Check-in:</strong> <?php echo $search_params['check_in']; ?> | 
           <strong>Check-out:</strong> <?php echo $search_params['check_out']; ?> | 
           <strong>Nights:</strong> <?php echo $search_params['nights']; ?> | 
           <strong>Guests:</strong> <?php echo $search_params['adults']; ?> Adult(s), 
           <?php echo $search_params['children']; ?> Child(ren)
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Search Filter -->
<div class="container">
    <div class="panel p-4 mb-5">
        <form method="GET" class="row g-3">
            <div class="col-lg-3">
                <label class="form-label fw-bold">Check-in</label>
                <input type="date" name="check_in" class="form-control shadow-none" 
                       value="<?php echo $search_params['check_in'] ?? ''; ?>" required>
            </div>

            <div class="col-lg-3">
                <label class="form-label fw-bold">Check-out</label>
                <input type="date" name="check_out" class="form-control shadow-none" 
                       value="<?php echo $search_params['check_out'] ?? ''; ?>" required>
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-bold">Adults</label>
                <select class="form-select shadow-none" name="adults">
                    <option value="1" <?php echo ($search_params['adults'] ?? 1) == 1 ? 'selected' : ''; ?>>1 Adult</option>
                    <option value="2" <?php echo ($search_params['adults'] ?? 1) == 2 ? 'selected' : ''; ?>>2 Adults</option>
                    <option value="3" <?php echo ($search_params['adults'] ?? 1) == 3 ? 'selected' : ''; ?>>3 Adults</option>
                </select>
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-bold">Children</label>
                <select class="form-select shadow-none" name="children">
                    <option value="0" <?php echo ($search_params['children'] ?? 0) == 0 ? 'selected' : ''; ?>>0 Children</option>
                    <option value="1" <?php echo ($search_params['children'] ?? 0) == 1 ? 'selected' : ''; ?>>1 Child</option>
                    <option value="2" <?php echo ($search_params['children'] ?? 0) == 2 ? 'selected' : ''; ?>>2 Children</option>
                </select>
            </div>

            <div class="col-lg-2 d-flex align-items-end">
                <button type="submit" class="btn text-white custom-bg shadow-none w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- ROOMS GRID -->
<div class="container">
    <div class="row g-4">

        <!-- ROOM CARD 1 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room1.jpg" class="w-100" alt="Madison Hotel deluxe king room">
                <div class="p-3">
                    <h5>Madison Hotel</h5>
                    <h5>Deluxe King Room</h5>
                    <p class="text-muted small">Paris, France</p>
                    <p class="text-muted small">Perfect for couples with king-size bed, balcony view & free Wi-Fi.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['deluxe']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 720 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 720 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('deluxe', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

        <!-- ROOM CARD 2 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room2.jpg" class="w-100" alt="Hilton Millennium luxury suite">
                <div class="p-3">
                    <h5>Hilton Millennium</h5>
                    <h5>Luxury Suite</h5>
                    <p class="text-muted small">Bangkok, Thailand</p>
                    <p class="text-muted small">Features a premium living area, bath tub, and complimentary breakfast.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['luxury']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 580 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 580 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('luxury', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

        <!-- ROOM CARD 3 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room3.jpg" class="w-100" alt="Radisson Blu family room">
                <div class="p-3">
                    <h5>Radisson Blu Hotel</h5>
                    <h5>Family Room</h5>
                    <p class="text-muted small">London, England</p>
                    <p class="text-muted small">Designed for families; includes 2 queen beds & a private lounge.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['family']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 250 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 250 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('family', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

        <!-- ROOM CARD 4 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room4.jpg" class="w-100" alt="Hotel Colosseum standard room">
                <div class="p-3">
                    <h5>Hotel Colosseum</h5>
                    <h5>Standard Room</h5>
                    <p class="text-muted small">Rome, Italy</p>
                    <p class="text-muted small">Affordable and comfortable with essential hotel amenities.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['standard']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 200 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 200 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('standard', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

        <!-- ROOM CARD 5 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room5.jpg" class="w-100" alt="World Centre Hotel executive suite">
                <div class="p-3">
                    <h5>World Centre Hotel</h5>
                    <h5>Executive Suite</h5>
                    <p class="text-muted small">New York, USA</p>
                    <p class="text-muted small">Premium suite with office desk, minibar & city skyline view.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['executive']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 320 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 320 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('executive', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

        <!-- ROOM CARD 6 -->
        <div class="col-lg-4 col-md-6">
            <div class="room-card h-100">
                <img src="images/rooms/room6.jpg" class="w-100" alt="Four Points by Sheraton business room">
                <div class="p-3">
                    <h5>Four Points By Sheraton</h5>
                    <h5>Business Room</h5>
                    <p class="text-muted small">Kuwait City, Kuwait</p>
                    <p class="text-muted small">Perfect for business travelers with work desk & high-speed Wi-Fi.</p>
                    <?php if (!empty($calculated_prices)): ?>
                        <h6 class="mb-2">Total Price: <span class="text-success">AED <?php echo $calculated_prices['business']; ?></span></h6>
                        <p class="text-muted small mb-2">(AED 500 x <?php echo $search_params['nights']; ?> nights)</p>
                    <?php else: ?>
                        <h6 class="mb-3">Price: AED 500 / night</h6>
                    <?php endif; ?>
                    <a href="<?php echo booking_url('business', $default_check_in, $default_check_out, $default_adults, $default_children); ?>" 
                       class="btn custom-bg text-white w-100">Book Now</a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<div class="footer text-center p-4 mt-5">
    <p class="mb-0">&copy; 2026 BookMyHotel. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
