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
	<title>Edit Profile</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php if ($user) { ?>

    <div class="d-flex justify-content-center align-items-center vh-100">
        
        <form class="shadow w-450 p-3" 
              action="php/edit.php" 
              method="post"
              enctype="multipart/form-data">

            <h4 class="display-4  fs-1">Edit Profile</h4><br>
            <!-- error -->
            <?php if(isset($_GET['error'])){ ?>
            <div class="alert alert-danger" role="alert">
              <?php echo $_GET['error']; ?>
            </div>
            <?php } ?>
            
            <!-- success -->
            <?php if(isset($_GET['success'])){ ?>
            <div class="alert alert-success" role="alert">
              <?php echo $_GET['success']; ?>
            </div>
            <?php } ?>
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" 
                   class="form-control"
                   name="fname"
                   value="<?php echo $user['fname']?>">
          </div>

          <div class="mb-3">
            <label class="form-label">User name</label>
            <input type="text" 
                   class="form-control"
                   name="uname"
                   value="<?php echo $user['username']?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" 
                   class="form-control"
                   name="pp">
            <img src="upload/<?=$user['pp']?>"
                 class="rounded-circle"
                 style="width: 70px">
            <input type="text"
                   hidden="hidden" 
                   name="old_pp"
                   value="<?=$user['pp']?>" >
          </div>

          <div class="mb-3">
  <label class="form-label">Year Level</label>
  <select class="form-control" name="year">
    <option value="">Select Year Level</option>
    <option value="1st Year" <?php echo ($user['year'] == "1st Year")?"selected":"" ?>>1st Year</option>
    <option value="2nd Year" <?php echo ($user['year'] == "2nd Year")?"selected":"" ?>>2nd Year</option>
    <option value="3rd Year" <?php echo ($user['year'] == "3rd Year")?"selected":"" ?>>3rd Year</option>
    <option value="4th Year" <?php echo ($user['year'] == "4th Year")?"selected":"" ?>>4th Year</option>
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Course</label>
  <select class="form-control" name="course">
    <option value="">Select Course</option>
    <option value="ACT" <?php echo ($user['course'] == "ACT")?"selected":"" ?>>ACT</option>
    <option value="BSIS" <?php echo ($user['course'] == "BSIS")?"selected":"" ?>>BSIS</option>
    <option value="BSCS" <?php echo ($user['course'] == "BSCS")?"selected":"" ?>>BSCS</option>
  </select>
</div>

<div class="mb-3">
  <label class="form-label">About Yourself</label>
  <textarea class="form-control" name="about" rows="1"><?php echo $user['about'] ?></textarea>
</div>
          
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="home.php" class="link-secondary">Home</a>
        </form>
    </div>
    <?php }else{ 
        header("Location: home.php");
        exit;

    } ?>
</body>
</html>

<?php }else {
	header("Location: login.php");
	exit;
} ?>