<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle room operations
if (isset($_POST['add_room'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $features = $_POST['features'];
    
    // Handle image upload
    $image = '';
    if ($_FILES['image']['name']) {
        $target_dir = "images/rooms/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    $stmt = $conn->prepare("INSERT INTO rooms (name, description, price, capacity, features, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $capacity, $features, $image);
    $stmt->execute();
    
    header("Location: admin_rooms.php");
    exit();
}

if (isset($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id = $room_id");
    header("Location: admin_rooms.php");
    exit();
}

// Get all rooms
$rooms = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - BookMyHotel Admin</title>
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
        .room-image {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <h4 class="text-white text-center h-font mb-4">BookMyHotel Admin</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_bookings.php">
                                <i class="bi bi-calendar-check me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_rooms.php">
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
                    <h1 class="h2 h-font">Manage Rooms</h1>
                    <button class="btn custom-bg text-white" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="bi bi-plus-circle me-2"></i>Add New Room
                    </button>
                </div>

                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Room Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Capacity</th>
                                        <th>Features</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($room = $rooms->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <img src="images/rooms/<?php echo $room['image']; ?>" class="room-image" alt="<?php echo $room['name']; ?>">
                                        </td>
                                        <td><?php echo $room['name']; ?></td>
                                        <td><?php echo substr($room['description'], 0, 50) . '...'; ?></td>
                                        <td>AED <?php echo $room['price']; ?></td>
                                        <td><?php echo $room['capacity']; ?> Persons</td>
                                        <td><?php echo $room['features']; ?></td>
                                        <td>
                                            <a href="admin_rooms.php?delete=<?php echo $room['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title h-font">Add New Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Room Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price per Night (AED)</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Capacity</label>
                                <input type="number" class="form-control" name="capacity" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Features (comma separated)</label>
                                <input type="text" class="form-control" name="features" placeholder="WiFi, AC, TV, etc.">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_room" class="btn custom-bg text-white">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>