-- GadgetHub database (import this in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS gadgethub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gadgethub;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mobile VARCHAR(15) NOT NULL,
  password VARCHAR(255) NOT NULL,
  address TEXT,
  city VARCHAR(60), state VARCHAR(60), pincode VARCHAR(10),
  status TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  image VARCHAR(255) DEFAULT '',
  description VARCHAR(255) DEFAULT '',
  status TINYINT NOT NULL DEFAULT 1
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  image VARCHAR(255) DEFAULT '',
  price DECIMAL(10,2) NOT NULL,
  discount TINYINT NOT NULL DEFAULT 0,          -- percentage
  description TEXT,
  specs TEXT,                                    -- one "Label: value" per line
  stock INT NOT NULL DEFAULT 0,
  rating DECIMAL(2,1) NOT NULL DEFAULT 4.0,
  featured TINYINT NOT NULL DEFAULT 0,
  status TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(100), email VARCHAR(150), mobile VARCHAR(15),
  billing_address TEXT, shipping_address TEXT,
  city VARCHAR(60), state VARCHAR(60), pincode VARCHAR(10),
  total DECIMAL(10,2) NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  payment_status ENUM('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  status ENUM('Pending','Confirmed','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  name VARCHAR(150) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  qty INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100), email VARCHAR(150), message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data (the default admin account is auto-created on first visit to /admin/login.php)
INSERT INTO categories (name, description) VALUES
('Headphones','Wireless buds, neckbands and over-ear headsets'),
('Chargers & Cables','Fast chargers, braided cables and adapters'),
('Power Banks','Pocket-size batteries for long days'),
('Smart Watches','Fitness and notification trackers for your wrist'),
('Phone Cases','Slim, rugged and clear protection');

INSERT INTO products (category_id,name,price,discount,description,specs,stock,rating,featured) VALUES
(1,'Bassline Wireless Earbuds',2499,20,'True wireless earbuds with deep bass and a 30-hour case.','Battery: 30 hours with case\nBluetooth: 5.3\nWater resistance: IPX4',40,4.5,1),
(1,'Studio Pro Over-Ear Headset',4999,15,'Comfortable over-ear headset with active noise cancellation.','Driver: 40 mm\nBattery: 45 hours\nNoise cancellation: Active',25,4.6,1),
(1,'Pulse Neckband',1299,10,'Lightweight neckband with magnetic earbuds and fast charge.','Battery: 25 hours\nBluetooth: 5.2',60,4.1,0),
(2,'65W GaN Fast Charger',1899,25,'Compact dual-port charger for laptop and phone.','Output: 65W\nPorts: USB-C + USB-A',80,4.4,1),
(2,'Braided USB-C Cable 1.5m',499,0,'Tough nylon braided cable, 100W power delivery.','Length: 1.5 m\nPower: up to 100W',150,4.2,0),
(2,'Magnetic Wireless Charger',1599,30,'15W magnetic wireless charging pad.','Output: 15W\nCompatibility: Qi devices',35,4.0,0),
(3,'PocketCell 10000 mAh',1399,10,'Slim 10000 mAh power bank with two outputs.','Capacity: 10000 mAh\nOutput: 22.5W',70,4.3,1),
(3,'MegaCell 20000 mAh',2299,18,'High capacity power bank with digital display.','Capacity: 20000 mAh\nOutput: 45W',45,4.5,0),
(4,'FitBand Active',2999,33,'AMOLED smart band with heart-rate and sleep tracking.','Display: 1.47 inch AMOLED\nBattery: 10 days\nWater resistance: 5 ATM',55,4.2,1),
(4,'Orbit Smart Watch',6999,22,'Round-dial smart watch with GPS and calling.','Display: 1.43 inch AMOLED\nGPS: Built-in\nBattery: 7 days',20,4.7,0),
(5,'ClearGuard Phone Case',599,0,'Crystal-clear case that resists yellowing.','Material: TPU + polycarbonate\nDrop protection: 2 m',200,4.0,0),
(5,'Armor Rugged Case',899,12,'Shock-absorbing rugged case with raised edges.','Material: TPU\nDrop protection: 3 m',120,4.3,0);
