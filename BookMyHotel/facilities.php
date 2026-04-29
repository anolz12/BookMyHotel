<?php
// facilities.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities - BookMyHotel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/site.css">

    <style>
        .facility-card img { height: 180px; object-fit: cover; }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">BookMyHotel</a>

        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="rooms.php" class="nav-link">Rooms</a></li>
                <li class="nav-item"><a href="facilities.php" class="nav-link active">Facilities</a></li>
                <li class="nav-item"><a href="contact.php" class="nav-link">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HEADER -->
<div class="container page-hero text-center">
    <span class="section-kicker">Guest comforts</span>
    <h2 class="fw-bold h-font mt-2">Our Hotel Facilities</h2>
    <p>We offer practical, polished facilities to make your stay comfortable from check-in to checkout.</p>
</div>

<!-- FACILITIES GRID -->
<div class="container">
    <div class="row g-4">

        <!-- Swimming Pool -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/pool.png" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-water fs-1 text-primary"></i>
                    <h5 class="mt-2">Swimming Pool</h5>
                    <p class="text-muted small">Open from 6am to 10pm. Towels provided.</p>
                </div>
            </div>
        </div>

        <!-- Gym -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/gym.png" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-heart-pulse fs-1 text-danger"></i>
                    <h5 class="mt-2">Fitness Gym</h5>
                    <p class="text-muted small">Modern equipment with trainer support.</p>
                </div>
            </div>
        </div>

        <!-- Spa -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/spa.png" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-flower1 fs-1 text-success"></i>
                    <h5 class="mt-2">Spa & Wellness</h5>
                    <p class="text-muted small">Relaxing massages & steam rooms.</p>
                </div>
            </div>
        </div>

        <!-- Restaurant -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/restaurant.jpg" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-cup-hot fs-1 text-warning"></i>
                    <h5 class="mt-2">Multi-Cuisine Restaurant</h5>
                    <p class="text-muted small">Breakfast, lunch & dinner included.</p>
                </div>
            </div>
        </div>

        <!-- Parking -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/parking.jpg" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-car-front fs-1 text-dark"></i>
                    <h5 class="mt-2">Free Parking</h5>
                    <p class="text-muted small">Secure 24/7 monitored parking.</p>
                </div>
            </div>
        </div>

        <!-- WiFi -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/wifi.png" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-wifi fs-1 text-info"></i>
                    <h5 class="mt-2">High-Speed WiFi</h5>
                    <p class="text-muted small">Unlimited access throughout the hotel.</p>
                </div>
            </div>
        </div>

        <!-- Conference Hall -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/conference.jpg" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1 text-primary"></i>
                    <h5 class="mt-2">Conference Hall</h5>
                    <p class="text-muted small">Perfect for meetings & corporate events.</p>
                </div>
            </div>
        </div>

        <!-- Room Service -->
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="facility-card h-100">
                <img src="images/facilities/roomservice.jpg" class="card-img-top">
                <div class="card-body text-center">
                    <i class="bi bi-bell fs-1 text-secondary"></i>
                    <h5 class="mt-2">24/7 Room Service</h5>
                    <p class="text-muted small">Call anytime for food or assistance.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<div class="footer text-center p-4 mt-5">
    &copy; 2026 BookMyHotel | All Rights Reserved
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
