# VidiQ IPTV Streaming Panel

This project is a custom IPTV streaming panel built from scratch using PHP, Python3, and JavaScript. It is inspired by Xtreamcodes/xtream-ui and uses a dark theme with cyan highlights.

## Installation

1. **Environment Setup:**
   - VPS server running Ubuntu (or your preferred distro).
   - Nginx installed and configured.
   - PHP 7+ (or PHP 8) installed.
   - MySQL server installed.
   - Python3 installed.

2. **Clone the Repository:**
   ```bash
   cd /home/
   git clone https://github.com/yourusername/vidiq.git
   cd vidiq


   ## Channel Streaming

The panel now includes basic channel streaming functionality. You can:
- **Add Streams:** Use the "Add New Stream" page in the admin section to create a new channel by specifying its name, URL, and category.
- **Manage Streams:** View all added channels, and preview them via an integrated HTML5 video player.

To access these features, log in as an admin and navigate to the Streams section.

## User Management

### Login

- **Main Login:**  
  Users access the login page at `http://<server-ip>`.  
  The system first checks the **admin** table for admin credentials. If not found, it checks the **users** table (which holds resellers and subresellers).  
  Based on the role, users are redirected to:
  - **Admins:** `/admin/dashboard.php`
  - **Resellers:** `/reseller/dashboard.php`
  - **Subresellers:** `/subreseller/dashboard.php`

### Admin Capabilities

- From the **Admin Dashboard**, an admin can:
  - **Add Additional Admins:** Via `/admin/manage_admins.php`
  - **Add Resellers:** Via `/admin/manage_resellers.php`
  - And later manage other aspects of the panel.

### Reseller Capabilities

- **Resellers** can log in and add **Subresellers** via `/reseller/add_subreseller.php`.

