<?php  
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['fname'])) {
if(isset($_POST['fname']) && 
   isset($_POST['uname'])){
    include "../db_conn.php";
    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $old_pp = $_POST['old_pp'];
    $id = $_SESSION['id'];
    $year = $_POST['year'];
    $course = $_POST['course'];
    $about = $_POST['about'];
    $address = $_POST['address'];
    if (empty($fname)) {
        $em = "Full name is required";
        header("Location: ../edit.php?error=$em");
        exit;
    }else if(empty($uname)){
        $em = "User name is required";
        header("Location: ../edit.php?error=$em");
        exit;
    }else {
      if (isset($_FILES['pp']['name']) AND !empty($_FILES['pp']['name'])) {

         $img_name = $_FILES['pp']['name'];
         $tmp_name = $_FILES['pp']['tmp_name'];
         $error = $_FILES['pp']['error'];

         if($error === 0){
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_to_lc = strtolower($img_ex);
            $allowed_exs = array('jpg', 'jpeg', 'png');
            if(in_array($img_ex_to_lc, $allowed_exs)){
               $new_img_name = uniqid($uname, true).'.'.$img_ex_to_lc;
              $img_upload_path = '../upload/'.$new_img_name;
               
               // Delete old profile picture if it exists
               if($old_pp != "default-pp.png"){
                  unlink('../upload/'.$old_pp);
               }

               move_uploaded_file($tmp_name, $img_upload_path);
            }else {
               $em = "You can't upload files of this type";
               header("Location: ../edit.php?error=$em");
               exit;
            }
         }else {
            $em = "Unknown error occurred while uploading";
            header("Location: ../edit.php?error=$em");
            exit;
         }
      }else {
         $new_img_name = $old_pp;
      }

      // Update the database
      $sql = "UPDATE users 
              SET fname=?, username=?, pp=?, year=?, course=?, about=?, address=?
              WHERE id=?";
      $stmt = $conn->prepare($sql);
      if($stmt){
          $stmt->execute([$fname, $uname, $new_img_name, $year, $course, $about, $address, $id]);
          
          // Update the session variables
          $_SESSION['fname'] = $fname;
          $_SESSION['username'] = $uname;
          $_SESSION['pp'] = $new_img_name;
          
          $sm = "Profile updated successfully";
          header("Location: ../edit.php?success=$sm");
          exit;
      }else{
          $em = "Unknown error occurred";
          header("Location: ../edit.php?error=$em");
          exit;
      }
    }
}else{
    header("Location: ../edit.php");
    exit;
}
}else{
    header("Location: ../login.php");
    exit;
}
?>