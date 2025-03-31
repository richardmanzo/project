<?php
// admin/add_user.php - Add new user form
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php?error=Admin access required");
    exit;
}

include "../db_conn.php";

$error = "";
$success = "";

// If form is submitted
if(isset($_POST['submit'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $year = trim($_POST['year']);
    $course = trim($_POST['course']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if(empty($fname) || empty($lname) || empty($username) || empty($email) || empty($password) || empty($year) || empty($course)) {
        $error = "All fields are required";
    } elseif(strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username already exists
        $check_sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$username]);
        if($check_stmt->fetchColumn() > 0) {
            $error = "Username already exists";
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (fname, lname, username, email, year, course, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            
            try {
                $stmt->execute([$fname, $lname, $username, $email, $year, $course, $hashed_password]);
                $success = "User created successfully";
                // Clear form inputs
                $fname = $lname = $username = $email = $year = $course = "";
            } catch(PDOException $e) {
                $error = "Error creating user: " . $e->getMessage();
            }
        }
    }
}

// Get list of courses for dropdown
$sql_courses = "SELECT DISTINCT course FROM users ORDER BY course";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->execute();
$courses = $stmt_courses->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Admin Dashboard</title>
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
        .form-card {
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
                    <h1 class="h2">Add New User</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="users.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <?php if(!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if(!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card form-card">
                            <div class="card-header">
                                <h5 class="mb-0">User Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="add_user.php">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="fname" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="fname" name="fname" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lname" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lname" name="lname" value="<?php echo isset($lname) ? htmlspecialchars($lname) : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="year" class="form-label">Year</label>
                                            <select class="form-select" id="year" name="year" required>
                                                <option value="" disabled selected>Select Year</option>
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php echo (isset($year) && $year == $i) ? 'selected' : ''; ?>>
                                                        Year <?php echo $i; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="course" class="form-label">Course</label>
                                            <input type="text" class="form-control" id="course" name="course" list="course-list" value="<?php echo isset($course) ? htmlspecialchars($course) : ''; ?>" required>
                                            <datalist id="course-list">
                                                <?php foreach($courses as $course_option): ?>
                                                    <option value="<?php echo htmlspecialchars($course_option); ?>">
                                                <?php endforeach; ?>
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <div class="form-text">Password must be at least 8 characters long.</div>
                                        </div>
                                        <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="reset" class="btn btn-outline-secondary me-md-2">Reset</button>
                                        <button type="submit" name="submit" class="btn btn-primary">Add User</button>
                                    </div>
                                </form>
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