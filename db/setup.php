<?php
// KostHub — Database Setup
// Run once via browser

$db = new mysqli('localhost', 'root', '', 'kosmanager');
if ($db->connect_error) { die('DB connection failed: ' . $db->connect_error); }
$db->set_charset('utf8mb4');

//  Create users table 
$db->query("CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('owner','admin','user') NOT NULL DEFAULT 'user',
  customer_id VARCHAR(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','user') NOT NULL DEFAULT 'user'");

//  Create requests table 
$db->query("CREATE TABLE IF NOT EXISTS requests (
  id VARCHAR(10) PRIMARY KEY,
  customer_id VARCHAR(10) NOT NULL,
  type ENUM('pindah','checkout') NOT NULL,
  detail TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  admin_note TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

//  Ensure repairs table schema upvotes compatibility 
$chk = $db->query("SHOW COLUMNS FROM repairs LIKE 'votes'");
if ($chk && $chk->num_rows === 0) {
    $db->query("ALTER TABLE repairs ADD COLUMN votes INT NOT NULL DEFAULT 1");
}
$chk2 = $db->query("SHOW COLUMNS FROM repairs LIKE 'voted_by'");
if ($chk2 && $chk2->num_rows === 0) {
    $db->query("ALTER TABLE repairs ADD COLUMN voted_by TEXT DEFAULT NULL");
}

//  Seed users 
$db->query("SET FOREIGN_KEY_CHECKS = 0");
$db->query("DELETE FROM users");

$owner_hash = password_hash('owner123', PASSWORD_DEFAULT);
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$user_hash  = password_hash('user123', PASSWORD_DEFAULT);

$db->query("INSERT INTO users (username, password, role, customer_id) VALUES
  ('owner',  '$owner_hash', 'owner', NULL),
  ('admin',  '$admin_hash', 'admin', NULL),
  ('andi',   '$user_hash',  'user',  'C001'),
  ('budi',   '$user_hash',  'user',  'C002'),
  ('cici',   '$user_hash',  'user',  'C003'),
  ('dika',   '$user_hash',  'user',  'C004'),
  ('eka',    '$user_hash',  'user',  'C005'),
  ('fajar',  '$user_hash',  'user',  'C006')
");
$db->query("SET FOREIGN_KEY_CHECKS = 1");

//  Seed sample requests 
$db->query("DELETE FROM requests");
$db->query("INSERT INTO requests (id, customer_id, type, detail, status, created_at) VALUES
  ('REQ-001', 'C002', 'pindah', '{\"toRoom\":\"B202\",\"reason\":\"Ingin kamar yang lebih luas\"}', 'pending', NOW()),
  ('REQ-002', 'C003', 'checkout', '{\"date\":\"2025-08-31\",\"reason\":\"Pindah ke luar kota\"}', 'pending', NOW())
");

echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Tables created: <b>users</b>, <b>requests</b></p>";
echo "<p>Users seeded: owner + admin + 6 tenants</p>";
echo "<p><b>Owner:</b> owner / owner123</p>";
echo "<p><b>Admin:</b> admin / admin123</p>";
echo "<p><b>Users:</b> andi, budi, cici, dika, eka, fajar / user123</p>";
echo "<br><a href='../login.php'>→ Go to Login</a>";

$db->close();
?>
