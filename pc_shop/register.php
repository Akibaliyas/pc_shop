<?php
// =============================================
// User Registration Page
// =============================================

session_start();
include "database.php";

$message = "";
$message_type = "";
$success = false;

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

// Process registration form
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);
    
    // Validation
    $errors = [];
    
    // Check if username exists
    $check_query = "SELECT id FROM users WHERE username='$username'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $errors[] = "Username already exists. Please choose another.";
    }
    
    // Check if email exists
    $check_email = "SELECT id FROM users WHERE email='$email'";
    $check_email_result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($check_email_result) > 0) {
        $errors[] = "Email already registered. Please use another email.";
    }
    
    // Check password match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    
    // Check password length
    if (strlen($_POST['password']) < 4) {
        $errors[] = "Password must be at least 4 characters long.";
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $insert_query = "INSERT INTO users (username, full_name, email, phone, password, role) 
                         VALUES ('$username', '$full_name', '$email', '$phone', '$password', 'user')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success = true;
            $message = "✅ Registration successful! You can now login.";
            $message_type = "success";
        } else {
            $message = "❌ Registration failed: " . mysqli_error($conn);
            $message_type = "error";
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PC Laptop Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 500px;
            max-width: 100%;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .form-section {
            padding: 30px;
        }
        
        .input-group {
            margin-bottom: 20px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .redirect-message {
            text-align: center;
            margin-top: 15px;
        }
        
        .redirect-message a {
            color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Create Account</h1>
            <p>Join PC Laptop Shop today!</p>
        </div>
        <div class="form-section">
            <?php if($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="redirect-message">
                    <a href="signin.php">Click here to login →</a>
                </div>
            <?php else: ?>
                <form method="post" action="">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required placeholder="Enter your full name">
                    </div>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" required placeholder="Choose a username">
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="your@email.com">
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="1234567890">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Minimum 4 characters">
                    </div>
                    <div class="input-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required placeholder="Re-enter password">
                    </div>
                    <button type="submit" name="register">Register Now</button>
                </form>
                <div class="login-link">
                    Already have an account? <a href="signin.php">Sign In</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>