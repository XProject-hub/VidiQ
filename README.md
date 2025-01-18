### Features
- Login page at `/index.php`
- Admin dashboard at `/admin/dashboard.php` with navigation for:
  - Users
  - Media
  - Settings
  - Logs
- Secure session-based authentication.
- Logout functionality via `/logout.php`.
- Manage Users
  - View all users.
  - Add new users.
  - Edit or delete existing users (coming soon).
  - Manage Users
  - View all users.
  - Add new users.
  - Edit user details.
  - Delete users.
  - Streams Management:
  - View all streams.
  - Add new streams.
  - Edit stream details.
  - Delete streams.
- Reseller Management:
  - View all resellers.
  - Add new resellers.
  - Edit reseller details.
  - Delete resellers.
- Streams Management:
  - View all streams.
  - Add new streams.
  - Edit stream details.
  - Delete streams.
  - Toggle stream status (Active/Inactive).
  - Streams Management:
  - View all streams.
  - Add new streams.
  - Edit existing streams.
  - Delete streams.
  - Toggle stream status (Active/Inactive).

  # Project Name: [Your Project Name]

## Description
Brief description of the project, e.g.:
- An interactive IPTV dashboard with server stats, user management, and advanced analytics.
- Real-time updates for CPU, RAM, bandwidth, and server connections.

## Features
- **Dashboard**: Real-time stats with circular progress bars.
- **User Management**: Add, edit, and manage user accounts.
- **Server Stats**: Fetch and display server resource usage.
- **Responsive Design**: Fully responsive UI/UX.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP, SQLite
- **APIs**: Custom-built API for server usage stats.

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/YourUsername/YourRepository.git

## User Management

This feature provides the ability to manage users (add, edit, delete) from the admin panel.

### Endpoints
- `GET /api/user_api.php`: Fetch all users.
- `POST /api/user_api.php`: Add a new user.
- `PUT /api/user_api.php`: Update an existing user.
- `DELETE /api/user_api.php`: Delete a user.

### Steps
1. Navigate to `/admin/users/user_management.php` for user management.
2. Add, edit, or delete users using the interface.

### File Structure
- `/admin/users/user_management.php`: User management interface.
- `/api/user_api.php`: API for user CRUD operations.
- `/public/js/user_management.js`: JavaScript logic for user management.
- `/public/css/style.css`: Contains modal and table styles.
## User Management API

### Endpoints
- **GET** `/api/user_api.php`: Fetch all users.
- **POST** `/api/user_api.php`: Add a new user.
  - **Body** (JSON):
    ```json
    {
      "username": "example",
      "email": "example@example.com",
      "password": "password"
    }
    ```
- **PUT** `/api/user_api.php`: Update user details.
  - **Body** (JSON):
    ```json
    {
      "id": 1,
      "email": "new@example.com",
      "password": "newpassword"
    }
    ```
- **DELETE** `/api/user_api.php`: Delete a user.
  - **Body** (JSON):
    ```json
    {
      "id": 1
    }
    ```
## User Role Management

### API Endpoints
- **GET** `/api/user_api.php`: Fetch all users with roles.
- **POST** `/api/user_api.php`: Add a new user (username, email, password, role).
- **PUT** `/api/user_api.php`: Update user details (id, email, password, role).
- **DELETE** `/api/user_api.php`: Delete a user by ID.

### Frontend
- **User Management**: Accessible via `/admin/user_management.php`.
- **Role Support**: Assign roles (`Admin`, `Editor`, `Viewer`) to users.

### Database
Ensure `role` column exists in the `users` table:
```sql
ALTER TABLE users ADD COLUMN role TEXT NOT NULL DEFAULT 'Viewer';