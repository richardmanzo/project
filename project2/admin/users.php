<?php
// admin/users.php - Full user management page
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php?error=Admin access required");
    exit;
}

include "../db_conn.php";

// Handle filters
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$course_filter = isset($_GET['course']) ? $_GET['course'] : '';

// Build query with filters
$sql = "SELECT id, username, fname, lname, email, year, course, created_at FROM users WHERE is_admin = 0";
$params = [];

if (!empty($year_filter)) {
    $sql .= " AND year = ?";
    $params[] = $year_filter;
}

if (!empty($course_filter)) {
    $sql .= " AND course = ?";
    $params[] = $course_filter;
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all available years for filter dropdown
$sql_years = "SELECT DISTINCT year FROM users WHERE is_admin = 0 ORDER BY year";
$stmt_years = $conn->prepare($sql_years);
$stmt_years->execute();
$years = $stmt_years->fetchAll(PDO::FETCH_COLUMN);

// Get all available courses for filter dropdown
$sql_courses = "SELECT DISTINCT course FROM users WHERE is_admin = 0 ORDER BY course";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->execute();
$courses = $stmt_courses->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
        }
        .user-table {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="px-3 py-4 mb-3">
                        <h4>Admin Panel</h4>
                        <p>Welcome, <?php echo $_SESSION['fname']; ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../home.php">
                                <i class="fas fa-home me-2"></i>
                                Main Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Users</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_user.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Filter Users</h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="users.php" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="year" class="form-label">Year</label>
                                        <select class="form-select" id="year" name="year">
                                            <option value="">All Years</option>
                                            <?php foreach($years as $year): ?>
                                                <option value="<?php echo $year; ?>" <?php echo ($year_filter == $year) ? 'selected' : ''; ?>>
                                                    Year <?php echo htmlspecialchars($year); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="course" class="form-label">Course</label>
                                        <select class="form-select" id="course" name="course">
                                            <option value="">All Courses</option>
                                            <?php foreach($courses as $course): ?>
                                                <option value="<?php echo $course; ?>" <?php echo ($course_filter == $course) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($course); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                        <a href="users.php" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card user-table">
                            <div class="card-header">
                                <h5 class="mb-0">User List</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Year</th>
                                                <th>Course</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['year']); ?></td>
                                                <td><?php echo htmlspecialchars($user['course']); ?></td>
                                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                                <td>
                                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if(count($users) == 0): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No users found</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>