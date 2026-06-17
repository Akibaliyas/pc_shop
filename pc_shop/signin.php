<?php
// =============================================
// Login Page - User & Admin Authentication
// =============================================

session_start();
include "database.php";

$message = "";
$message_type = "";

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

// Process login form
if (isset($_POST['signin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['email'] = $row['email'];
        
        // Redirect based on role
        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $message = "Invalid username or password";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Laptop Shop - Sign In</title>
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
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 900px;
            max-width: 95%;
            display: flex;
        }
        
        .info-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .info-section h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .info-section p {
            line-height: 1.6;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .features {
            list-style: none;
            margin-top: 20px;
        }
        
        .features li {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .features li:before {
            content: "✓";
            margin-right: 10px;
            font-weight: bold;
        }
        
        .form-section {
            padding: 40px;
            flex: 1;
        }
        
        .form-section h2 {
            color: #333;
            margin-bottom: 30px;
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
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .demo-credentials {
            margin-top: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 10px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .info-section {
                text-align: center;
            }
            .features li {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="info-section">
            <h1>🖥️ PC Laptop Shop</h1>
            <p>Your one-stop destination for premium computers, laptops, and accessories.</p>
            <ul class="features">
                <li>High-quality products</li>
                <li>Competitive prices</li>
                <li>Expert support</li>
                <li>Fast delivery</li>
            </ul>
        </div>
        <div class="form-section">
            <h2>Sign In</h2>
            <?php if($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" name="signin">Sign In</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        
        </div>
    </div>
</body>
</html>