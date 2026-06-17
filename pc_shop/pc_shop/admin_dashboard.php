<?php
// =============================================
// Admin Dashboard - Full System Management with Images
// =============================================

session_start();
include "database.php";

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: signin.php");
    exit();
}

// Get statistics
$stats = [];
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'");
$stats['total_users'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$stats['total_products'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders WHERE status='delivered'");
$stats['total_revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='pending'");
$stats['pending_orders'] = mysqli_fetch_assoc($result)['count'];

// Handle product actions
$message = "";
$message_type = "";

if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    
    $insert = "INSERT INTO products (name, category, price, stock, description, image_url) 
                VALUES ('$name', '$category', $price, $stock, '$description', '$image_url')";
    
    if (mysqli_query($conn, $insert)) {
        $message = "✅ Product added successfully!";
        $message_type = "success";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
        $message_type = "error";
    }
}

if (isset($_GET['delete_product'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_product']);
    if (mysqli_query($conn, "DELETE FROM products WHERE id = $id")) {
        $message = "✅ Product deleted successfully!";
        $message_type = "success";
    }
}

if (isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if (mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id")) {
        $message = "✅ Order status updated!";
        $message_type = "success";
    }
}

if (isset($_POST['update_stock'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    
    if (mysqli_query($conn, "UPDATE products SET stock=$stock WHERE id=$product_id")) {
        $message = "✅ Stock updated successfully!";
        $message_type = "success";
    }
}

if (isset($_POST['update_image'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    
    if (mysqli_query($conn, "UPDATE products SET image_url='$image_url' WHERE id=$product_id")) {
        $message = "✅ Product image updated!";
        $message_type = "success";
    }
}

// Get all data for display
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
$orders = mysqli_query($conn, "SELECT o.*, u.username, u.full_name, p.name as product_name, p.image_url
                               FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               JOIN products p ON o.product_id = p.id 
                               ORDER BY o.order_date DESC");
$users = mysqli_query($conn, "SELECT id, username, full_name, email, phone, role, created_at 
                              FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PC Laptop Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .admin-badge {
            background: #ff6b6b;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        
        .section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
            display: block;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        .status-select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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
        
        .stock-form, .image-form {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .stock-form input, .image-form input {
            width: 80px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .image-form input[type="text"] {
            width: 200px;
        }
        
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
            .image-form input[type="text"] {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            🖥️ PC Laptop Shop 
            <span class="admin-badge">Admin Panel</span>
        </div>
        <div class="user-info">
            <span>👋 Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?php if($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-label">Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
        
        <!-- Add Product Section -->
        <div class="section">
            <div class="section-title">
                <span>➕</span>
                <span>Add New Product</span>
            </div>
            <form method="post">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="PC">PC</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Accessory">Accessory</option>
                        <option value="Component">Component</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" required placeholder="0">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Product description..."></textarea>
                </div>
                <div class="form-group">
                    <label>Image URL (Unsplash or any image link)</label>
                    <input type="text" name="image_url" placeholder="https://images.unsplash.com/...">
                    <small style="color: #666;">Tip: Use free images from Unsplash or Pexels</small>
                </div>
                <button type="submit" name="add_product" class="btn-primary">➕ Add Product</button>
            </form>
        </div>
        
        <!-- Manage Products Section -->
        <div class="section">
            <div class="section-title">
                <span>📦</span>
                <span>Manage Products</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Update Stock</th>
                        <th>Update Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <?php if($product['image_url']): ?>
                                    <img src="<?php echo $product['image_url']; ?>" alt="Product" class="product-thumb" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
                                <?php else: ?>
                                    🖥️
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['category']; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <form method="post" class="stock-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>">
                                    <button type="submit" name="update_stock" class="btn-warning">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" class="image-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="text" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" placeholder="Image URL">
                                    <button type="submit" name="update_image" class="btn-warning">Change</button>
                                </form>
                            </td>
                            <td>
                                <a href="?delete_product=<?php echo $product['id']; ?>" class="btn-danger" onclick="return confirm('Delete this product permanently?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Manage Orders Section -->
        <div class="section">
            <div class="section-title">
                <span>📋</span>
                <span>Manage Orders</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td>
                                <?php if($order['image_url']): ?>
                                    <img src="<?php echo $order['image_url']; ?>" alt="Product" class="product-thumb" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
                                <?php else: ?>
                                    🖥️
                                <?php endif; ?>
                            </td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <span class="status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $order['status']=='confirmed'?'selected':''; ?>>Confirmed</option>
                                        <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-primary" style="padding: 5px 10px; font-size: 12px;">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Users Section -->
        <div class="section">
            <div class="section-title">
                <span>👥</span>
                <span>Registered Users</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td>
                                <span style="padding: 3px 10px; background: <?php echo $user['role']=='admin'?'#667eea':'#28a745'; ?>; color: white; border-radius: 5px; font-size: 12px;">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>