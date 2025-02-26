-- Updated schema.sql

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','reseller','subreseller') DEFAULT 'reseller',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS streams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(255) DEFAULT NULL,           -- URL or path to the stream icon
  name VARCHAR(100) NOT NULL,
  stream_url VARCHAR(255) NOT NULL,
  clients INT DEFAULT 0,                      -- number of connected clients
  uptime VARCHAR(50) DEFAULT NULL,            -- uptime text (e.g., "2h 15m")
  player VARCHAR(255) DEFAULT NULL,           -- player information or link
  epg VARCHAR(255) DEFAULT NULL,              -- EPG info or URL
  stream_info TEXT,                           -- additional stream details
  category VARCHAR(50) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);
