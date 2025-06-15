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

// Get RSVP data if logged in
$rsvps = $is_logged_in ? getAllRSVPs() : [];

// Calculate statistics
$attending_count = $is_logged_in ? getAttendingCount() : 0;
$not_attending_count = $is_logged_in ? getNotAttendingCount() : 0;
$total_guests = $is_logged_in ? countAttendees() : 0;
$pending_messages = $is_logged_in ? getPendingMessagesCount() : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Undangan Pernikahan</title>
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
                    <a href="index.php" class="bg-white text-rose-600 px-4 py-2 rounded hover:bg-gray-100">Dashboard</a>
                    <a href="messages.php" class="hover:underline relative">
                        Ucapan & Doa
                        <?php if ($pending_messages > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo $pending_messages; ?>
                        </span>
                        <?php endif; ?>
                    </a>
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
            <!-- Admin Dashboard -->
            <div class="my-8">
                <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Konfirmasi Hadir</p>
                                <p class="text-xl font-semibold"><?php echo $attending_count; ?> orang</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                                <i class="fas fa-user-times text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Konfirmasi Tidak Hadir</p>
                                <p class="text-xl font-semibold"><?php echo $not_attending_count; ?> orang</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Tamu yang Hadir</p>
                                <p class="text-xl font-semibold"><?php echo $total_guests; ?> orang</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <a href="messages.php" class="flex items-center hover:opacity-80">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                                <i class="fas fa-comment-dots text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Ucapan Menunggu Moderasi</p>
                                <p class="text-xl font-semibold"><?php echo $pending_messages; ?> pesan</p>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Guest Link Generator -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h3 class="text-xl font-semibold mb-4">Generate Link Undangan</h3>
                    <form id="guest-link-form" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <input type="text" id="guest-name" placeholder="Nama Tamu" 
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>
                        <button type="submit" class="bg-rose-500 text-white py-2 px-6 rounded-lg hover:bg-rose-600">
                            Generate Link
                        </button>
                    </form>
                    <div id="guest-link-result" class="mt-4 hidden">
                        <p class="text-sm text-gray-600 mb-1">Link Undangan:</p>
                        <div class="flex">
                            <input type="text" id="generated-link" readonly 
                                  class="flex-grow px-4 py-2 border rounded-l-lg focus:outline-none bg-gray-50">
                            <button id="copy-link" class="bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-r-lg">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Tamu akan disambut dengan nama mereka saat membuka undangan
                        </p>
                    </div>
                </div>
                
                <!-- RSVP Table -->
                <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
                    <h3 class="text-xl font-semibold mb-4">Daftar RSVP</h3>
                    
                    <?php if (empty($rsvps)): ?>
                    <p class="text-gray-500">Belum ada konfirmasi kehadiran.</p>
                    <?php else: ?>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 text-left border-b">No</th>
                                <th class="py-3 px-4 text-left border-b">Nama</th>
                                <th class="py-3 px-4 text-left border-b">Kehadiran</th>
                                <th class="py-3 px-4 text-left border-b">Jumlah Tamu</th>
                                <th class="py-3 px-4 text-left border-b">Pesan</th>
                                <th class="py-3 px-4 text-left border-b">Tanggal Submit</th>
                                <th class="py-3 px-4 text-left border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rsvps as $index => $rsvp): ?>
                            <tr>
                                <td class="py-3 px-4 border-b"><?php echo $index + 1; ?></td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($rsvp['name']); ?></td>
                                <td class="py-3 px-4 border-b">
                                    <?php if ($rsvp['attending'] === 'yes'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm">Hadir</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-sm">Tidak Hadir</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 border-b"><?php echo $rsvp['attending'] === 'yes' ? $rsvp['guest_count'] : '-'; ?></td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($rsvp['message'] ?: '-'); ?></td>
                                <td class="py-3 px-4 border-b"><?php echo formatIndonesianDate($rsvp['created_at']); ?></td>
                                <td class="py-3 px-4 border-b">
                                    <button class="text-blue-500 hover:text-blue-700 generate-link" data-name="<?php echo htmlspecialchars($rsvp['name']); ?>">
                                        <i class="fas fa-link"></i> Generate Link
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
    
    <?php if ($is_logged_in): ?>
    <script>
        // Guest link generator
        document.addEventListener('DOMContentLoaded', function() {
            const guestLinkForm = document.getElementById('guest-link-form');
            const guestNameInput = document.getElementById('guest-name');
            const guestLinkResult = document.getElementById('guest-link-result');
            const generatedLink = document.getElementById('generated-link');
            const copyLinkBtn = document.getElementById('copy-link');
            
            // Function to generate link
            function generateLink(name) {
                if (!name.trim()) return '';
                
                const baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname.replace('admin/index.php', '');
                return baseUrl + 'index.php?to=' + encodeURIComponent(name.trim());
            }
            
            // Handle form submission
            guestLinkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const guestName = guestNameInput.value.trim();
                
                if (guestName) {
                    const link = generateLink(guestName);
                    generatedLink.value = link;
                    guestLinkResult.classList.remove('hidden');
                }
            });
            
            // Handle copy button
            copyLinkBtn.addEventListener('click', function() {
                generatedLink.select();
                document.execCommand('copy');
                
                // Show "Copied!" feedback
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                }, 1500);
            });
            
            // Generate link buttons in the table
            document.querySelectorAll('.generate-link').forEach(button => {
                button.addEventListener('click', function() {
                    const name = this.getAttribute('data-name');
                    if (name) {
                        guestNameInput.value = name;
                        const link = generateLink(name);
                        generatedLink.value = link;
                        guestLinkResult.classList.remove('hidden');
                        
                        // Scroll to link generator
                        document.querySelector('.bg-white.p-6.rounded-lg.shadow-md.mb-8').scrollIntoView({ 
                            behavior: 'smooth' 
                        });
                    }
                });
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
