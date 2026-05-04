-- KosManager Database Schema
CREATE TABLE IF NOT EXISTS rooms (
  id VARCHAR(10) PRIMARY KEY,
  floor INT,
  type VARCHAR(20),
  rent VARCHAR(20),
  price INT,
  status VARCHAR(20),
  tenant VARCHAR(100),
  until DATE
);

CREATE TABLE IF NOT EXISTS customers (
  id VARCHAR(10) PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  wa VARCHAR(20),
  ktp VARCHAR(30),
  room VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS facilities (
  id VARCHAR(10) PRIMARY KEY,
  name VARCHAR(100),
  floor VARCHAR(10),
  `desc` VARCHAR(255),
  status VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS orders (
  id VARCHAR(20) PRIMARY KEY,
  customer VARCHAR(100),
  room VARCHAR(10),
  type VARCHAR(20),
  start DATE,
  end DATE,
  total INT,
  status VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS repairs (
  id VARCHAR(20) PRIMARY KEY,
  target VARCHAR(100),
  type VARCHAR(20),
  issue VARCHAR(100),
  reported DATE,
  status VARCHAR(20),
  tech VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  time DATETIME,
  action VARCHAR(100),
  detail VARCHAR(255),
  type VARCHAR(20)
);
