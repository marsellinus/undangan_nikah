# Wedding Invitation Website

A beautiful and responsive digital wedding invitation website built using PHP, Tailwind CSS, and JavaScript.

## Features

- Beautiful envelope design with guest personalization
- Responsive design for all devices
- Digital RSVP functionality
- Guest message and well-wishes system
- Countdown timer to the wedding date
- Photo gallery with lightbox
- Interactive schedule and location maps
- Gift registry information with copy-to-clipboard functionality
- Background music toggle
- Smooth scrolling and animations

## Requirements

- PHP 7.4 or higher
- MySQL database
- Web server (Apache/Nginx)

## Installation

1. Clone or download the project to your web server directory
   ```
   git clone https://github.com/yourusername/wedding-invitation.git
   ```
   Or simply extract the ZIP file to your web server directory

2. Create a MySQL database for the application
   ```
   CREATE DATABASE wedding_invitation;
   ```

3. Import the SQL schema
   ```
   mysql -u username -p wedding_invitation < database/schema.sql
   ```

4. Configure database connection in `includes/config.php`
   ```php
   // Database configuration
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'wedding_invitation');
   ```

5. Ensure proper permissions are set on the project folders
   ```
   chmod -R 755 /path/to/project
   ```

## Usage

### Guest Invitation Links

You can create personalized invitation links for your guests by adding their name as a URL parameter:
