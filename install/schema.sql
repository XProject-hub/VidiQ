-- schema.sql

-- Users table for panel accounts
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','reseller','subreseller') DEFAULT 'reseller',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Streams table for channels/streams
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

-- Admin table for panel admin credentials
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
);

-- Servers table to store real-time server monitoring data
CREATE TABLE IF NOT EXISTS servers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,               -- Server name (e.g., "Main Server")
  ip VARCHAR(100) NOT NULL,                 -- Real IP address of the server
  is_main TINYINT DEFAULT 0,                -- 1 if this is the main server
  cpu_usage INT DEFAULT 0,                  -- CPU usage percentage
  ram_usage INT DEFAULT 0,                  -- RAM usage percentage
  bandwidth_usage INT DEFAULT 0,            -- Bandwidth/network usage percentage
  users INT DEFAULT 0,                      -- Total user count (or online users)
  live_connections INT DEFAULT 0,           -- Active/live connections
  down_channels INT DEFAULT 0,              -- Number of channels currently down
  streams_live INT DEFAULT 0,               -- Number of streams that are live
  streams_off INT DEFAULT 0,                -- Number of streams that are down
  input_bw INT DEFAULT 0,                   -- Input bandwidth (e.g., in Mbps)
  output_bw INT DEFAULT 0,                  -- Output bandwidth (e.g., in Mbps)
  uptime VARCHAR(50) DEFAULT NULL,          -- Server uptime (e.g., "4d 8h")
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
