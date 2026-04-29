<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    
    if ($stmt->execute()) {
        $success = "Booking status updated successfully!";
    } else {
        $error = "Error updating booking status: " . $conn->error;
    }
}

// Handle booking deletion
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $success = "Booking deleted successfully!";
    } else {
        $error = "Error deleting booking: " . $conn->error;
    }
}

// Get all bookings with user information (fixed query)
$bookings_query = "
    SELECT b.*, u.name as user_name, u.email as user_email, u.phone as user_phone
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
";
$bookings_result = $conn->query($bookings_query);

// Get booking statistics
$total_bookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$pending_bookings = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetch_row()[0];
$confirmed_bookings = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetch_row()[0];
$cancelled_bookings = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - BookMyHotel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        *{ font-family: "Poppins", sans-serif; }
        .h-font{ font-family: "Merienda", cursive; }
        .custom-bg{ background-color: #2ec1ac; }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #495057;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #495057;
        }
        .stat-card {
            border-left: 4px solid #2ec1ac;
        }
        .booking-card {
            transition: transform 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar bg-dark">
                <div class="position-sticky pt-3">
                    <h4 class="text-white text-center h-font mb-4">BookMyHotel Admin</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_bookings.php">
                                <i class="bi bi-calendar-check me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_rooms.php">
                                <i class="bi bi-door-closed me-2"></i>Rooms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">
                                <i class="bi bi-people me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_facilities.php">
                                <i class="bi bi-building me-2"></i>Facilities
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_settings.php">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 bg-light">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 h-font">Manage Bookings</h1>
                </div>

                <!-- Success/Error Messages -->
                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Booking Statistics -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted">Total Bookings</h5>
                                        <h2 class="mb-0"><?php echo $total_bookings; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-calendar-check fs-1 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted">Pending</h5>
                                        <h2 class="mb-0"><?php echo $pending_bookings; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clock fs-1 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted">Confirmed</h5>
                                        <h2 class="mb-0"><?php echo $confirmed_bookings; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle fs-1 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted">Cancelled</h5>
                                        <h2 class="mb-0"><?php echo $cancelled_bookings; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-x-circle fs-1 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="card shadow border-0">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 h-font">All Bookings</h5>
                    </div>
                    <div class="card-body">
                        <?php if($bookings_result && $bookings_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Guest</th>
                                            <th>Room Type</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Guests</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Booked On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = $bookings_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $booking['id']; ?></td>
                                            <td>
                                                <?php if(isset($booking['user_name'])): ?>
                                                    <div><strong><?php echo $booking['user_name']; ?></strong></div>
                                                    <small class="text-muted"><?php echo $booking['user_email']; ?></small>
                                                    <br>
                                                    <small class="text-muted"><?php echo $booking['user_phone'] ?? 'N/A'; ?></small>
                                                <?php else: ?>
                                                    <div><strong>User #<?php echo $booking['user_id']; ?></strong></div>
                                                    <small class="text-muted">User data not available</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $room_types = [
                                                    'deluxe' => 'Deluxe King Room',
                                                    'luxury' => 'Luxury Suite', 
                                                    'family' => 'Family Room',
                                                    'standard' => 'Standard Room',
                                                    'executive' => 'Executive Suite',
                                                    'business' => 'Business Room'
                                                ];
                                                echo $room_types[$booking['room_type']] ?? ucfirst($booking['room_type']);
                                                ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($booking['check_in'])); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($booking['check_out'])); ?></td>
                                            <td><?php echo $booking['adults'] . ' Adults, ' . $booking['children'] . ' Children'; ?></td>
                                            <td>AED <?php echo $booking['total_amount']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $booking['status'] == 'confirmed' ? 'success' : 
                                                         ($booking['status'] == 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                                <input type="hidden" name="status" value="confirmed">
                                                                <button type="submit" name="update_status" class="dropdown-item text-success">
                                                                    <i class="bi bi-check-circle me-2"></i>Confirm
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" name="update_status" class="dropdown-item text-warning">
                                                                    <i class="bi bi-clock me-2"></i>Mark Pending
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" name="update_status" class="dropdown-item text-danger">
                                                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a href="admin_bookings.php?delete=<?php echo $booking['id']; ?>" 
                                                               class="dropdown-item text-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this booking?')">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                <h4 class="text-muted mt-3">No Bookings Found</h4>
                                <p class="text-muted">There are no bookings in the system yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>