<?php
session_start();
$contact_success = $_SESSION['contact_success'] ?? '';
$contact_error = $_SESSION['contact_error'] ?? '';
unset($_SESSION['contact_success'], $_SESSION['contact_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - BookMyHotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/site.css">

    <style>
        .footer-btn:hover { opacity: 0.7; }
        .contact-info-box {
            padding: 20px;
            border-left: 4px solid #159a8c;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">BookMyHotel</a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="facilities.php">Facilities</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="contact.php">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HEADER -->
<div class="container page-hero text-center">
    <span class="section-kicker">Need help?</span>
    <h2 class="fw-bold h-font mt-2">Contact Us</h2>
    <p>We are here to assist you 24/7. Reach out through any method below.</p>
</div>

<!-- CONTACT INFORMATION SECTION -->
<div class="container mt-4">
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="contact-info-box shadow-sm">
                <h5 class="fw-bold"><i class="bi bi-geo-alt-fill text-success me-2"></i>Address</h5>
                <p>123 Palm Avenue, Downtown Dubai, UAE</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="contact-info-box shadow-sm">
                <h5 class="fw-bold"><i class="bi bi-telephone-fill text-success me-2"></i>Phone</h5>
                <p>+971 55 123 4567<br>+971 4 987 6543</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="contact-info-box shadow-sm">
                <h5 class="fw-bold"><i class="bi bi-envelope-fill text-success me-2"></i>Email</h5>
                <p>support@bookmyhotel.com<br>info@bookmyhotel.com</p>
            </div>
        </div>

    </div>
</div>

<!-- CONTACT FORM -->
<div class="container mt-5">
    <div class="row justify-content-center">

        <div class="col-lg-7">
            <div class="panel">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Send Us a Message</h4>

                    <?php if ($contact_success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($contact_success); ?></div>
                    <?php endif; ?>
                    <?php if ($contact_error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($contact_error); ?></div>
                    <?php endif; ?>

                    <form action="send_message.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn custom-bg w-100">Send Message</button>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- GOOGLE MAP -->
<div class="container mt-5 mb-5">
    <h4 class="fw-bold text-center mb-3">Find Us on the Map</h4>

    <div class="card shadow border-0">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3608.86362989293!2d55.270782!3d25.204849!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f434d5681a3b7%3A0x8f98e65ce42928f1!2sDubai%20Mall!5e0!3m2!1sen!2sae!4v1695069631769"
            width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
    </div>
</div>

<!-- FOOTER -->
<div class="footer text-center p-4">
    <p class="m-0">&copy; 2026 BookMyHotel</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
