<?php
// KostHub — Database Setup
// Run once via browser: http://localhost/Vanilla_Web/db/setup.php

$db = new mysqli('localhost', 'root', '', 'kosmanager');
if ($db->connect_error) {
  die('DB connection failed: ' . $db->connect_error);
}
$db->set_charset('utf8mb4');

// Disable foreign key checks for dropping/creating tables
$db->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop existing tables
$tables = ['logs', 'repairs', 'orders', 'requests', 'users', 'rooms', 'facilities', 'customers'];
foreach ($tables as $table) {
  $db->query("DROP TABLE IF EXISTS `$table`");
}

// 1. Create customers table
$db->query("CREATE TABLE `customers` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `wa` varchar(20) DEFAULT NULL,
  `room` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 2. Create facilities table
$db->query("CREATE TABLE `facilities` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `floor` varchar(10) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 3. Create rooms table
$db->query("CREATE TABLE `rooms` (
  `id` varchar(10) NOT NULL,
  `floor` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `rent` varchar(20) NOT NULL,
  `price` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'empty',
  `tenant` varchar(100) DEFAULT '-',
  `until` varchar(20) DEFAULT '-',
  `facilities` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 4. Create users table
$db->query("CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','admin','user') NOT NULL DEFAULT 'user',
  `customer_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 5. Create requests table
$db->query("CREATE TABLE `requests` (
  `id` varchar(10) NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `type` enum('pindah','checkout') NOT NULL,
  `detail` text DEFAULT NULL,
  `from_room` varchar(10) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 6. Create orders table
$db->query("CREATE TABLE `orders` (
  `id` varchar(20) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `room` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `total` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 7. Create repairs table
$db->query("CREATE TABLE `repairs` (
  `id` varchar(20) NOT NULL,
  `target` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `issue` varchar(255) NOT NULL,
  `reported` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `tech` varchar(100) DEFAULT '-',
  `report_count` int(11) DEFAULT 1,
  `votes` int(11) NOT NULL DEFAULT 1,
  `voted_by` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// 8. Create logs table
$db->query("CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `action` varchar(100) NOT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");


// --- Seed Customers ---
$db->query("INSERT INTO `customers` (`id`, `name`, `email`, `wa`, `room`) VALUES
('C001', 'Made Agastya Devanantha Dharmawan', 'madedeva@gmail.com', '087865927214', 'A301'),
('C002', 'Muhammad Andika Azkiya', 'andika@gmail.com', '087865314562', 'A102')");

// --- Seed Facilities ---
$db->query("INSERT INTO `facilities` (`id`, `name`, `floor`, `desc`, `status`) VALUES
('F001', 'Parkiran', '1', 'Parkiran dengan kapasitas Motor: 20 & Mobil: 5', 'pending'),
('F002', 'Dapur Bersama', '2', 'Dapur lengkap dengan kompor gas dan kulkas', 'pending')");

// --- Seed Rooms ---
$db->query("INSERT INTO `rooms` (`id`, `floor`, `type`, `rent`, `price`, `status`, `tenant`, `until`, `facilities`) VALUES
('A101', 1, 'Standar', 'Bulanan', 1500000, 'empty', '-', '-', 'AC, WiFi, Kamar mandi dalam'),
('A102', 1, 'Standar', 'Bulanan', 1500000, 'occupied', 'Muhammad Andika Azkiya', '2026-07-01', 'AC, WiFi, Kamar mandi '),
('A103', 1, 'Standar', 'Bulanan', 1500000, 'empty', '-', '-', 'AC, WiFi, Kamar mandi '),
('A104', 1, 'Standar', 'Bulanan', 1500000, 'empty', '-', '-', 'AC, WiFi, Kamar mandi '),
('A201', 2, 'VIP', 'Bulanan', 2500000, 'empty', '-', '-', 'AC, WiFi, Kamar mandi, Dapur, TV\\n\\n'),
('A301', 3, 'Executive', 'Bulanan', 3350000, 'occupied', 'Made Agastya Devanantha Dharmawan', '2026-07-01', 'AC, WiFi, Kamar mandi, Dapur, Mini-bar, 2-Kasur, TV')");

// --- Seed Users ---
// Using single quotes in PHP to prevent variable interpolation of $ in hashes
$db->query('INSERT INTO `users` (`id`, `username`, `password`, `role`, `customer_id`) VALUES
(81, \'owner\', \'$2y$10$j/BrKsFZPGIPyTHBTOKQq.XW30OW5uW.CErm3CAG8Lu9YFyXJAMFu\', \'owner\', NULL),
(82, \'admin\', \'$2y$10$i.ak9icT/2gx2lj9mJyfS.qGMaLuwkgWPdTrJyldQ5hljATVBJC6W\', \'admin\', NULL),
(92, \'Deva\', \'$2y$10$oqaKqyulqSE.e.H64A6GLeIv2KP42RedNN9h5HKLaYvBswcWOVTLO\', \'user\', \'C001\'),
(93, \'AB\', \'$2y$10$81yZqroFq0HrOWh5JX7/sOKV0IldvsJslSiWnPGILy/60cbaLanM6\', \'user\', \'C002\')');

// --- Seed Orders ---
$db->query("INSERT INTO `orders` (`id`, `customer`, `room`, `type`, `start`, `end`, `total`, `status`) VALUES
('ORD-001', 'Made Agastya Devanantha Dharmawan', 'A301', 'Bulanan', '2026-05-31', '2026-07-01', 3350000, 'paid'),
('ORD-002', 'Muhammad Andika Azkiya', 'A102', 'Bulanan', '2026-05-31', '2026-07-01', 1500000, 'paid')");

// --- Seed Repairs ---
$db->query("INSERT INTO `repairs` (`id`, `target`, `type`, `issue`, `reported`, `status`, `tech`, `report_count`, `votes`, `voted_by`) VALUES
('REP-001', 'Parkiran ', 'fasum', '1 Lampu di parkiran rusak', '2026-05-31', 'pending', '-', 1, 2, '[\"C001\",\"C002\"]'),
('REP-002', 'Dapur Bersama', 'fasum', 'Kompornya rusak', '2026-05-31', 'pending', '-', 1, 1, '[\"C002\"]')");

// --- Seed Logs ---
$logs = [
  [188, '2026-05-31 14:26:29', 'User mendaftar', 'Made Agastya Devanantha Dharmawan mendaftar sebagai user baru (C001)', 'customer'],
  [189, '2026-05-31 14:27:06', 'User mendaftar', 'Muhammad Andika Azkiya mendaftar sebagai user baru (C002)', 'customer'],
  [190, '2026-05-31 14:27:49', 'Booking oleh user', 'Made Agastya Devanantha Dharmawan memesan Kamar A301 (ORD-001)', 'order'],
  [191, '2026-05-31 14:27:53', 'Pembayaran diterima', 'ORD-001 via OVO oleh Made Agastya Devanantha Dharmawan (Masa sewa diperpanjang hingga 2026-07-01)', 'order'],
  [192, '2026-05-31 14:30:53', 'Laporan perbaikan', 'REP-001: 1 Lampu di parkiran rusak – Parkiran ', 'repair'],
  [193, '2026-05-31 14:36:24', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [194, '2026-05-31 14:36:48', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [195, '2026-05-31 14:37:02', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [196, '2026-05-31 14:41:00', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [197, '2026-05-31 14:41:06', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [198, '2026-05-31 14:41:20', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [199, '2026-05-31 14:41:23', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [200, '2026-05-31 14:41:26', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [201, '2026-05-31 14:42:15', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [202, '2026-05-31 14:42:18', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [203, '2026-05-31 14:42:21', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [204, '2026-05-31 14:42:23', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [205, '2026-05-31 14:42:28', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [206, '2026-05-31 14:46:34', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [207, '2026-05-31 14:46:47', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [208, '2026-05-31 14:46:50', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [209, '2026-05-31 14:46:55', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [210, '2026-05-31 14:49:30', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [211, '2026-05-31 14:49:33', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [212, '2026-05-31 14:49:37', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [213, '2026-05-31 14:50:55', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [214, '2026-05-31 14:50:57', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [215, '2026-05-31 14:51:00', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [216, '2026-05-31 14:56:05', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [217, '2026-05-31 14:56:08', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [218, '2026-05-31 14:56:12', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [219, '2026-05-31 14:56:18', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [220, '2026-05-31 14:56:22', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [221, '2026-05-31 14:56:24', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [222, '2026-05-31 14:56:45', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [223, '2026-05-31 14:56:53', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [224, '2026-05-31 14:57:06', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [225, '2026-05-31 15:00:02', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [226, '2026-05-31 15:00:13', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [227, '2026-05-31 15:00:15', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [228, '2026-05-31 15:14:15', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [229, '2026-05-31 15:14:19', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [230, '2026-05-31 15:14:21', 'Fasilitas diperbarui', 'F001 diperbarui', 'room'],
  [231, '2026-05-31 15:17:29', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [232, '2026-05-31 15:17:34', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [233, '2026-05-31 15:17:39', 'Perbaikan diperbarui', 'REP-001 status: repairing', 'repair'],
  [234, '2026-05-31 15:18:07', 'Dukungan laporan perbaikan', 'Tenant C002 mendukung perbaikan REP-001', 'repair'],
  [235, '2026-05-31 15:23:12', 'Perbaikan diperbarui', 'REP-001 status: pending', 'repair'],
  [236, '2026-05-31 15:23:43', 'Booking oleh user', 'Muhammad Andika Azkiya memesan Kamar A102 (ORD-002)', 'order'],
  [237, '2026-05-31 15:23:46', 'Pembayaran diterima', 'ORD-002 via DANA oleh Muhammad Andika Azkiya (Masa sewa diperpanjang hingga 2026-07-01)', 'order'],
  [238, '2026-05-31 15:24:06', 'Laporan perbaikan', 'REP-002: Kompornya rusak – Dapur Bersama', 'repair']
];

$stmt = $db->prepare("INSERT INTO `logs` (`id`, `time`, `action`, `detail`, `type`) VALUES (?, ?, ?, ?, ?)");
foreach ($logs as $log) {
  $stmt->bind_param('issss', $log[0], $log[1], $log[2], $log[3], $log[4]);
  $stmt->execute();
}

// Re-enable foreign key checks
$db->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<h2> Setup Complete!</h2>";
echo "<br><a href='../login.php'>→ Go to Login</a>";

$db->close();
?>