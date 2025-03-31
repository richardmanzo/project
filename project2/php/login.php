<?php 
session_start();

if(isset($_POST['uname']) && isset($_POST['pass'])){
    include "../db_conn.php";

    $uname = $_POST['uname'];
    $pass = $_POST['pass'];

    $data = "uname=".$uname;
    // Add this to your login authentication script where you set session variables
$_SESSION['is_admin'] = $row['is_admin'];
    if(password_verify($pass, $password)){
        // Set session variables
        $_SESSION['id'] = $id;
        $_SESSION['fname'] = $fname;
        $_SESSION['pp'] = $pp;
        $_SESSION['is_admin'] = $user['is_admin']; // Store admin status in session
    
        
        // Redirect based on role
        if($user['is_admin'] == 1) {
            header("Location: ../admin/dashboard.php"); // Admin dashboard
        } else {
            header("Location: ../home.php"); // Regular user home
        }
        exit;
    }

    if(empty($uname)){
        $em = "User name is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    }else if(empty($pass)){
        $em = "Password is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    }else {
        // Add this line to check connection
        if(!$conn) {
            $em = "Database connection failed";
            header("Location: ../login.php?error=$em&$data");
            exit;
        }

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if($stmt->rowCount() == 1){
            $user = $stmt->fetch();

            $username = $user['username'];
            $password = $user['password'];
            $fname = $user['fname'];
            $id = $user['id'];
            $pp = $user['pp'];

            if($username === $uname){
                // Check if password is actually hashed
                if(strlen($password) < 20) {
                    // Password is not hashed properly
                    $em = "Account setup issue. Contact administrator.";
                    header("Location: ../login.php?error=$em&$data");
                    exit;
                }
                
                if(password_verify($pass, $password)){
                    // Set session variables
                    $_SESSION['id'] = $id;
                    $_SESSION['fname'] = $fname;
                    $_SESSION['pp'] = $pp;

                    // Redirect to home page
                    header("Location: ../home.php");
                    exit;
                }else {
                    $em = "Incorrect User name or password (password verification failed)";
                    header("Location: ../login.php?error=$em&$data");
                    exit;
                }
            }else {
                $em = "Incorrect User name or password (username mismatch)";
                header("Location: ../login.php?error=$em&$data");
                exit;
            }
        }else {
            $em = "User not found in database";
            header("Location: ../login.php?error=$em&$data");
            exit;
        }
    }
}else {
    header("Location: ../login.php?error=Invalid form submission");
    exit;
}
?>