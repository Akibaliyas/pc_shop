-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 03:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pc_shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `status`, `order_date`) VALUES
(1, 2, 2, 1, 799.99, 'delivered', '2026-05-28 13:47:35'),
(2, 2, 4, 1, 1999.99, 'shipped', '2026-05-28 13:47:35'),
(3, 3, 5, 1, 1699.99, 'confirmed', '2026-05-28 13:47:35'),
(4, 4, 8, 1, 99.99, 'shipped', '2026-05-28 13:54:57'),
(5, 4, 5, 3, 5099.97, 'pending', '2026-05-30 10:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` enum('PC','Laptop','Accessory','Component') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `description`, `image_url`, `created_at`) VALUES
(1, 'Gaming PC Intel i9', 'PC', 1499.99, 5, 'High-end gaming PC with Intel i9 processor, 32GB RAM, 1TB SSD, NVIDIA RTX 4080 graphics card', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(2, 'Office PC Bundle', 'PC', 799.99, 8, 'Complete office desktop with Intel i5, 16GB RAM, 512GB SSD, includes 24\" monitor', 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(3, 'Mini PC Intel NUC', 'PC', 599.99, 10, 'Compact mini PC perfect for home entertainment, Intel Core i7, 16GB RAM', 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(4, 'MacBook Pro M3', 'Laptop', 1999.99, 3, 'Apple MacBook Pro with M3 chip, 16GB RAM, 512GB SSD, 14-inch Liquid Retina display', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(5, 'Dell XPS 15', 'Laptop', 1699.99, 1, 'Premium laptop with Intel i7, 32GB RAM, 1TB SSD, 15.6\" 4K display', 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(6, 'ASUS ROG Zephyrus', 'Laptop', 1799.99, 4, 'Gaming laptop with AMD Ryzen 9, 32GB RAM, 1TB SSD, RTX 4070', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(7, 'HP Pavilion', 'Laptop', 699.99, 7, 'Budget laptop for students, AMD Ryzen 5, 8GB RAM, 256GB SSD', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(8, 'Logitech MX Master 3S', 'Accessory', 99.99, 19, 'Wireless ergonomic mouse with ultra-fast scrolling', 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(9, 'Mechanical Keyboard', 'Accessory', 129.99, 12, 'RGB mechanical gaming keyboard with blue switches', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(10, '4K Monitor 27\"', 'Accessory', 349.99, 8, '27-inch 4K UHD monitor with HDR and built-in speakers', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(11, 'Gaming Headset', 'Accessory', 79.99, 18, 'Surround sound gaming headset with noise-canceling mic', 'https://images.unsplash.com/photo-1599661046289-e31897846e41?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(12, 'ASUS ROG Strix B760', 'Component', 199.99, 10, 'Gaming motherboard with PCIe 5.0 and WiFi 6', 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(13, 'Samsung 1TB SSD', 'Component', 89.99, 25, 'Fast NVMe SSD storage up to 7000MB/s', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(14, 'Corsair 32GB RAM', 'Component', 149.99, 15, 'DDR4 3200MHz high-performance RAM kit', 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=400&h=300&fit=crop', '2026-05-28 13:47:35'),
(15, 'NVIDIA RTX 4070', 'Component', 599.99, 5, 'High-performance graphics card for gaming', 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=400&h=300&fit=crop', '2026-05-28 13:47:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin@pcshop.com', '1234567890', 'admin', '2026-05-28 13:47:35'),
(2, 'john_doe', '6ad14ba9986e3615423dfca256d04e3f', 'John Doe', 'john@email.com', '9876543210', 'user', '2026-05-28 13:47:35'),
(3, 'jane_smith', '5570c0cd80d575f9db152f9cc8bf1c6a', 'Jane Smith', 'jane@email.com', '5551234567', 'user', '2026-05-28 13:47:35'),
(4, 'sadi_q', '61785a8e996a36a23d1b346e28f5f816', 'Sadiq Shigri', 'sadiqshigri12@gmail.com', '03129734351', 'user', '2026-05-28 13:54:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
