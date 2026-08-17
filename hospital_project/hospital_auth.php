<?php
// Include the database connection file (configured with database: my_website)
include 'hospital_db.php';
session_start();

// 1. REGISTRATION PROCESS (When a new user signs up)
if (isset($_POST['register'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Secure Password Hashing
    $password = $_POST['password']; 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $role = $conn->real_escape_string($_POST['role']);

    // SQL query to check if the email is already registered
    $checkEmail = "SELECT email FROM hospital_users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='hospital_login.php';</script>";
    } else {
        // SQL query to insert new user data into the database
        $sql = "INSERT INTO hospital_users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful! Please Login.'); window.location.href='hospital_login.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// 2. LOGIN PROCESS (When a user submits the login form)
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // SQL query to fetch user data based on the entered email
    $sql = "SELECT * FROM hospital_users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifying password (Supports both new Hashed passwords and old Plain text passwords)
        if (password_verify($password, $user['password']) || $password === $user['password']) { 
            // Setting up session variables to carry user data to the dashboard
            $_SESSION['h_name'] = $user['name'];
            $_SESSION['h_email'] = $user['email'];
            $_SESSION['h_role'] = $user['role'];
            
            // Redirecting the authorized user to the premium dashboard page
            header("Location: hospital_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='hospital_login.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with this email!'); window.location.href='hospital_login.php';</script>";
    }
}
?>