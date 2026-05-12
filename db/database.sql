-- KosManager Seed Data
USE kosmanager;

-- Clear existing data
DELETE FROM logs;
DELETE FROM repairs;
DELETE FROM orders;
DELETE FROM customers;
DELETE FROM facilities;
DELETE FROM rooms;

-- Rooms
INSERT INTO rooms (id, floor, type, rent, price, status, tenant, `until`, facilities) VALUES
('A101', 1, 'Standar',   'Bulanan',  800000,   'occupied',    'Andi Pratama',   '2025-08-31', 'AC, WiFi, Kamar Mandi Dalam'),
('A102', 1, 'Standar',   'Bulanan',  800000,   'empty',       '-',              '-',          'AC, WiFi'),
('A103', 1, 'VIP',       'Bulanan',  1500000,  'cleaning',    '-',              '-',          'AC, WiFi, TV, Kamar Mandi Dalam'),
('A201', 2, 'Standar',   'Bulanan',  800000,   'occupied',    'Budi Santoso',   '2025-09-15', 'AC, WiFi'),
('A202', 2, 'VIP',       'Bulanan',  1500000,  'maintenance', '-',              '-',          'AC, WiFi, TV'),
('A203', 2, 'Standar',   'Bulanan',  800000,   'occupied',    'Cici Marlina',   '2025-07-31', 'AC, WiFi'),
('B101', 1, 'Standar',   'Harian',   150000,   'empty',       '-',              '-',          'AC, WiFi'),
('B102', 1, 'Standar',   'Harian',   150000,   'occupied',    'Dika Rahman',    '2025-07-20', 'AC, WiFi'),
('B201', 2, 'Executive', 'Bulanan',  2000000,  'occupied',    'Eka Putri',      '2025-10-31', 'AC, WiFi, TV, Mini Bar, Kamar Mandi Dalam'),
('B202', 2, 'Executive', 'Bulanan',  2000000,  'empty',       '-',              '-',          'AC, WiFi, TV, Mini Bar'),
('C101', 1, 'Standar',   'Bulanan',  800000,   'cleaning',    '-',              '-',          'AC, WiFi'),
('C201', 2, 'VIP',       'Tahunan',  15000000, 'occupied',    'Fajar Nugroho',  '2026-01-15', 'AC, WiFi, TV, Kamar Mandi Dalam');

-- Customers
INSERT INTO customers (id, name, email, wa, ktp, room, emergency1, emergency2) VALUES
('C001', 'Andi Pratama',   'andi@gmail.com',  '081234567890', '3374010101990001', 'A101', 'Ibu Ani – 081200000001', ''),
('C002', 'Budi Santoso',   'budi@gmail.com',  '081234567891', '3374010101990002', 'A201', 'Pak Budi Sr – 081200000002', ''),
('C003', 'Cici Marlina',   'cici@gmail.com',  '081234567892', '3374010101990003', 'A203', '', ''),
('C004', 'Dika Rahman',    'dika@gmail.com',  '081234567893', '3374010101990004', 'B102', '', ''),
('C005', 'Eka Putri',      'eka@gmail.com',   '081234567894', '3374010101990005', 'B201', '', ''),
('C006', 'Fajar Nugroho',  'fajar@gmail.com', '081234567895', '3374010101990006', 'C201', '', '');

-- Facilities
INSERT INTO facilities (id, name, floor, `desc`, status) VALUES
('F001', 'Parkir Motor',   'B1', 'Area parkir 30 motor',             'ok'),
('F002', 'Dapur Bersama',  '1',  'Dapur lengkap dengan kompor gas',  'repair'),
('F003', 'Ruang Laundry',  '1',  '3 mesin cuci + 2 pengering',       'ok'),
('F004', 'Lobi & CCTV',    '1',  'CCTV 24 jam + resepsionis',        'ok'),
('F005', 'Rooftop Garden', '3',  'Taman rooftop dengan kursi santai', 'repairing');

-- Orders
INSERT INTO orders (id, customer, room, type, `start`, `end`, total, status) VALUES
('ORD-001', 'Andi Pratama',   'A101', 'Bulanan', '2025-07-01', '2025-07-31', 800000,   'paid'),
('ORD-002', 'Budi Santoso',   'A201', 'Bulanan', '2025-07-01', '2025-07-31', 800000,   'pending'),
('ORD-003', 'Eka Putri',      'B201', 'Bulanan', '2025-07-01', '2025-07-31', 2000000,  'pending'),
('ORD-004', 'Fajar Nugroho',  'C201', 'Tahunan', '2025-01-15', '2026-01-15', 15000000, 'paid'),
('ORD-005', 'Dika Rahman',    'B102', 'Harian',  '2025-07-15', '2025-07-20', 750000,   'pending');

-- Repairs
INSERT INTO repairs (id, target, type, issue, reported, status, tech) VALUES
('REP-001', 'Kamar A202',     'kamar', 'AC rusak',        '2025-07-10', 'repairing', 'Pak Slamet'),
('REP-002', 'Dapur Bersama',  'fasum', 'Kompor gas bocor','2025-07-12', 'pending',   '-'),
('REP-003', 'Rooftop Garden', 'fasum', 'Lampu mati',      '2025-07-08', 'repairing', 'Pak Joko'),
('REP-004', 'Kamar B102',     'kamar', 'WC tersumbat',    '2025-07-05', 'done',      'Pak Slamet');

-- Logs
INSERT INTO logs (time, action, detail, type) VALUES
('2025-07-15 14:32:00', 'Order dibuat',         'ORD-005 oleh Dika Rahman – Kamar B102',        'order'),
('2025-07-15 11:10:00', 'Status kamar diubah',  'A103 → Need Cleaning',                         'room'),
('2025-07-14 09:45:00', 'Customer ditambah',    'Fajar Nugroho terdaftar',                      'customer'),
('2025-07-13 16:20:00', 'Invoice dikirim',      'ORD-003 ke Eka Putri via WhatsApp',            'invoice'),
('2025-07-13 10:00:00', 'Laporan perbaikan',    'REP-001: AC Kamar A202 dalam perbaikan',       'repair'),
('2025-07-12 08:30:00', 'Order lunas',          'ORD-004 oleh Fajar Nugroho',                   'order');
