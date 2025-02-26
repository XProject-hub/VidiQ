-- schema.sql

-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','reseller','subreseller') DEFAULT 'reseller',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. STREAMS TABLE
CREATE TABLE IF NOT EXISTS streams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(255) DEFAULT NULL,         -- URL or path to stream icon
  name VARCHAR(100) NOT NULL,
  stream_url VARCHAR(255) NOT NULL,         -- URL of the stream
  clients INT DEFAULT 0,                    -- Number of connected clients
  uptime VARCHAR(50) DEFAULT NULL,          -- Uptime information (e.g., "4d 8h")
  player VARCHAR(255) DEFAULT NULL,         -- Player info or link
  epg VARCHAR(255) DEFAULT NULL,            -- EPG info or URL
  stream_info TEXT,                         -- Additional stream details
  category VARCHAR(50) DEFAULT NULL,        -- Category (Sports, Movies, etc.)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. ADMIN TABLE
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

-- 4. SERVERS TABLE (for real‑time server monitoring)
CREATE TABLE IF NOT EXISTS servers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  ip VARCHAR(100) NOT NULL,
  is_main TINYINT DEFAULT 0,
  cpu_usage INT DEFAULT 0,        -- CPU usage in percent
  ram_usage INT DEFAULT 0,        -- RAM usage in percent
  hdd_usage INT DEFAULT 0,        -- HDD usage in percent
  bandwidth_usage INT DEFAULT 0,  -- optional leftover column
  users INT DEFAULT 0,
  live_connections INT DEFAULT 0,
  down_channels INT DEFAULT 0,
  streams_live INT DEFAULT 0,
  streams_off INT DEFAULT 0,
  input_bw INT DEFAULT 0,         -- input bandwidth usage in percent
  output_bw INT DEFAULT 0,        -- output bandwidth usage in percent
  uptime VARCHAR(50) DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
