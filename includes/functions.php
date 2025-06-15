<?php
// Clean input data to prevent SQL injection and XSS
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get all RSVPs for admin display
function getAllRSVPs() {
    $conn = connectDB();
    $sql = "SELECT * FROM rsvp ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $rsvps = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rsvps[] = $row;
        }
    }
    
    $conn->close();
    return $rsvps;
}

// Get all messages for admin moderation
function getAllMessages() {
    $conn = connectDB();
    $sql = "SELECT * FROM messages ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $messages = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    
    $conn->close();
    return $messages;
}

// Get only approved messages
function getApprovedMessages($limit = 10) {
    $conn = connectDB();
    
    // Check if the is_approved column exists in the messages table
    $columnExists = false;
    $columnCheckQuery = "SHOW COLUMNS FROM messages LIKE 'is_approved'";
    $columnCheckResult = $conn->query($columnCheckQuery);
    
    if ($columnCheckResult && $columnCheckResult->num_rows > 0) {
        $columnExists = true;
    }
    
    // If is_approved column exists, filter by it; otherwise, show all messages
    if ($columnExists) {
        $sql = "SELECT * FROM messages WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?";
    } else {
        // Fallback query without the is_approved filter
        $sql = "SELECT * FROM messages ORDER BY created_at DESC LIMIT ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $messages;
}

// Approve a message
function approveMessage($messageId) {
    $conn = connectDB();
    $sql = "UPDATE messages SET is_approved = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Reject a message
function rejectMessage($messageId) {
    $conn = connectDB();
    $sql = "UPDATE messages SET is_approved = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Delete a message
function deleteMessage($messageId) {
    $conn = connectDB();
    $sql = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Count total attendees
function countAttendees() {
    $conn = connectDB();
    $sql = "SELECT SUM(guest_count) AS total FROM rsvp WHERE attending = 'yes'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['total'] ?? 0;
}

// Get count of people attending
function getAttendingCount() {
    $conn = connectDB();
    $sql = "SELECT COUNT(*) AS count FROM rsvp WHERE attending = 'yes'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['count'] ?? 0;
}

// Get count of people not attending
function getNotAttendingCount() {
    $conn = connectDB();
    $sql = "SELECT COUNT(*) AS count FROM rsvp WHERE attending = 'no'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['count'] ?? 0;
}

// Count messages awaiting moderation
function getPendingMessagesCount() {
    $conn = connectDB();
    $sql = "SELECT COUNT(*) AS count FROM messages WHERE is_approved IS NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();
    
    return $row['count'] ?? 0;
}

// Format date to Indonesian format
function formatIndonesianDate($date) {
    $months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return "$day $month $year, $time WIB";
}

// Generate unique guest link
function generateGuestLink($name) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    
    // Ensure the baseUrl ends with a trailing slash
    if (substr($baseUrl, -1) !== '/') {
        $baseUrl .= '/';
    }
    
    return $baseUrl . 'index.php?to=' . urlencode($name);
}
?>
