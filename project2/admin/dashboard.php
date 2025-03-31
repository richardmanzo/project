<?php
// admin/dashboard.php - Main admin dashboard
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php?error=Admin access required");
    exit;
}

include "../db_conn.php";

// Get total user count
$sql_count = "SELECT COUNT(*) as total FROM users WHERE is_admin = 0";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->execute();
$total_users = $stmt_count->fetch()['total'];

// Get user count by year (assuming users table has a 'year' column)
$sql_years = "SELECT year, COUNT(*) as count FROM users WHERE is_admin = 0 GROUP BY year ORDER BY year";
$stmt_years = $conn->prepare($sql_years);
$stmt_years->execute();
$users_by_year = $stmt_years->fetchAll(PDO::FETCH_ASSOC);

// Get user count by course (assuming users table has a 'course' column)
$sql_courses = "SELECT course, COUNT(*) as count FROM users WHERE is_admin = 0 GROUP BY course ORDER BY course";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->execute();
$users_by_course = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

// Get all users
$sql_users = "SELECT id, username, fname, lname, email, year, course, created_at FROM users WHERE is_admin = 0 ORDER BY id DESC";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_user.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <!-- Stats cards -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Total Users</h5>
                            <h2><?php echo $total_users; ?></h2>
                            <p class="mb-0 text-muted">Registered accounts</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>User Years</h5>
                            <div class="small" style="max-height: 120px; overflow-y: auto;">
                                <?php foreach($users_by_year as $year_data): ?>
                                <div class="d-flex justify-content-between">
                                    <span>Year <?php echo htmlspecialchars($year_data['year']); ?></span>
                                    <span class="badge bg-primary"><?php echo $year_data['count']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>User Courses</h5>
                            <div class="small" style="max-height: 120px; overflow-y: auto;">
                                <?php foreach($users_by_course as $course_data): ?>
                                <div class="d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($course_data['course']); ?></span>
                                    <span class="badge bg-success"><?php echo $course_data['count']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent users -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card user-table">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Users</h5>
                                    <a href="users.php" class="btn btn-sm btn-link">View All</a>
                                </div>
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
                                            <?php 
                                            $count = 0;
                                            foreach($users as $user):
                                                if($count >= 5) break; // Only show 5 recent users
                                                $count++;
                                            ?>
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
                                            
                                            <?php if($count == 0): ?>
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