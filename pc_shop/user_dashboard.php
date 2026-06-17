<?php
// =============================================
// User Dashboard - With Product Images
// =============================================

session_start();
include "database.php";

// Check if user is logged in and has user role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: signin.php");
    exit();
}

// Get all available products
$products_query = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC";
$products_result = mysqli_query($conn, $products_query);

// Get user's order history
$orders_query = "SELECT o.*, p.name as product_name, p.category, p.image_url 
                 FROM orders o 
                 JOIN products p ON o.product_id = p.id 
                 WHERE o.user_id = " . $_SESSION['user_id'] . " 
                 ORDER BY o.order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);

// Handle order placement
$order_message = "";
$order_message_type = "";

if (isset($_POST['place_order'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    
    // Get product details
    $product_query = "SELECT price, stock, name FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
    
    if ($product && $product['stock'] >= $quantity) {
        $total_price = $product['price'] * $quantity;
        
        $insert_query = "INSERT INTO orders (user_id, product_id, quantity, total_price) 
                         VALUES (" . $_SESSION['user_id'] . ", $product_id, $quantity, $total_price)";
        
        if (mysqli_query($conn, $insert_query)) {
            // Update stock
            $new_stock = $product['stock'] - $quantity;
            mysqli_query($conn, "UPDATE products SET stock = $new_stock WHERE id = $product_id");
            
            $order_message = "✅ Order placed successfully!";
            $order_message_type = "success";
            
            // Refresh products and orders
            $products_result = mysqli_query($conn, $products_query);
            $orders_result = mysqli_query($conn, $orders_query);
        } else {
            $order_message = "❌ Failed to place order. Please try again.";
            $order_message_type = "error";
        }
    } else {
        $order_message = "❌ Insufficient stock! Only " . $product['stock'] . " units available.";
        $order_message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - PC Laptop Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .product-badge {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            display: inline-block;
            margin: 15px 0 0 15px;
            border-radius: 5px;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        
        .product-category {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .product-price {
            font-size: 28px;
            color: #28a745;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .product-stock {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .product-desc {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .order-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }
        
        .order-form input {
            width: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        
        .order-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .order-btn:hover {
            transform: scale(1.02);
        }
        
        .orders-table {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .orders-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .orders-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        .order-product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-pending { background: #ffeaa7; color: #d63031; }
        .status-confirmed { background: #74b9ff; color: #0984e3; }
        .status-shipped { background: #a29bfe; color: #6c5ce7; }
        .status-delivered { background: #55efc4; color: #00b894; }
        .status-cancelled { background: #fab1a0; color: #d63031; }
        
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
        
        .no-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">🖥️ Shigri PC & Laptop Shop</div>
        <div class="user-info">
            <span>👋 Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
            <span>📧 <?php echo htmlspecialchars($_SESSION['email']); ?></span>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Welcome to Your Dashboard</h2>
            <p>Browse our latest products with images and place your orders. Track your orders easily from here.</p>
        </div>
        
        <?php if($order_message): ?>
            <div class="message <?php echo $order_message_type; ?>"><?php echo $order_message; ?></div>
        <?php endif; ?>
        
        <div class="section-title">
            <span>🛒</span>
            <span>Available Products</span>
        </div>
        <div class="products-grid">
            <?php if(mysqli_num_rows($products_result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="product-card">
                        <?php if($product['image_url']): ?>
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" onerror="this.src='https://placehold.co/400x200?text=No+Image'">
                        <?php else: ?>
                            <div class="no-image">🖥️</div>
                        <?php endif; ?>
                        <div class="product-badge"><?php echo $product['category']; ?></div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-stock">📦 Stock: <?php echo $product['stock']; ?> units</div>
                            <div class="product-desc">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...
                            </div>
                            <form method="post" class="order-form" onsubmit="return confirm('Confirm your order?')">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" min="1" max="<?php echo $product['stock']; ?>" value="1" required>
                                <button type="submit" name="place_order" class="order-btn">🛍️ Order Now</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="section-title">
            <span>📋</span>
            <span>My Order History</span>
        </div>
        <div class="orders-table">
            <?php if(mysqli_num_rows($orders_result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td>
                                    <?php if($order['image_url']): ?>
                                        <img src="<?php echo $order['image_url']; ?>" alt="Product" class="order-product-image" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
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
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="padding: 20px; text-align: center;">No orders yet. Start shopping!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>