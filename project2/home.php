<?php 
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['fname'])) {

include "db_conn.php";
include 'php/User.php';
$user = getUserById($_SESSION['id'], $conn);

 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php if ($user) { ?>
    <div class="d-flex justify-content-center align-items-center vh-100">
    	<div class="container">
            <div class="row">
                <!-- Profile Card - Left Side -->
                <div class="col-md-5">
                    <div class="shadow w-350 p-3 text-center">
                        <img src="upload/<?=$user['pp']?>"
                            class="img-fluid rounded-circle">
                        <h3 class="display-4"><?=$user['fname']?></h3>
                        <p class="text-muted">@<?=$user['username']?></p>
                        
                        <!-- Display Course and Year -->
                        <?php if(!empty($user['course']) || !empty($user['year'])): ?>
                        <div class="course-year-info">
                            <p class="info-tag">
                                <span class="badge bg-light text-dark mb-2">
                                    <?php 
                                    $details = [];
                                    if(!empty($user['course'])) $details[] = $user['course'];
                                    if(!empty($user['year'])) $details[] = $user['year'];
                                    echo implode(' | ', $details);
                                    ?>
                                </span>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Display About information -->
                        <?php if(!empty($user['about'])): ?>
                        <div class="about-section">
                            <h6 class="text-start text-muted">About Me</h6>
                            <p class="text-start"><?=nl2br(htmlspecialchars($user['about']))?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="action-buttons">
                            <a href="edit.php" class="btn btn-primary">
                                Edit Profile
                            </a>
                            <a href="logout.php" class="btn btn-warning">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Information - Right Side -->
                <div class="col-md-7">
                    <div class="shadow details-box p-4">
                        <h4 class="details-title mb-4">Personal Information</h4>
                        
                        <div class="detail-section mb-3">
                            <h5 class="detail-heading">Contact Details</h5>
                            <div class="detail-content">
                                <p><strong>Username:</strong> <?=$user['username']?></p>
                                <p><strong>Full Name:</strong> <?=$user['fname']?></p>
                                <?php if(!empty($user['address'])): ?>
                                <p><strong>Address:</strong></p>
                                <p class="address-text"><?=nl2br(htmlspecialchars($user['address']))?></p>
                                <?php else: ?>
                                <p><strong>Address:</strong> <em>No address provided</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="detail-section mb-3">
                            <h5 class="detail-heading">Academic Information</h5>
                            <div class="detail-content">
                                <p><strong>Course:</strong> <?=!empty($user['course']) ? $user['course'] : '<em>Not specified</em>'?></p>
                                <p><strong>Year Level:</strong> <?=!empty($user['year']) ? $user['year'] : '<em>Not specified</em>'?></p>
                            </div>
                        </div>
                        
                        <?php if(!empty($user['about'])): ?>
                        <div class="detail-section">
                            <h5 class="detail-heading">About</h5>
                            <div class="detail-content">
                                <p class="about-text"><?=nl2br(htmlspecialchars($user['about']))?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }else { 
     header("Location: login.php");
     exit;
    } ?>
</body>
</html>

<?php }else {
	header("Location: login.php");
	exit;
} ?>