-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2024 at 09:15 AM
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
-- Database: `digital_menu`
--

-- --------------------------------------------------------

--
-- Table structure for table `operator`
--

CREATE TABLE `operator` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir','manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operator`
--

INSERT INTO `operator` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin1', 'admin'),
(2, 'kasir', 'kasir1', 'kasir'),
(3, 'manager', 'manager1', 'manager');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Belum Dibayar','Sudah Dibayar') NOT NULL DEFAULT 'Belum Dibayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Makanan','Minuman') NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`) VALUES
(11, 'Nasi Goreng', 'Makanan', 35000, 'NasiGoreng.jpg'),
(12, 'Nasi Telur', 'Makanan', 20000, 'NasiTelur.jpg'),
(13, 'Nasi Ikan', 'Makanan', 25000, 'NasiIkan.jpg'),
(14, 'Nasi Ayam Lalapan', 'Makanan', 35000, 'NasiLalapan.jpg'),
(15, 'Nasi Ayam Rica Rica', 'Makanan', 30000, 'NasiRica.jpg'),
(16, 'Nasi Daging Rendang', 'Makanan', 40000, 'NasiRendang.jpg'),
(17, 'Nasi Campur', 'Makanan', 35000, 'NasiCampur.jpg'),
(18, 'Nasi Pecel', 'Makanan', 20000, 'NasiPecel.jpg'),
(19, 'Nasi Kuning', 'Makanan', 25000, 'NasiKuning.jpg'),
(20, 'Burger', 'Makanan', 25000, 'Burger.jpg'),
(21, 'Beef Steak', 'Makanan', 45000, 'BeefSteak.jpg'),
(22, 'Chicken Katsu', 'Makanan', 40000, 'ChickenKatsu.jpg'),
(23, 'Chicken Wings', 'Makanan', 30000, 'ChickenWings.jpg'),
(24, 'Kentang Goreng', 'Makanan', 20000, 'KentangGoreng.jpg'),
(25, 'Long Potato', 'Makanan', 25000, 'LongPotato.jpg'),
(26, 'Hotdog', 'Makanan', 25000, 'Hotdog.jpg'),
(27, 'Soto Ayam', 'Makanan', 25000, 'SotoAyam.jpg'),
(28, 'Rawon', 'Makanan', 35000, 'Rawon.jpg'),
(29, 'Mie Goreng Malaysia', 'Makanan', 40000, 'MieGorengMalaysia.jpg'),
(30, 'Mie Goreng Ayam', 'Makanan', 35000, 'MieGorengAyam.jpg'),
(31, 'Aqua', 'Minuman', 5000, 'Aqua.jpg'),
(34, 'Es Jeruk Manis', 'Minuman', 20000, 'JerukManis.jpg'),
(35, 'Es Jeruk Nipis', 'Minuman', 20000, 'JerukNipis.jpg'),
(37, 'Es Kacang Merah', 'Minuman', 20000, 'EsKacangMerah.jpg'),
(38, 'Es Teh Manis', 'Minuman', 15000, 'EsTehmanis.jpg'),
(40, 'Es Milo', 'Minuman', 10000, 'EsMilo.jpg'),
(42, 'Fanta', 'Minuman', 10000, 'Fanta.jpg'),
(43, 'Coca Cola', 'Minuman', 10000, 'Cocacola.jpg'),
(45, 'Juice Alpukat', 'Minuman', 25000, 'Alpukat.jpg'),
(47, 'Juice Orange', 'Minuman', 25000, 'JusLemon.jpg'),
(48, 'Juice Naga', 'Minuman', 25000, 'JusNaga.jpg'),
(49, 'Juice Mangga', 'Minuman', 25000, 'JusMangga.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `operator`
--
ALTER TABLE `operator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `operator`
--
ALTER TABLE `operator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
