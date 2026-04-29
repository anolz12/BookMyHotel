<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch real-time statistics from database
$stats = [];

// 1. Total Bookings
$totalBookingsQuery = $conn->query("SELECT COUNT(*) as count FROM bookings");
$stats['total_bookings'] = $totalBookingsQuery ? $totalBookingsQuery->fetch_assoc()['count'] : 0;

// 2. Total Users
$totalUsersQuery = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $totalUsersQuery ? $totalUsersQuery->fetch_assoc()['count'] : 0;

// 3. Total Rooms (check if rooms table exists)
$checkRoomsTable = $conn->query("SHOW TABLES LIKE 'rooms'");
if ($checkRoomsTable && $checkRoomsTable->num_rows > 0) {
    $totalRoomsQuery = $conn->query("SELECT COUNT(*) as count FROM rooms");
    $stats['total_rooms'] = $totalRoomsQuery ? $totalRoomsQuery->fetch_assoc()['count'] : 0;
} else {
    // If no rooms table, use hardcoded value based on your rooms in rooms.php
    $stats['total_rooms'] = 6; // You have 6 rooms in rooms.php
}

// 4. Total Revenue
$totalRevenueQuery = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'");
$revenueResult = $totalRevenueQuery ? $totalRevenueQuery->fetch_assoc() : ['total' => 0];
$stats['total_revenue'] = $revenueResult['total'] ? $revenueResult['total'] : 0;

// 5. Pending Bookings
$pendingBookingsQuery = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $pendingBookingsQuery ? $pendingBookingsQuery->fetch_assoc()['count'] : 0;

// 6. Today's Bookings
$today = date('Y-m-d');
$todaysBookingsQuery = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = '$today'");
$stats['todays_bookings'] = $todaysBookingsQuery ? $todaysBookingsQuery->fetch_assoc()['count'] : 0;

// 7. This Month's Revenue
$currentMonth = date('Y-m');
$monthRevenueQuery = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed' AND DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'");
$monthRevenueResult = $monthRevenueQuery ? $monthRevenueQuery->fetch_assoc() : ['total' => 0];
$stats['month_revenue'] = $monthRevenueResult['total'] ? $monthRevenueResult['total'] : 0;

// 8. Get recent bookings for table
$recentBookingsQuery = $conn->query("
    SELECT b.*, u.name as user_name, u.email as user_email 
    FROM bookings b 
    LEFT JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$recentBookings = $recentBookingsQuery ? $recentBookingsQuery->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookMyHotel</title>
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
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .recent-bookings {
            max-height: 400px;
            overflow-y: auto;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .bg-pending { background-color: #ffc107; }
        .bg-confirmed { background-color: #28a745; }
        .bg-cancelled { background-color: #dc3545; }
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
                            <a class="nav-link active" href="admin_dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_bookings.php">
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
                    <h1 class="h2 h-font">Dashboard</h1>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?> | <?php echo date('F j, Y'); ?></span>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title text-muted">Total Bookings</h5>
                                        <h2 class="mb-0"><?php echo $stats['total_bookings']; ?></h2>
                                        <small class="text-muted"><?php echo $stats['todays_bookings']; ?> today</small>
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
                                        <h5 class="card-title text-muted">Total Users</h5>
                                        <h2 class="mb-0"><?php echo $stats['total_users']; ?></h2>
                                        <small class="text-muted">Registered users</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people fs-1 text-success"></i>
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
                                        <h5 class="card-title text-muted">Total Rooms</h5>
                                        <h2 class="mb-0"><?php echo $stats['total_rooms']; ?></h2>
                                        <small class="text-muted">Available rooms</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-door-closed fs-1 text-warning"></i>
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
                                        <h5 class="card-title text-muted">Total Revenue</h5>
                                        <h2 class="mb-0">AED <?php echo number_format($stats['total_revenue'], 2); ?></h2>
                                        <small class="text-muted">AED <?php echo number_format($stats['month_revenue'], 2); ?> this month</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-currency-dollar fs-1 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row of Stats -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-clock-history me-2"></i>Pending Bookings</h5>
                                <h2 class="mb-0"><?php echo $stats['pending_bookings']; ?></h2>
                                <a href="admin_bookings.php?filter=pending" class="btn btn-sm btn-warning mt-2">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-calendar-event me-2"></i>Today's Bookings</h5>
                                <h2 class="mb-0"><?php echo $stats['todays_bookings']; ?></h2>
                                <a href="admin_bookings.php?filter=today" class="btn btn-sm btn-info mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-graph-up me-2"></i>This Month's Revenue</h5>
                                <h2 class="mb-0">AED <?php echo number_format($stats['month_revenue'], 2); ?></h2>
                                <small class="text-muted">Revenue for <?php echo date('F Y'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 h-font"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h5>
                                <a href="admin_bookings.php" class="btn btn-sm custom-bg text-white">View All Bookings</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recentBookings)): ?>
                                    <div class="table-responsive recent-bookings">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Guest</th>
                                                    <th>Room Type</th>
                                                    <th>Check-in</th>
                                                    <th>Check-out</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Booked On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentBookings as $booking): ?>
                                                <tr>
                                                    <td>#<?php echo $booking['id']; ?></td>
                                                    <td>
                                                        <?php if (!empty($booking['user_name'])): ?>
                                                            <div><strong><?php echo htmlspecialchars($booking['user_name']); ?></strong></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
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
                                                    <td>AED <?php echo $booking['total_amount']; ?></td>
                                                    <td>
                                                        <span class="badge status-badge bg-<?php 
                                                            echo $booking['status'] == 'confirmed' ? 'confirmed' : 
                                                                ($booking['status'] == 'pending' ? 'pending' : 'cancelled'); 
                                                        ?>">
                                                            <?php echo ucfirst($booking['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No recent bookings found</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0 h-font"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="admin_bookings.php?action=add" class="btn custom-bg text-white w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Add Booking
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="admin_rooms.php?action=add" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-plus-square me-2"></i>Add Room
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="admin_users.php?action=add" class="btn btn-outline-success w-100">
                                            <i class="bi bi-person-plus me-2"></i>Add User
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="admin_settings.php" class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-gear me-2"></i>Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="pt-3 mt-4 border-top">
                    <p class="text-muted text-center">BookMyHotel Admin Dashboard © <?php echo date('Y'); ?> | System Time: <?php echo date('h:i A'); ?></p>
                </footer>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh dashboard every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 300000ms = 5 minutes
        
        // Highlight current date in stats
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            console.log('Dashboard loaded on: ' + today.toLocaleDateString('en-US', dateOptions));
        });
    </script>
</body>
</html>