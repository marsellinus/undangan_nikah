<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undangan_nikah');

// Connect to database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    return $conn;
}

// Create tables if they don't exist
function createTablesIfNotExist() {
    $conn = connectDB();
    
    // Create RSVP table
    $sql = "CREATE TABLE IF NOT EXISTS rsvp (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        attending ENUM('yes', 'no') NOT NULL,
        guest_count INT(2) DEFAULT 1,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        echo "Error creating table: " . $conn->error;
    }
    
    // Check if messages table exists
    $result = $conn->query("SHOW TABLES LIKE 'messages'");
    
    if ($result->num_rows === 0) {
        // Create messages table with approval system if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS messages (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_approved TINYINT(1) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!$conn->query($sql)) {
            echo "Error creating table: " . $conn->error;
        }
    } else {
        // Check if is_approved column exists in messages table
        $columnExists = $conn->query("SHOW COLUMNS FROM messages LIKE 'is_approved'")->num_rows > 0;
        
        // Add is_approved column if it doesn't exist
        if (!$columnExists) {
            $sql = "ALTER TABLE messages ADD COLUMN is_approved TINYINT(1) DEFAULT NULL";
            
            if (!$conn->query($sql)) {
                echo "Error adding column: " . $conn->error;
            }
        }
    }
    
    $conn->close();
}

// Create the database if it doesn't exist
function createDatabaseIfNotExists() {
    // Connect to MySQL without selecting a database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create the database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci";
    
    if (!$conn->query($sql)) {
        echo "Error creating database: " . $conn->error;
    }
    
    $conn->close();
    
    // Now create the tables
    createTablesIfNotExist();
}

// Initialize the database
createDatabaseIfNotExists();
?>
