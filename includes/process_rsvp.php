<?php
require_once 'config.php';
require_once 'functions.php';

session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = isset($_POST['name']) ? cleanInput($_POST['name']) : '';
    $attending = isset($_POST['attending']) ? cleanInput($_POST['attending']) : '';
    $guest_count = ($attending === 'yes' && isset($_POST['guest_count'])) ? (int)$_POST['guest_count'] : 0;
    $message = isset($_POST['message']) ? cleanInput($_POST['message']) : '';
    $display_message = isset($_POST['display_message']) && $_POST['display_message'] === 'yes';
    
    // Validate required fields
    if (empty($name) || empty($attending)) {
        $_SESSION['rsvp_message'] = "Error: Nama dan konfirmasi kehadiran harus diisi.";
        header('Location: ../index.php#rsvp');
        exit;
    }
    
    // Connect to database
    $conn = connectDB();
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Prepare and execute SQL statement for RSVP
        $stmt = $conn->prepare("INSERT INTO rsvp (name, attending, guest_count, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $attending, $guest_count, $message);
        $stmt->execute();
        $stmt->close();
        
        // If there's a message and user allowed it to be displayed, add to messages table
        if (!empty($message) && $display_message) {
            // Check if is_approved column exists in messages table
            $columnExists = $conn->query("SHOW COLUMNS FROM messages LIKE 'is_approved'")->num_rows > 0;
            
            if ($columnExists) {
                $stmt = $conn->prepare("INSERT INTO messages (name, message, is_approved) VALUES (?, ?, NULL)");
                $stmt->bind_param("ss", $name, $message);
            } else {
                $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $message);
            }
            
            $stmt->execute();
            $stmt->close();
        }
        
        // Commit the transaction
        $conn->commit();
        
        $_SESSION['rsvp_message'] = "Terima kasih, {$name}! Konfirmasi kehadiran Anda telah kami terima." . 
                                    ($display_message && !empty($message) ? " Ucapan Anda akan ditampilkan setelah dimoderasi oleh admin." : "");
        
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        $_SESSION['rsvp_message'] = "Error: Gagal menyimpan data. Silakan coba lagi. " . $e->getMessage();
    }
    
    // Close connection
    $conn->close();
    
    // Redirect back to the form
    header('Location: ../index.php#rsvp');
    exit;
} else {
    // If not POST request, redirect to homepage
    header('Location: ../index.php');
    exit;
}

// Function to send email notification (uncomment and configure if needed)
/*
function sendEmailNotification($name, $attending, $guest_count, $message) {
    $to = "pengantin@example.com"; // Change this to the couple's email
    $subject = "Konfirmasi Kehadiran Baru - Undangan Pernikahan";
    
    $body = "Konfirmasi kehadiran baru telah diterima:\n\n";
    $body .= "Nama: " . $name . "\n";
    $body .= "Kehadiran: " . ($attending === 'yes' ? 'Hadir' : 'Tidak Hadir') . "\n";
    
    if ($attending === 'yes') {
        $body .= "Jumlah Tamu: " . $guest_count . "\n";
    }
    
    if (!empty($message)) {
        $body .= "Pesan: " . $message . "\n";
    }
    
    $headers = "From: website@undanganpernikahan.com";
    
    mail($to, $subject, $body, $headers);
}
*/
?>
