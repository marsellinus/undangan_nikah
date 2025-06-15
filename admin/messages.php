<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Since session_start() is now handled in config.php, 
// we don't need to call it again here

// Simple authentication (you might want to implement a more secure method)
$username = "admin";
$password = "wedding2023";

// Check if user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $is_logged_in = true;
    } else {
        $login_error = "Username atau password salah!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: index.php");
    exit;
}

// Handle message actions if logged in
if ($is_logged_in) {
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $messageId = (int)$_GET['id'];
        
        if ($_GET['action'] === 'approve') {
            if (approveMessage($messageId)) {
                $_SESSION['admin_message'] = "Pesan berhasil disetujui!";
            } else {
                $_SESSION['admin_message'] = "Gagal menyetujui pesan.";
            }
        } elseif ($_GET['action'] === 'reject') {
            if (rejectMessage($messageId)) {
                $_SESSION['admin_message'] = "Pesan berhasil ditolak!";
            } else {
                $_SESSION['admin_message'] = "Gagal menolak pesan.";
            }
        } elseif ($_GET['action'] === 'delete') {
            if (deleteMessage($messageId)) {
                $_SESSION['admin_message'] = "Pesan berhasil dihapus!";
            } else {
                $_SESSION['admin_message'] = "Gagal menghapus pesan.";
            }
        }
        
        // Redirect to remove action parameters
        header("Location: messages.php");
        exit;
    }
}

// Get message data if logged in
$messages = $is_logged_in ? getAllMessages() : [];

// Group messages by approval status
$pendingMessages = [];
$approvedMessages = [];
$rejectedMessages = [];

if ($is_logged_in) {
    foreach ($messages as $message) {
        if ($message['is_approved'] === null) {
            $pendingMessages[] = $message;
        } elseif ($message['is_approved'] == 1) {
            $approvedMessages[] = $message;
        } else {
            $rejectedMessages[] = $message;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Moderasi Ucapan & Doa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-rose-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-xl md:text-2xl font-bold">Admin Dashboard</h1>
                <?php if ($is_logged_in): ?>
                <div class="flex space-x-4 items-center">
                    <a href="index.php" class="hover:underline">Dashboard</a>
                    <a href="messages.php" class="bg-white text-rose-600 px-4 py-2 rounded hover:bg-gray-100">Ucapan & Doa</a>
                    <a href="?logout=1" class="hover:underline">Logout</a>
                </div>
                <?php endif; ?>
            </div>
        </nav>
        
        <div class="container mx-auto p-4">
            <?php if (!$is_logged_in): ?>
            <!-- Login Form -->
            <div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-6 text-center text-rose-600">Login Admin</h2>
                
                <?php if (isset($login_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $login_error; ?>
                </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                    
                    <button type="submit" name="login" class="w-full bg-rose-500 text-white py-2 rounded-lg hover:bg-rose-600">
                        Login
                    </button>
                </form>
            </div>
            <?php else: ?>
            <!-- Admin Messages Dashboard -->
            <div class="my-8">
                <h2 class="text-2xl font-bold mb-6">Moderasi Ucapan & Doa</h2>
                
                <?php if (isset($_SESSION['admin_message'])): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                    <?php 
                    echo $_SESSION['admin_message'];
                    unset($_SESSION['admin_message']);
                    ?>
                </div>
                <?php endif; ?>
                
                <!-- Pending Messages -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Menunggu Moderasi 
                        <span class="bg-yellow-100 text-yellow-600 text-sm py-1 px-2 rounded-full ml-2">
                            <?php echo count($pendingMessages); ?>
                        </span>
                    </h3>
                    
                    <?php if (empty($pendingMessages)): ?>
                    <p class="bg-white p-4 rounded-lg shadow text-gray-500">Tidak ada ucapan yang menunggu moderasi.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($pendingMessages as $message): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <p class="italic text-gray-600 mb-4">"<?php echo htmlspecialchars($message['message']); ?>"</p>
                            <div class="flex justify-between items-center mb-4">
                                <p>
                                    <span class="font-semibold"><?php echo htmlspecialchars($message['name']); ?></span>
                                    <span class="text-sm text-gray-500 ml-2"><?php echo formatIndonesianDate($message['created_at']); ?></span>
                                </p>
                                <span class="bg-yellow-100 text-yellow-600 text-xs px-2 py-1 rounded-full">Menunggu</span>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <a href="?action=approve&id=<?php echo $message['id']; ?>" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </a>
                                <a href="?action=reject&id=<?php echo $message['id']; ?>" 
                                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Approved Messages -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Pesan yang Disetujui
                        <span class="bg-green-100 text-green-600 text-sm py-1 px-2 rounded-full ml-2">
                            <?php echo count($approvedMessages); ?>
                        </span>
                    </h3>
                    
                    <?php if (empty($approvedMessages)): ?>
                    <p class="bg-white p-4 rounded-lg shadow text-gray-500">Belum ada ucapan yang disetujui.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($approvedMessages as $message): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <p class="italic text-gray-600 mb-4">"<?php echo htmlspecialchars($message['message']); ?>"</p>
                            <div class="flex justify-between items-center mb-4">
                                <p>
                                    <span class="font-semibold"><?php echo htmlspecialchars($message['name']); ?></span>
                                    <span class="text-sm text-gray-500 ml-2"><?php echo formatIndonesianDate($message['created_at']); ?></span>
                                </p>
                                <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">Disetujui</span>
                            </div>
                            <div class="flex justify-end">
                                <a href="?action=delete&id=<?php echo $message['id']; ?>" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm"
                                   onclick="return confirm('Yakin ingin menghapus pesan ini?');">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Rejected Messages -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Pesan yang Ditolak
                        <span class="bg-red-100 text-red-600 text-sm py-1 px-2 rounded-full ml-2">
                            <?php echo count($rejectedMessages); ?>
                        </span>
                    </h3>
                    
                    <?php if (empty($rejectedMessages)): ?>
                    <p class="bg-white p-4 rounded-lg shadow text-gray-500">Tidak ada ucapan yang ditolak.</p>
                    <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($rejectedMessages as $message): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <p class="italic text-gray-600 mb-4">"<?php echo htmlspecialchars($message['message']); ?>"</p>
                            <div class="flex justify-between items-center mb-4">
                                <p>
                                    <span class="font-semibold"><?php echo htmlspecialchars($message['name']); ?></span>
                                    <span class="text-sm text-gray-500 ml-2"><?php echo formatIndonesianDate($message['created_at']); ?></span>
                                </p>
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">Ditolak</span>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <a href="?action=approve&id=<?php echo $message['id']; ?>" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </a>
                                <a href="?action=delete&id=<?php echo $message['id']; ?>" 
                                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm"
                                   onclick="return confirm('Yakin ingin menghapus pesan ini?');">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-200 py-4 mt-8">
        <div class="container mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> Admin Undangan Pernikahan</p>
        </div>
    </footer>
</body>
</html>
