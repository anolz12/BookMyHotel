<?php
session_start();
include 'config.php';

$login_required_message = $_SESSION['login_required'] ?? '';
unset($_SESSION['login_required']);

// Handle Registration
if (isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $pincode = $conn->real_escape_string($_POST['pincode']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $register_error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($check_email->num_rows > 0) {
            $register_error = "Email already exists!";
        } else {
            // Handle file upload
            $picture = '';
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
                $target_dir = "images/users/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $picture = basename($_FILES["picture"]["name"]);
                $target_file = $target_dir . $picture;
                move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (name, email, phone, address, pincode, dob, password, picture) 
                    VALUES ('$name', '$email', '$phone', '$address', '$pincode', '$dob', '$hashed_password', '$picture')";
            
            if ($conn->query($sql) === TRUE) {
                $register_success = "Registration successful! You can now login.";
            } else {
                $register_error = "Error: " . $conn->error;
            }
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            if (!empty($_SESSION['intended_booking_url'])) {
                $redirect_url = $_SESSION['intended_booking_url'];
                unset($_SESSION['intended_booking_url']);
                header('Location: ' . $redirect_url);
                exit();
            }

            $login_success = "Login successful! Welcome back, " . $user['name'] . "!";
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyHotel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/site.css">

    <style>
        .swiper{ width: 100%; }
        @media screen and (max-width: 575px){
            .availability-form{ margin-top: 0px; padding: 0 35px; }
        }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">BookMyHotel</a>

        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="facilities.php">Facilities</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            </ul>

            <div class="d-flex">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="nav-link me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="btn btn-outline-dark shadow-none">Logout</a>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-dark shadow-none me-2" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Login</button>
                    <button type="button" class="btn btn-outline-dark shadow-none" data-bs-toggle="modal"
                            data-bs-target="#registerModal">Register</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if (!empty($login_required_message)): ?>
<div class="container mt-3">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle-fill"></i> <?php echo htmlspecialchars($login_required_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-circle fs-3 me-2"></i>User Login</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <?php if(isset($login_success)): ?>
                        <div class="alert alert-success"><?php echo $login_success; ?></div>
                    <?php endif; ?>
                    <?php if(isset($login_error)): ?>
                        <div class="alert alert-danger"><?php echo $login_error; ?></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control shadow-none" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control shadow-none" name="password" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="login" class="btn btn-dark shadow-none">Login</button>
                        <a href="#">Forgot password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REGISTER MODAL -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-lines-fill fs-3 me-2"></i>User Registration</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <?php if(isset($register_success)): ?>
                        <div class="alert alert-success"><?php echo $register_success; ?></div>
                    <?php endif; ?>
                    <?php if(isset($register_error)): ?>
                        <div class="alert alert-danger"><?php echo $register_error; ?></div>
                    <?php endif; ?>

                    <span class="badge bg-light text-dark mb-3 text-wrap">
                        Note: Your details must match your Passport/ID for check-in.
                    </span>

                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control shadow-none" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control shadow-none" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control shadow-none" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Picture</label>
                                <input type="file" class="form-control shadow-none" name="picture" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control shadow-none" name="pincode" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control shadow-none" name="dob" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control shadow-none" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control shadow-none" name="confirm_password" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" name="register" class="btn btn-dark shadow-none">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SWIPER CAROUSEL -->
<div class="container-fluid px-0">
    <div class="swiper">
        <div class="hero-copy">
            <span class="section-kicker text-white">Curated hotel stays</span>
            <h1 class="h-font mt-2 mb-3">Find a better room for your next trip</h1>
            <p class="mb-4">Browse polished rooms, compare dates instantly, and complete your booking in a few simple steps.</p>
            <a href="rooms.php" class="btn custom-bg btn-lg shadow-none">Explore Rooms</a>
        </div>
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="images/carousel/1.png" alt="Hotel lobby"></div>
            <div class="swiper-slide"><img src="images/carousel/2.png" alt="Luxury hotel room"></div>
            <div class="swiper-slide"><img src="images/carousel/3.png" alt="Hotel swimming pool"></div>
            <div class="swiper-slide"><img src="images/carousel/4.png" alt="Hotel dining area"></div>
            <div class="swiper-slide"><img src="images/carousel/5.png" alt="Hotel bedroom"></div>
            <div class="swiper-slide"><img src="images/carousel/6.png" alt="Hotel exterior"></div>
        </div>

        <!-- Controls -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<!-- CHECK AVAILABILITY -->
<div class="container my-5 availability-form booking-panel">
    <div class="row">
        <div class="col-lg-12 panel p-4">
            <span class="section-kicker">Plan your stay</span>
            <h5 class="mt-1 mb-3">Check Booking Availability</h5>
            <form method="GET" action="rooms.php">
                <div class="row align-items-end g-3">
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Check-in</label>
                        <input type="date" name="check_in" class="form-control shadow-none" required>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Check-out</label>
                        <input type="date" name="check_out" class="form-control shadow-none" required>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Adults</label>
                        <select class="form-select shadow-none" name="adults">
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Children</label>
                        <select class="form-select shadow-none" name="children">
                            <option value="0">None</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="submit" class="btn custom-bg w-100 shadow-none">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- OUR ROOMS -->
<div class="container mt-5 pt-4 mb-4 text-center">
    <span class="section-kicker">Popular choices</span>
    <h2 class="fw-bold h-font mt-2">Our Rooms</h2>
    <p class="text-muted">Comfortable stays for couples, families, and business travellers.</p>
</div>

<div class="container">
    <div class="row">

        <!-- Room 1 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="room-card h-100">
                <img src="images/rooms/room1.jpg" class="card-img-top" alt="Madison Hotel deluxe room">
                <div class="card-body">
                    <h5 class="card-title">Madison Hotel</h5>
                    <h5 class="card-title">Deluxe Room</h5>
                    <p class="card-text text-muted">Paris, France</p>
                    <p class="card-text">A modern and comfortable room with a beautiful city view.</p>
                    <h6 class="price-pill mb-3">AED 720 / night</h6><br>
                    <a href="rooms.php" class="btn btn-sm custom-bg shadow-none">Book Now</a>
                    <a href="rooms.php" class="btn btn-sm btn-outline-dark shadow-none">More Details</a>
                </div>
            </div>
        </div>

        <!-- Room 2 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="room-card h-100">
                <img src="images/rooms/room2.jpg" class="card-img-top" alt="Hilton Millennium luxury suite">
                <div class="card-body">
                    <h5 class="card-title">Hilton Millennium</h5>
                    <h5 class="card-title">Luxury Suite</h5>
                    <p class="card-text text-muted">Bangkok, Thailand</p>
                    <p class="card-text">Spacious suite with premium facilities and ocean view.</p>
                    <h6 class="price-pill mb-3">AED 580 / night</h6><br>
                    <a href="rooms.php" class="btn btn-sm custom-bg shadow-none">Book Now</a>
                    <a href="rooms.php" class="btn btn-sm btn-outline-dark shadow-none">More Details</a>
                </div>
            </div>
        </div>

        <!-- Room 3 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="room-card h-100">
                <img src="images/rooms/room3.jpg" class="card-img-top" alt="Radisson Blu family room">
                <div class="card-body">
                    <h5 class="card-title">Radisson Blu Hotel</h5>
                    <h5 class="card-title">Family Room</h5>
                    <p class="card-text text-muted">London, England</p>
                    <p class="card-text">Perfect for families, includes free breakfast and play area.</p>
                    <h6 class="price-pill mb-3">AED 250 / night</h6><br>
                    <a href="rooms.php" class="btn btn-sm custom-bg shadow-none">Book Now</a>
                    <a href="rooms.php" class="btn btn-sm btn-outline-dark shadow-none">More Details</a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- FACILITIES -->
<div class="container mt-5 pt-4 mb-4 text-center">
    <span class="section-kicker">Guest comforts</span>
    <h2 class="fw-bold h-font mt-2">Our Facilities</h2>
</div>

<div class="container">
    <div class="row g-4">

        <!-- Facility 1 -->
        <div class="col-lg-3 col-md-6">
            <div class="facility-card p-4 text-center h-100">
                <img src="images/facilities/wifi.png" class="facility-icon mb-3" alt="Wi-Fi icon">
                <h5 class="mb-2">Free Wi-Fi</h5>
                <p class="text-muted small">High-speed internet access available 24/7 for all guests.</p>
            </div>
        </div>

        <!-- Facility 2 -->
        <div class="col-lg-3 col-md-6">
            <div class="facility-card p-4 text-center h-100">
                <img src="images/facilities/pool.png" class="facility-icon mb-3" alt="Swimming pool icon">
                <h5 class="mb-2">Swimming Pool</h5>
                <p class="text-muted small">Relax and enjoy our luxurious temperature-controlled pool.</p>
            </div>
        </div>

        <!-- Facility 3 -->
        <div class="col-lg-3 col-md-6">
            <div class="facility-card p-4 text-center h-100">
                <img src="images/facilities/gym.png" class="facility-icon mb-3" alt="Gym icon">
                <h5 class="mb-2">Gym & Fitness</h5>
                <p class="text-muted small">Modern gym with personal trainers and premium equipment.</p>
            </div>
        </div>

        <!-- Facility 4 -->
        <div class="col-lg-3 col-md-6">
            <div class="facility-card p-4 text-center h-100">
                <img src="images/facilities/spa.png" class="facility-icon mb-3" alt="Spa icon">
                <h5 class="mb-2">Spa & Wellness</h5>
                <p class="text-muted small">Professional spa services for relaxation and rejuvenation.</p>
            </div>
        </div>

    </div>

    <div class="text-center mt-4">
        <a href="facilities.php" class="btn btn-outline-dark shadow-none">
            More Facilities >>
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer text-center py-4 mt-5">
    <p class="mb-0">&copy; 2026 BookMyHotel. All Rights Reserved.</p>
</footer>

<!-- REQUIRED SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>

<script>
    var swiper = new Swiper(".swiper", {
        loop: true,
        grabCursor: true,
        autoplay: { delay: 2500, disableOnInteraction: false },
        pagination: { el: ".swiper-pagination", clickable: true },
        navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    });

    <?php if (!empty($login_required_message)): ?>
    new bootstrap.Modal(document.getElementById('loginModal')).show();
    <?php endif; ?>
</script>

</body>
</html>
