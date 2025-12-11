-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 02:54 AM
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
-- Database: `db_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image_file` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image_file`, `title`, `link_url`, `is_active`, `display_order`, `created_at`) VALUES
(9, '1765372358_693971c676a68.png', '', 'index.php?kategori=6', 1, 0, '2025-12-10 13:12:38'),
(11, '1765373294_6939756ee6e68.png', '', 'index.php?kategori=2', 1, 0, '2025-12-10 13:28:14'),
(12, '1765374265_6939793908935.png', '', 'detail_produk.php?id=11', 1, 0, '2025-12-10 13:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Smartphone'),
(2, 'Smartwatch'),
(3, 'Laptop'),
(5, 'Aksesoris'),
(6, 'VGA');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `shipping_address`, `created_at`) VALUES
(1, 2, '', 64000.00, 'completed', 'dgfdgdrgtdgdtg', '2025-11-28 12:00:35'),
(2, 2, '', 164000.00, 'completed', 'Wonogoro', '2025-11-28 12:28:20'),
(3, 2, '', 232000.00, 'completed', 'd', '2025-11-28 12:37:45'),
(4, 2, '', 232000.00, 'completed', 'eeee', '2025-11-29 11:25:21'),
(5, 2, '', 32000.00, 'completed', 'rrrrr', '2025-12-04 05:25:08'),
(6, 2, 'ORD-1764827169-2', 200000.00, 'completed', '1212', '2025-12-04 05:46:09'),
(7, 2, 'ORD-1764829607-2', 100000.00, 'shipped', 'qwqwqwqwqwq', '2025-12-04 06:26:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `transaction_ref` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `status`, `amount`, `transaction_ref`, `created_at`) VALUES
(1, 6, 'Bank Transfer', 'pending', 200000.00, NULL, '2025-12-04 05:46:09'),
(2, 7, 'E-wallet', 'pending', 100000.00, NULL, '2025-12-04 06:26:47');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `stock`, `description`, `image`) VALUES
(7, 6, 'MSI RTX 5060', 6200000.00, 23, 'The MSI GeForce RTX 5060 8G GAMING TRIO OC delivers next-generation performance for smooth 1080p and entry-level 1440p gaming, powered by NVIDIA’s latest Ada-Next architecture. Designed with MSI’s signature premium cooling and build quality, this card brings efficiency, lower temperatures, and excellent acoustics for gamers and creators.\r\n\r\nThe card features 8GB of high-speed GDDR7 memory on a 128-bit interface, giving you improved bandwidth and responsiveness in modern titles. MSI’s TRI FROZR 3 thermal design uses three TORX Fan 5.0 fans, precision-machined Core Pipes, and a copper baseplate to keep the GPU cool and quiet even under heavy gaming loads.\r\n\r\nWith boost clocks above reference speeds thanks to MSI’s factory overclock, the RTX 5060 GAMING TRIO OC is built for reliable performance. Support for DLSS 3.5, Ray Tracing, and NVIDIA Reflex ensures better visuals, smoother gameplay, and low-latency responsiveness across competitive and AAA games.\r\n\r\nWhether you\'re upgrading a mid-range system or building a lean, power-efficient rig, the MSI RTX 5060 GAMING TRIO OC delivers modern GPU features without excessive power draw, making it one of the most balanced new-generation cards for mainstream gamers.', '5060.png'),
(8, 6, 'MSI RTX 5070', 12900000.00, 9, 'The MSI GeForce RTX 5070 12G GAMING TRIO OC brings next-generation performance to gamers and creators who want exceptional speed, efficiency, and cooling. Powered by NVIDIA’s latest architecture, this GPU delivers high-refresh-rate 1440p performance and strong 4K capability, supported by fast 12GB GDDR7 memory for smoother gameplay and heavier creative workloads.\r\n\r\nMSI’s GAMING TRIO design features the advanced TRI FROZR 3 cooling system, equipped with TORX Fan 5.0 technology, precision Core Pipes, and a robust copper baseplate to maintain low temperatures and whisper-quiet acoustics. The factory overclock adds extra performance right out of the box, while the reinforced backplate and rigid shroud ensure durability.\r\n\r\nWith support for the newest NVIDIA features like DLSS, Ray Tracing, and Reflex, the RTX 5070 offers lifelike graphics, improved responsiveness, and accelerated rendering across your favorite games and creative software. The card also includes DisplayPort 2.1 and HDMI 2.1 for high-resolution, high-refresh display setups.\r\n\r\nPacked with power but optimized for efficiency, the MSI RTX 5070 GAMING TRIO OC is an ideal choice for gamers looking to push modern titles at high settings or creators needing reliable performance for demanding workflows.', '5070.png'),
(9, 6, 'MSI RTX 5080', 19000000.00, 3, 'The MSI GeForce RTX 5080 GAMING TRIO OC is a high-performance graphics card designed for gamers and creators who want top-tier power and exceptional cooling. Built on NVIDIA’s latest architecture, the RTX 5080 delivers outstanding performance in demanding modern games, high-resolution workflows, and AI-accelerated creative applications.\r\n\r\nMSI’s GAMING TRIO cooler uses a triple-fan design, advanced heatpipe layout, and a reinforced metal backplate to keep temperatures low and noise levels quiet during heavy usage. Its factory overclock provides extra speed straight out of the box, while the sturdy shroud and premium components ensure long-term durability.\r\n\r\nWith support for the newest visual technologies—such as ray tracing, DLSS, and ultra-high-refresh displays—the RTX 5080 offers smooth gameplay, sharper visuals, and improved responsiveness across all kinds of setups. Whether you\'re playing at 1440p, pushing into 4K, or handling intense rendering tasks, the MSI RTX 5080 GAMING TRIO OC delivers powerful performance with excellent efficiency.', '5080.png'),
(10, 6, 'MSI RTX 5090', 27000000.00, 5, 'The MSI RTX 5090 stands at the pinnacle of GPU performance, crafted for gamers and creators who demand unmatched power and future-proof graphics. With blazing-fast GDDR7 memory and next-generation architecture, it delivers buttery-smooth gameplay even at 4K and beyond, with fluid frame rates, realistic ray tracing, and powerful AI-accelerated rendering.\r\n\r\nBuilt with MSI’s premium cooling and robust construction, the RTX 5090 stays cool under heavy loads while remaining surprisingly quiet. Its refined design ensures stable performance for long gaming sessions or intensive creative workloads, from 3D rendering to video editing.\r\n\r\nWhether you’re diving into the most graphically demanding AAA games, building a high-end workstation, or pushing boundaries in creative software, the MSI RTX 5090 equips you with top-tier power and reliability — a GPU ready for the future.', '5090.png'),
(11, 3, 'Asus Pro Book ', 10500000.00, 21, 'The ASUS Pro Book ZX15 is crafted for power users — from software developers and content creators to students and professionals needing reliable performance for demanding workloads. The Ryzen 9 / RTX 4070 combination delivers smooth multitasking, high‑performance video editing, 3D rendering readiness, and the flexibility to handle modern creative tools with ease. The 32 GB DDR5 RAM and speedy 1 TB PCIe SSD ensure lag‑free performance even with heavy workloads and large media files.\r\n\r\nIts 15.6″ Full HD+ display balances portability and workspace, offering a sharp, color‑accurate panel ideal for video editing or design, while the 120 Hz refresh adds smoothness even during light gaming or fast scrolling. Connectivity is complete — from high‑speed USB4/USB‑C and HDMI 2.1 to wired LAN and Wi‑Fi 6E, giving you flexibility whether you’re docking at home or working remotely.\r\n\r\nIn short — the Pro Book ZX15 is a versatile, powerful, and future‑proof laptop for anyone needing serious performance without going into bulky workstation territory.', 'asus.jpeg'),
(12, 2, 'Apple Watch 10 Black', 6320000.00, 12, 'It introduces a refined design that feels lighter, slimmer, and more elegant on the wrist, while still carrying the unmistakable identity of an Apple product. With a display that seems to blend seamlessly into the case, interactions feel more fluid and natural than ever.\r\n\r\nThe watch brings a sense of calm sophistication, whether worn casually or dressed up, and its interface feels more responsive and intuitive, making everyday tasks smoother and more enjoyable. Its new health and wellness capabilities feel more personal and supportive, adapting to your habits and guiding you toward a more mindful lifestyle.\r\n\r\nFrom morning routines to evening wind-downs, Apple Watch Series 10 fits effortlessly into your day, helping you stay connected, active, and present — all while delivering a polished, modern expression of Apple’s vision for wearable technology.', 'apple watch black.png'),
(13, 2, 'Apple Watch 10 With Purple Waist Band', 6720000.00, 7, 'It introduces a refined design that feels lighter, slimmer, and more elegant on the wrist, while still carrying the unmistakable identity of an Apple product. With a display that seems to blend seamlessly into the case, interactions feel more fluid and natural than ever.\r\n\r\nThe watch brings a sense of calm sophistication, whether worn casually or dressed up, and its interface feels more responsive and intuitive, making everyday tasks smoother and more enjoyable. Its new health and wellness capabilities feel more personal and supportive, adapting to your habits and guiding you toward a more mindful lifestyle.\r\n\r\nFrom morning routines to evening wind-downs, Apple Watch Series 10 fits effortlessly into your day, helping you stay connected, active, and present — all while delivering a polished, modern expression of Apple’s vision for wearable technology.', 'apple watch purple waist.jpg'),
(14, 2, 'Apple Watch 10 Silver With Blue Waist Band', 7150000.00, 4, 'It introduces a refined design that feels lighter, slimmer, and more elegant on the wrist, while still carrying the unmistakable identity of an Apple product. With a display that seems to blend seamlessly into the case, interactions feel more fluid and natural than ever.\r\n\r\nThe watch brings a sense of calm sophistication, whether worn casually or dressed up, and its interface feels more responsive and intuitive, making everyday tasks smoother and more enjoyable. Its new health and wellness capabilities feel more personal and supportive, adapting to your habits and guiding you toward a more mindful lifestyle.\r\n\r\nFrom morning routines to evening wind-downs, Apple Watch Series 10 fits effortlessly into your day, helping you stay connected, active, and present — all while delivering a polished, modern expression of Apple’s vision for wearable technology.', 'apple watch silver and blue waist band.jpg'),
(15, 1, 'Samsung S25 Ultra', 18900000.00, 4, 'Its design feels deliberate and sculpted, with clean lines and a confident presence that stands out without trying too hard. The display flows toward the edges with a smoothness that draws you in, creating an experience that feels immersive the moment you unlock it.\r\n\r\nUsing the device feels natural and fluid, with interactions that respond instantly and visuals that carry a sense of clarity and precision. Its intelligent camera system brings a new level of expression, capturing moments with an artistry that feels both effortless and deeply refined. Everyday tasks feel elevated, from communication to creativity, all supported by a system that quietly adapts to how you use it.\r\n\r\nFrom productivity to play, the Galaxy S25 Ultra becomes a steady companion — powerful, expressive, and thoughtfully designed to support the rhythm of your day while reflecting Samsung’s continued pursuit of innovation.', 'samsung s25.jpg'),
(16, 5, 'Steelseries Keyboard', 2140000.00, 14, 'Its design balances durability with a sleek, understated profile that fits seamlessly into any setup, whether for competitive play or focused work. The surface feels smooth and refined, while the keys invite your fingers with a satisfying blend of resistance and fluidity.\r\n\r\nEvery interaction feels intentional, creating a rhythm that becomes second nature as you type, chat, or command your games. Subtle lighting breathes life into the board, adding an ambient glow that enhances the mood without overwhelming your space. The keyboard responds with unwavering consistency, giving you a sense of control that makes long sessions feel effortless.\r\n\r\nWhether you\'re deep into a match or immersed in a project, the SteelSeries keyboard stands as a reliable companion — stable, expressive, and crafted for those who value both performance and design.', 'steel series keyboard.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@toko.com', '$2y$10$r5EheQqKdlOMdQgKjC3dlucT1EoQzuzTkqHYEdlsNYKUislgqwBPi', NULL, NULL, 'admin', '2025-11-28 11:39:52'),
(2, 'gangsta', 'test@gmail.com', '$2y$10$A.J1TxOULoHdnG1jWtFPSexh45MTSVJaTGJnD6azlCCtvH5pCfkH.', NULL, NULL, 'user', '2025-11-28 11:42:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
