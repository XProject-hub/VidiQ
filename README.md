Description
An interactive IPTV dashboard with server stats, user management, and advanced analytics. Real-time updates for CPU, RAM, bandwidth, and server connections.

Features
Dashboard: Real-time stats with circular progress bars.
User Management:
View all users.
Add new users.
Edit or delete existing users (coming soon).
Role-based access: Admin, Editor, Viewer.
Server Stats: Fetch and display server resource usage.
Responsive Design: Fully responsive UI/UX.
Technologies Used
Frontend: HTML, CSS, JavaScript
Backend: PHP, SQLite
APIs: Custom-built API for server usage stats.
Installation
Clone the repository:

git clone https://github.com/YourUsername/YourRepository.git
Navigate to the project directory:

cd YourProjectName
Install dependencies (if any):

composer install  # if using Composer for PHP packages
npm install      # if using Node.js packages for frontend
Set up database (SQLite, MySQL, etc.):

Create a SQLite database file or configure the MySQL connection.
Update config.php with your database credentials.
Run the application:

php -S localhost:8000 public/
User Management
This feature provides the ability to manage users (add, edit, delete) from the admin panel.

Endpoints
GET /api/user_api.php: Fetch all users.
POST /api/user_api.php: Add a new user.
Body:
{
  "username": "example",
  "email": "example@example.com",
  "password": "password",
  "role": "Viewer"
}
PUT /api/user_api.php: Update an existing user's details and/or role.
Body:
{
  "id": 1,
  "email": "new@example.com",
  "password": "newpassword",
  "role": "Editor"
}
DELETE /api/user_api.php: Delete a user by ID.
Body:
{
  "id": 1
}
Steps
Navigate to admin/dashboard.php for the admin dashboard.
Log in using credentials provided or create a new admin account from /index.php.
Access /admin/user_management.php for managing users.
Use the API endpoints listed above to add, edit, and delete users.
File Structure
Admin Dashboard:
/admin/dashboard.php: Main dashboard with navigation links.
/admin/user_management.php: Interface for user management.
API Endpoints:
/api/user_api.php: Handles all CRUD operations.
JavaScript Logic:
/public/js/user_management.js: Contains JavaScript logic for interactions.
CSS Styles:
/public/css/style.css: Styles for the dashboard and modal.
Database Schema
Ensure your users table includes columns such as id, username, email, password, and role.

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'Viewer'
);
This structure provides a clear, organized overview of your project, making it easy for developers to understand and navigate the features and implementation details.