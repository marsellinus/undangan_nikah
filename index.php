<?php
// WAJIB di baris paling atas untuk menggunakan $_SESSION
session_start();

// Pengaturan Error Reporting (sesuaikan untuk development/production)
error_reporting(E_ALL); // Laporkan semua error
// Untuk produksi, matikan tampilan error ke pengguna dan log ke file
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); // Tentukan path log error Anda
// Selama pengembangan, Anda bisa membiarkan display_errors aktif:
ini_set('display_errors', 1);


require_once 'includes/config.php'; // Asumsikan file ini ada dan berisi konfigurasi
require_once 'includes/functions.php'; // Asumsikan file ini ada dan berisi fungsi-fungsi helper

// Fungsi placeholder jika tidak ada di functions.php (SEBAIKNYA DEFINISIKAN DENGAN BENAR)
if (!function_exists('cleanInput')) {
    function cleanInput($data) {
        // Ini adalah sanitasi dasar, untuk keamanan lebih baik, gunakan filter yang lebih spesifik
        // atau library validasi. Fokus utama adalah htmlspecialchars untuk output.
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatIndonesianDate')) {
    function formatIndonesianDate($dateString) {
        // Implementasi sederhana, idealnya gunakan IntlDateFormatter atau library tanggal
        try {
            $date = new DateTime($dateString);
            // Anda bisa menambahkan logika konversi bulan/hari ke Bahasa Indonesia di sini jika diperlukan
            return $date->format('d F Y, H:i');
        } catch (Exception $e) {
            return $dateString; // Kembalikan string asli jika format tidak valid
        }
    }
}


// Get guest name from URL parameter dengan sanitasi
$guestName = isset($_GET['to']) ? cleanInput($_GET['to']) : '';

// Check if the envelope has been opened
$envelopeOpened = isset($_SESSION['envelope_opened']) && $_SESSION['envelope_opened'] === true;

// Set the envelope as opened if the open parameter is present
if (isset($_GET['open']) && $_GET['open'] == '1') {
    $_SESSION['envelope_opened'] = true;
    $envelopeOpened = true;

    // Redirect to remove the 'open' parameter from the URL
    $redirectUrl = 'index.php';
    if (!empty($guestName)) {
        // Pastikan $guestName sudah di-urlencode sebelumnya jika mengandung karakter khusus
        // cleanInput biasanya sudah menangani htmlspecialchars, tapi untuk URL, urlencode lebih tepat
        $redirectUrl .= '?to=' . urlencode(isset($_GET['to']) ? $_GET['to'] : ''); // Ambil dari GET asli untuk urlencode
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// Wedding date for countdown (Ambil dari config.php jika memungkinkan)
$weddingDate = defined('WEDDING_DATETIME') ? WEDDING_DATETIME : '2025-06-27 08:00:00';

// Format the wedding date
$weddingDateObj = new DateTime($weddingDate);
$formattedWeddingDate = $weddingDateObj->format('l, j F Y'); // Format dasar
$indonesianMonths = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$indonesianDays = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];

// Terjemahkan ke Bahasa Indonesia
$dayNameEnglish = $weddingDateObj->format('l');
$monthNameEnglish = $weddingDateObj->format('F');

$formattedWeddingDate = str_replace(
    [$dayNameEnglish, $monthNameEnglish],
    [$indonesianDays[$dayNameEnglish] ?? $dayNameEnglish, $indonesianMonths[$monthNameEnglish] ?? $monthNameEnglish],
    $formattedWeddingDate
);


// Get public messages if function exists
$publicMessages = [];
if (function_exists('getApprovedMessages')) {
    try {
        $publicMessages = getApprovedMessages(5); // Ambil 5 pesan yang disetujui
    } catch (Exception $e) {
        error_log('Error getting messages: ' . $e->getMessage());
        // Anda bisa menambahkan pesan error untuk ditampilkan jika perlu, atau biarkan kosong
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Pernikahan Andhika & Bunga</title>
    <meta name="description" content="Kami mengundang Anda untuk menghadiri pernikahan Andhika dan Bunga pada tanggal <?php echo $formattedWeddingDate; ?>">
    <meta property="og:title" content="Undangan Pernikahan Andhika & Bunga">
    <meta property="og:description" content="Kami mengundang Anda untuk menghadiri pernikahan Andhika dan Bunga pada tanggal <?php echo $formattedWeddingDate; ?>">
    <meta property="og:image" content="assets/images/couple-1.jpg"> <!-- Ganti dengan gambar utama Anda -->
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AOS (Animate On Scroll) CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <!-- Custom CSS (Pastikan file ini ada dan berisi definisi bg-elegant-light, text-gold, dll.) -->
    <link rel="stylesheet" href="assets/css/main.css">

    <script>
      // Konfigurasi Tailwind CSS (opsional, jika Anda ingin customisasi lebih lanjut)
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              'great-vibes': ['Great Vibes', 'cursive'],
              'cormorant': ['Cormorant Garamond', 'serif'],
              'montserrat': ['Montserrat', 'sans-serif']
            },
            colors: {
              'gold': '#B08D57', // Contoh warna gold
              'elegant-light': '#F8F5F2', // Contoh warna light
              'elegant-dark': '#3A3A3A', // Contoh warna dark
              // Definisikan warna lain yang Anda gunakan
            }
          }
        }
      }
    </script>
    <style>
        /* Tambahkan font-face jika font belum di-host secara global atau via CDN lain */
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Great+Vibes&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        
        /* Contoh styling dasar jika tidak di main.css */
        body { font-family: 'Montserrat', sans-serif; }
        .font-great-vibes { font-family: 'Great Vibes', cursive; }
        .font-cormorant { font-family: 'Cormorant Garamond', serif; }
        .text-gold { color: #B08D57; } /* Sesuaikan dengan kode warna Anda */
        .bg-gold { background-color: #B08D57; } /* Sesuaikan */
        .bg-elegant-light { background-color: #F8F5F2; } /* Sesuaikan */
        .bg-elegant-dark { background-color: #3A3A3A; } /* Sesuaikan */

        /* Styling untuk music button */
        .music-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--gold-color, #B08D57); /* Gunakan var jika didefinisikan di main.css atau root */
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .music-button:hover {
            transform: scale(1.1);
        }
        .music-button.playing {
             /* background-color: #8c6d41;  Warna alternatif saat playing */
        }
        /* Styling untuk frame gambar mempelai */
        .img-frame {
            border: 6px solid #E0C9A6; /* Warna border yang serasi */
            padding: 8px;
            background-color: white;
        }
        /* Garis pemisah dekoratif */
        .decorative-divider {
            width: 80px;
            height: 2px;
            background-color: var(--gold-color, #B08D57); /* Gunakan var jika didefinisikan */
            margin: 20px auto;
        }
        .decorative-divider.bg-white {
            background-color: white;
        }
        /* Styling untuk tombol */
        .btn-elegant {
            background-color: var(--gold-color, #B08D57);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 9999px; /* full */
            font-family: 'Cormorant Garamond', serif;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
        }
        .btn-elegant:hover {
            background-color: #8c6d41; /* Warna gold lebih gelap saat hover */
        }
        .btn-elegant.btn-sm {
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
        }
        .btn-elegant.btn-block {
            display: block;
            width: 100%;
        }
        .copy-text {
            background-color: #f0e6d5;
            color: #8c6d41;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid #e0c9a6;
            cursor: pointer;
            font-family: 'Cormorant Garamond', serif;
            transition: all 0.2s ease;
        }
        .copy-text:hover {
            background-color: #e0c9a6;
        }
        /* Messages Carousel (Basic Structure - JS needed for functionality) */
        .messages-carousel-inner {
            display: flex; /* Required for a simple scroll or JS-based carousel */
            overflow-x: auto; /* Allows horizontal scrolling if content overflows */
            /* Add more styling for individual message cards and transitions if using JS */
        }
        .message-card {
            min-width: 100%; /* Each card takes full width of the container */
            box-sizing: border-box;
        }
    </style>
</head>
<body class="font-montserrat bg-elegant-light text-gray-800">

    <?php if (!$envelopeOpened): ?>
    <!-- Invitation Card -->
    <div id="invitation-card" class="fixed inset-0 flex items-center justify-center bg-cover bg-center z-50" style="background-image: url('assets/images/bg-pattern.jpg');">
        <div class="invitation-card-inner bg-white rounded-lg shadow-xl p-6 sm:p-8 max-w-md mx-4 text-center">
            <div class="date-badge bg-gold text-white py-2 px-6 rounded-full inline-block -mt-10 sm:-mt-12 mb-4 shadow">
                27 Juni 2025 <!-- Sesuaikan jika tanggal diambil dari PHP -->
            </div>
            
            <h3 class="font-cormorant text-gray-700 text-lg sm:text-xl mb-2">Undangan Pernikahan</h3>
            
            <h1 class="font-great-vibes text-gold text-5xl sm:text-6xl mb-4">Andhika<br>&<br>Bunga</h1>
            
            <div class="border-b border-gray-200 w-24 mx-auto my-4"></div>
            
            <p class="text-gray-700 mb-6 font-cormorant text-base sm:text-lg">Kepada Yth. Bapak/Ibu/Saudara/i:</p>
            
            <p class="font-cormorant text-xl sm:text-2xl font-semibold text-rose-600 mb-6"><?php echo !empty($guestName) ? $guestName : 'Nama Tamu'; ?></p>
            
            <?php
            $openLink = '?open=1';
            if (!empty($guestName)) {
                // Gunakan $_GET['to'] yang asli (belum di-htmlspecialchars) untuk parameter URL
                $openLink = '?to=' . urlencode(isset($_GET['to']) ? $_GET['to'] : '') . '&open=1';
            }
            ?>
            <a href="<?php echo $openLink; ?>" 
               class="bg-gold hover:bg-amber-600 text-white py-3 px-8 rounded-full font-cormorant flex items-center justify-center mx-auto max-w-xs text-lg shadow-md transition-transform transform hover:scale-105">
                <i class="fas fa-envelope-open-text mr-2"></i> Buka Undangan
            </a>
            
            <p class="text-gray-500 text-sm mt-6 italic">Ketuk untuk membuka undangan</p>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Music Player -->
    <div id="music-toggle" class="music-button">
        <i class="fas fa-play"></i> <!-- Icon akan diubah oleh JS -->
    </div>
    <audio id="background-music" loop preload="auto"> <!-- preload="auto" atau "metadata" lebih baik jika musik penting -->
        <source src="assets/audio/wedding-song.mp3" type="audio/mpeg">
        Browser Anda tidak mendukung elemen audio.
    </audio>

    <!-- Wedding Website Content -->
    <!-- Hero Section -->
    <section id="hero" class="relative h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('assets/images/hero-bg.jpg')">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative z-10 text-center px-4" data-aos="fade-up" data-aos-duration="1000">
            <h3 class="font-cormorant text-white text-xl md:text-2xl mb-4">Undangan Pernikahan</h3>
            <h1 class="font-great-vibes text-5xl md:text-7xl lg:text-8xl text-white mb-6">Andhika & Bunga</h1>
            <div class="decorative-divider bg-white"></div>
            <p class="font-cormorant text-white text-lg md:text-xl mt-6 mb-8"><?php echo $formattedWeddingDate; ?></p>
            <a href="#rsvp" class="btn-elegant">RSVP Sekarang</a>
            
            <?php if (!empty($guestName)): ?>
            <p class="text-white text-lg mt-8 font-cormorant">Selamat datang, <span class="font-semibold"><?php echo $guestName; ?></span></p>
            <?php endif; ?>
        </div>
        <div class="absolute bottom-10 left-0 right-0 flex justify-center z-10">
            <a href="#couple" class="text-white animate-bounce">
                <i class="fas fa-chevron-down text-3xl"></i>
            </a>
        </div>
    </section>

    <!-- Couple Section -->
    <section id="couple" class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Mempelai</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dalam acara pernikahan kami:</p>
            <div class="flex flex-col md:flex-row justify-center items-center md:space-x-10 lg:space-x-16 space-y-12 md:space-y-0">
                <div class="text-center" data-aos="fade-right" data-aos-duration="800">
                    <div class="w-56 h-56 sm:w-64 sm:h-64 mx-auto rounded-full overflow-hidden mb-6 img-frame shadow-lg">
                        <img src="assets/images/groom.jpg" alt="Foto Andhika Pratama" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-great-vibes text-4xl text-rose-600 mb-2">Andhika Pratama</h3>
                    <p class="font-cormorant text-gray-600 text-base">Putra dari Bpk. Ahmad Santoso<br> & Ibu Ratna Dewi</p>
                    <div class="mt-4 flex justify-center space-x-3">
                        <a href="https://instagram.com/andhika" target="_blank" class="text-gray-500 hover:text-rose-500 transition-colors">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <!-- Tambahkan ikon sosial media lain jika ada -->
                    </div>
                </div>
                <div class="relative hidden md:block" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="200">
                    <img src="assets/images/heart-ornament.png" alt="Lambang Hati" class="w-16 h-16 opacity-80">
                </div>
                <div class="text-center" data-aos="fade-left" data-aos-duration="800">
                    <div class="w-56 h-56 sm:w-64 sm:h-64 mx-auto rounded-full overflow-hidden mb-6 img-frame shadow-lg">
                        <img src="assets/images/bride.jpg" alt="Foto Bunga Citra" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-great-vibes text-4xl text-rose-600 mb-2">Bunga Citra</h3>
                    <p class="font-cormorant text-gray-600 text-base">Putri dari Bpk. Hendra Wijaya<br> & Ibu Sari Indah</p>
                    <div class="mt-4 flex justify-center space-x-3">
                        <a href="https://instagram.com/bunga" target="_blank" class="text-gray-500 hover:text-rose-500 transition-colors">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <!-- Tambahkan ikon sosial media lain jika ada -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Countdown Timer Section -->
    <section id="countdown" class="py-16 bg-elegant-light">
        <div class="container mx-auto text-center px-4">
            <h2 class="text-3xl md:text-4xl font-great-vibes text-gold mb-2" data-aos="fade-up">Menghitung Hari</h2>
            <p class="font-cormorant text-gray-600 mb-10 text-lg" data-aos="fade-up" data-aos-delay="100">Menuju hari bahagia kami</p>
            <div class="flex flex-wrap justify-center space-x-2 sm:space-x-4 md:space-x-8" id="countdown-timer-display" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 md:w-28 p-2 md:p-4 bg-white rounded-lg shadow-md border border-gray-100 mb-2">
                    <div id="days" class="text-2xl md:text-4xl font-bold text-rose-600">00</div>
                    <div class="text-xs md:text-sm text-gray-600 font-cormorant">Hari</div>
                </div>
                <div class="w-20 md:w-28 p-2 md:p-4 bg-white rounded-lg shadow-md border border-gray-100 mb-2">
                    <div id="hours" class="text-2xl md:text-4xl font-bold text-rose-600">00</div>
                    <div class="text-xs md:text-sm text-gray-600 font-cormorant">Jam</div>
                </div>
                <div class="w-20 md:w-28 p-2 md:p-4 bg-white rounded-lg shadow-md border border-gray-100 mb-2">
                    <div id="minutes" class="text-2xl md:text-4xl font-bold text-rose-600">00</div>
                    <div class="text-xs md:text-sm text-gray-600 font-cormorant">Menit</div>
                </div>
                <div class="w-20 md:w-28 p-2 md:p-4 bg-white rounded-lg shadow-md border border-gray-100 mb-2">
                    <div id="seconds" class="text-2xl md:text-4xl font-bold text-rose-600">00</div>
                    <div class="text-xs md:text-sm text-gray-600 font-cormorant">Detik</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section id="story" class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Cerita Kami</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Perjalanan cinta kami hingga hari bahagia</p>
            <div class="max-w-3xl mx-auto space-y-16 md:space-y-20">
                <!-- Story Item 1 -->
                <div class="flex flex-col md:flex-row items-center" data-aos="fade-right">
                    <div class="md:w-1/3 mb-6 md:mb-0 md:pr-6">
                        <div class="rounded-lg overflow-hidden shadow-lg img-frame">
                            <img src="assets/images/story-1.jpg" alt="Pertama Bertemu - Andhika & Bunga" class="w-full h-64 object-cover">
                        </div>
                    </div>
                    <div class="md:w-2/3 md:pl-6">
                        <h3 class="text-2xl font-cormorant font-semibold text-rose-600 mb-3">Pertama Kali Bertemu</h3>
                        <p class="text-gray-700 leading-relaxed font-cormorant text-lg">
                            Pertemuan pertama kami terjadi di sebuah kafe kecil di pusat kota Jakarta.
                            Andhika sedang membaca buku favoritnya saat secara tidak sengaja Bunga menyenggol kopinya.
                            Dari kecelakaan kecil itu, percakapan mengalir dengan sendirinya.
                        </p>
                    </div>
                </div>
                <!-- Story Item 2 -->
                <div class="flex flex-col md:flex-row-reverse items-center" data-aos="fade-left">
                    <div class="md:w-1/3 mb-6 md:mb-0 md:pl-6">
                        <div class="rounded-lg overflow-hidden shadow-lg img-frame">
                            <img src="assets/images/story-2.jpg" alt="Kencan Pertama - Andhika & Bunga" class="w-full h-64 object-cover">
                        </div>
                    </div>
                    <div class="md:w-2/3 md:pr-6">
                        <h3 class="text-2xl font-cormorant font-semibold text-rose-600 mb-3">Kencan Pertama</h3>
                        <p class="text-gray-700 leading-relaxed font-cormorant text-lg">
                            Dua minggu setelah pertemuan pertama, Andhika mengajak Bunga untuk makan malam. 
                            Malam itu kami tahu bahwa ada sesuatu yang istimewa yang sedang tumbuh di antara kami.
                        </p>
                    </div>
                </div>
                <!-- Story Item 3 -->
                <div class="flex flex-col md:flex-row items-center" data-aos="fade-right">
                    <div class="md:w-1/3 mb-6 md:mb-0 md:pr-6">
                        <div class="rounded-lg overflow-hidden shadow-lg img-frame">
                            <img src="assets/images/story-3.jpg" alt="Lamaran Andhika & Bunga" class="w-full h-64 object-cover">
                        </div>
                    </div>
                    <div class="md:w-2/3 md:pl-6">
                        <h3 class="text-2xl font-cormorant font-semibold text-rose-600 mb-3">Lamaran yang Berkesan</h3>
                        <p class="text-gray-700 leading-relaxed font-cormorant text-lg">
                            Setelah dua tahun bersama, Andhika berlutut dan melamar Bunga. Di tengah kejutan dan air mata
                            bahagia, Bunga menjawab "Ya!" dan kami memutuskan untuk memulai perjalanan seumur hidup bersama.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Event Details Section -->
    <section id="events" class="py-16 md:py-20 bg-elegant-light">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Acara Pernikahan</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Kami mengundang Anda untuk merayakan momen berharga ini</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10 max-w-5xl mx-auto">
                <div class="bg-white p-8 md:p-10 rounded-lg shadow-xl text-center" data-aos="fade-right" data-aos-duration="700">
                    <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-md">
                        <i class="fas fa-ring text-rose-500 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-cormorant font-semibold text-rose-600 mb-3">Akad Nikah</h3>
                    <div class="mb-4 font-cormorant text-lg text-gray-700">
                        <p class="mb-2"><i class="far fa-calendar-alt mr-2 text-gold"></i> <?php echo $formattedWeddingDate; ?></p>
                        <p class="mb-2"><i class="far fa-clock mr-2 text-gold"></i> 08:00 - 10:00 WIB</p>
                        <p class="mb-4"><i class="fas fa-map-marker-alt mr-2 text-gold"></i> Masjid Al-Hikmah<br>Jl. Kemanggisan Raya No. 12, Jakarta Barat</p>
                    </div>
                    <p class="text-sm text-gray-600 mb-6 font-cormorant italic">Dress Code: Putih & Gold</p>
                    <a href="https://maps.app.goo.gl/exampleAkad" target="_blank" class="btn-elegant btn-sm font-cormorant">
                        <i class="fas fa-map-marker-alt mr-2"></i> Lihat Lokasi
                    </a>
                </div>
                <div class="bg-white p-8 md:p-10 rounded-lg shadow-xl text-center" data-aos="fade-left" data-aos-duration="700" data-aos-delay="150">
                    <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-md">
                        <i class="fas fa-glass-cheers text-rose-500 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-cormorant font-semibold text-rose-600 mb-3">Resepsi</h3>
                    <div class="mb-4 font-cormorant text-lg text-gray-700">
                        <p class="mb-2"><i class="far fa-calendar-alt mr-2 text-gold"></i> <?php echo $formattedWeddingDate; ?></p>
                        <p class="mb-2"><i class="far fa-clock mr-2 text-gold"></i> 11:00 - 14:00 WIB</p>
                        <p class="mb-4"><i class="fas fa-map-marker-alt mr-2 text-gold"></i> Grand Ballroom Hotel Mulia<br>Jl. Gatot Subroto No. 123, Jakarta Selatan</p>
                    </div>
                    <p class="text-sm text-gray-600 mb-6 font-cormorant italic">Dress Code: Formal</p>
                    <a href="https://maps.app.goo.gl/exampleResepsi" target="_blank" class="btn-elegant btn-sm font-cormorant">
                        <i class="fas fa-map-marker-alt mr-2"></i> Lihat Lokasi
                    </a>
                </div>
            </div>
            <div class="mt-12 md:mt-16 max-w-5xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                <h3 class="text-2xl text-center font-cormorant font-semibold text-rose-600 mb-6">Peta Lokasi Gabungan</h3>
                <div class="rounded-lg overflow-hidden h-80 md:h-96 shadow-xl border-4 border-white">
                    <!-- Ganti dengan embed Google Maps yang mencakup kedua lokasi jika memungkinkan, atau peta utama -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126924.07000583096!2d106.69674630784909!3d-6.174062716079499!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e9b6da63%3A0x18f462abb4403727!2sJakarta%2C%20Indonesia!5e0!3m2!1sen!2sid!4v1678886400000!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Galeri Foto</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Momen-momen indah perjalanan cinta kami</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php for ($i = 1; $i <= 8; $i++) : ?>
                <div class="gallery-item overflow-hidden rounded-lg shadow-lg border-2 border-transparent hover:border-gold transition-all duration-300" data-aos="zoom-in" data-aos-delay="<?= $i * 70 ?>">
                    <a href="assets/images/gallery-<?= $i ?>.jpg" data-lightbox="wedding-gallery" data-title="Momen Indah #<?= $i ?>">
                        <img src="assets/images/gallery-<?= $i ?>-thumb.jpg" alt="Foto Prewedding <?= $i ?>" class="w-full h-48 sm:h-64 object-cover transition duration-500 transform hover:scale-110">
                    </a>
                </div>
                <?php endfor; ?>
            </div>
             <p class="text-center text-sm text-gray-500 mt-8 font-cormorant" data-aos="fade-up" data-aos-delay="200">Klik gambar untuk memperbesar.</p>
        </div>
    </section>

    <!-- Guest Messages Section -->
    <section id="messages" class="py-16 md:py-20 bg-elegant-light">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Ucapan & Doa</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Terima kasih atas doa dan ucapan dari Anda</p>
            
            <?php if (empty($publicMessages)): ?>
            <p class="text-center text-gray-500 font-cormorant italic text-lg" data-aos="fade-up" data-aos-delay="150">Belum ada ucapan dari tamu yang ditampilkan. Jadilah yang pertama!</p>
            <?php else: ?>
            <!-- Carousel Container - Tambahkan JS untuk fungsionalitas carousel -->
            <div class="max-w-4xl mx-auto relative" data-aos="fade-up" data-aos-delay="200">
                <div class="messages-carousel-inner overflow-hidden"> <!-- Wrapper untuk item -->
                    <!-- Item akan dimasukkan di sini oleh JS atau loop PHP jika statis -->
                    <?php foreach ($publicMessages as $index => $message): ?>
                    <div class="message-card min-w-full"> <!-- Pastikan setiap item mengambil lebar penuh jika JS tidak mengatur -->
                        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl mx-2 sm:mx-4">
                            <div class="text-gold text-3xl sm:text-4xl mb-4 text-center"><i class="fas fa-quote-left"></i></div>
                            <p class="italic text-gray-700 mb-6 font-cormorant text-md sm:text-lg text-center leading-relaxed"><?php echo cleanInput($message['message']); // Gunakan cleanInput atau htmlspecialchars ?></p>
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <p class="font-cormorant font-semibold text-rose-600 text-lg text-center"><?php echo cleanInput($message['name']); ?></p>
                                <p class="text-sm text-gray-500 font-cormorant text-center"><?php echo formatIndonesianDate($message['created_at']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($publicMessages) > 1): /* Tampilkan tombol navigasi jika lebih dari 1 pesan */ ?>
                <button class="carousel-prev absolute top-1/2 left-0 sm:-left-4 transform -translate-y-1/2 bg-white hover:bg-gray-100 text-gold w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center shadow-md z-10">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-next absolute top-1/2 right-0 sm:-right-4 transform -translate-y-1/2 bg-white hover:bg-gray-100 text-gold w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center shadow-md z-10">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <?php endif; ?>
                <!-- Tambahkan JS untuk mengaktifkan carousel di sini (misalnya dengan SwiperJS, Slick, atau custom JS) -->
            </div>
            <?php endif; ?>

            <div class="text-center mt-8 max-w-md mx-auto" data-aos="fade-up" data-aos-delay="250">
                <p class="text-sm text-gray-500 italic font-cormorant">Ucapan & doa Anda akan ditampilkan setelah dimoderasi oleh admin.</p>
                <a href="#rsvp" class="btn-elegant mt-6">Kirim Ucapan Anda</a>
            </div>
        </div>
    </section>

    <!-- RSVP Section -->
    <section id="rsvp" class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Konfirmasi Kehadiran</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 text-lg" data-aos="fade-up" data-aos-delay="100">Mohon konfirmasi kehadiran Anda untuk membantu persiapan kami.</p>
            <div class="max-w-2xl mx-auto bg-elegant-light p-6 sm:p-10 rounded-lg shadow-xl border border-gray-200" data-aos="fade-up" data-aos-delay="200">
                <?php
                // Display success or error messages from session
                if (isset($_SESSION['rsvp_message'])) {
                    $message_type = (strpos(strtolower($_SESSION['rsvp_message']), 'terima kasih') !== false || strpos(strtolower($_SESSION['rsvp_message']), 'berhasil') !== false) ? 'success' : 'error';
                    $bgColor = ($message_type === 'success') ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
                    echo '<div class="mb-6 p-4 rounded-lg font-cormorant text-lg border ' . $bgColor . '">' . cleanInput($_SESSION['rsvp_message']) . '</div>';
                    unset($_SESSION['rsvp_message']); // Hapus pesan setelah ditampilkan
                }
                ?>
                <!-- 
                    PENTING: Pastikan includes/process_rsvp.php menerapkan:
                    1. Validasi sisi server yang ketat untuk semua input.
                    2. Penggunaan PREPARED STATEMENTS (parameterized queries) untuk mencegah SQL Injection.
                    3. Pertimbangkan implementasi CSRF Token untuk keamanan tambahan.
                -->
                <form action="includes/process_rsvp.php" method="post" class="space-y-6">
                    <div>
                        <label for="name" class="block font-cormorant text-gray-700 mb-2 text-lg">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="<?php echo $guestName; // Sudah di-cleanInput di awal ?>" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold font-cormorant text-lg">
                    </div>
                    <div>
                        <label class="block font-cormorant text-gray-700 mb-2 text-lg">Konfirmasi Kehadiran</label>
                        <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-3 sm:space-y-0">
                            <label class="flex items-center font-cormorant cursor-pointer">
                                <input type="radio" name="attending" value="yes" class="mr-2 h-5 w-5 text-gold focus:ring-gold border-gray-300" required>
                                <span class="text-lg text-gray-800">Ya, saya akan hadir</span>
                            </label>
                            <label class="flex items-center font-cormorant cursor-pointer">
                                <input type="radio" name="attending" value="no" class="mr-2 h-5 w-5 text-gold focus:ring-gold border-gray-300" required>
                                <span class="text-lg text-gray-800">Maaf, saya tidak bisa hadir</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="guest-count-wrapper" class="hidden transition-all duration-300 ease-in-out">
                        <label for="guest_count" class="block font-cormorant text-gray-700 mb-2 text-lg">Jumlah Tamu (Termasuk Anda)</label>
                        <div class="flex items-center space-x-2">
                            <select id="guest_count" name="guest_count" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold font-cormorant text-lg">
                                <option value="1">1 orang</option>
                                <option value="2">2 orang</option>
                                <option value="3">3 orang (maks)</option> <!-- Sesuaikan batasan -->
                                <!-- <option value="4">4 orang</option>
                                <option value="5">5 orang</option> -->
                            </select>
                            <div class="tooltip relative group">
                                <i class="fas fa-info-circle text-gold text-xl cursor-help"></i>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-60 hidden group-hover:block bg-gray-800 text-white p-3 rounded-lg shadow-lg text-sm text-left font-cormorant z-10">
                                    Mohon konfirmasi jumlah tamu yang akan hadir termasuk Anda. Batas maksimal 3 orang per undangan.
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 font-cormorant italic">Harap isi jika Anda akan hadir.</p>
                    </div>
                    <div>
                        <label for="message" class="block font-cormorant text-gray-700 mb-2 text-lg">Ucapan & Doa</label>
                        <textarea id="message" name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold font-cormorant text-lg" 
                                  placeholder="Tuliskan ucapan, doa, dan harapan Anda untuk kami..."></textarea>
                        <div class="mt-2 text-sm text-gray-600 font-cormorant">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="display_message" value="1" class="mr-2 h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded" checked>
                                <span class="text-gray-700">Saya setuju ucapan ini ditampilkan di halaman (setelah dimoderasi)</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn-elegant btn-block py-3 font-cormorant text-lg">
                        Kirim Konfirmasi & Ucapan
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Gift Section -->
    <section id="gift" class="py-16 md:py-20 bg-elegant-light">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-center text-gold mb-2" data-aos="fade-up">Hadiah Pernikahan</h2>
            <p class="font-cormorant text-center text-gray-600 mb-12 md:mb-16 max-w-3xl mx-auto text-lg" data-aos="fade-up" data-aos-delay="100">
                Doa restu Anda merupakan karunia yang sangat berarti bagi kami. Jika memberi adalah ungkapan tanda kasih, Anda dapat mengirimkannya melalui:
            </p>
                
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10 max-w-xl mx-auto">
                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl text-center border border-gray-200" data-aos="fade-right" data-aos-duration="700">
                    <img src="assets/images/bca-logo.png" alt="Logo Bank BCA" class="h-12 sm:h-16 mx-auto mb-6">
                    <p class="font-cormorant font-semibold text-xl text-gray-800 mb-2">Andhika Pratama</p>
                    <p class="font-cormorant text-lg text-gray-700 mb-4 tracking-wider">1234 5678 90</p>
                    <button class="copy-text" data-text="1234567890">
                        <i class="fas fa-copy mr-2"></i> Salin Rekening
                    </button>
                </div>
                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl text-center border border-gray-200" data-aos="fade-left" data-aos-duration="700" data-aos-delay="150">
                    <img src="assets/images/mandiri-logo.png" alt="Logo Bank Mandiri" class="h-12 sm:h-16 mx-auto mb-6">
                    <p class="font-cormorant font-semibold text-xl text-gray-800 mb-2">Bunga Citra</p>
                    <p class="font-cormorant text-lg text-gray-700 mb-4 tracking-wider">0987 6543 21</p>
                    <button class="copy-text" data-text="0987654321">
                        <i class="fas fa-copy mr-2"></i> Salin Rekening
                    </button>
                </div>
            </div>
             <p class="text-center text-sm text-gray-500 mt-10 font-cormorant" data-aos="fade-up" data-aos-delay="200">Terima kasih atas kebaikan dan perhatian Anda.</p>
        </div>
    </section>

    <!-- Thank You Section -->
    <section id="thank-you" class="py-16 md:py-20 bg-rose-50 text-center">
        <div class="container mx-auto px-4">
            <img src="assets/images/floral-top-decoration.png" alt="Dekorasi Bunga" class="h-20 md:h-24 mx-auto mb-8 opacity-70" data-aos="zoom-in" data-aos-duration="800">
            <h2 class="text-4xl md:text-5xl font-great-vibes text-gold mb-6" data-aos="fade-up">Terima Kasih</h2>
            <p class="max-w-2xl mx-auto text-gray-700 font-cormorant text-lg leading-relaxed mb-8" data-aos="fade-up" data-aos-delay="100">
                Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i
                berkenan hadir untuk memberikan doa restu kepada kami. Atas kehadiran dan doa restunya, kami ucapkan terima kasih.
            </p>
            <div class="decorative-divider"></div>
            <h3 class="font-great-vibes text-5xl md:text-6xl text-rose-500 mt-10" data-aos="fade-up" data-aos-delay="200">
                Andhika & Bunga
            </h3>
            <p class="font-cormorant text-gray-600 italic mt-4 text-lg" data-aos="fade-up" data-aos-delay="300">
                <?php echo $formattedWeddingDate; ?>
            </p>
            <img src="assets/images/floral-divider.png" alt="Pemisah Bunga" class="h-12 md:h-16 mx-auto mt-10 opacity-60" data-aos="fade-up" data-aos-delay="400">
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 bg-elegant-dark text-white text-center">
        <div class="container mx-auto px-4">
            <p class="mb-2 font-cormorant text-sm">© <?php echo date('Y'); ?> Andhika & Bunga Wedding. All Rights Reserved.</p>
            <p class="text-xs opacity-70 font-cormorant">Dibuat dengan <i class="fas fa-heart text-rose-500"></i> untuk hari bahagia kami.</p>
            <!-- <p class="text-xs opacity-50 font-cormorant mt-1">Powered by [Your Name/Brand if any]</p> -->
        </div>
    </footer>

    <?php endif; // End of $envelopeOpened check ?>

    <!-- Scripts -->
    <!-- jQuery (jika masih dibutuhkan oleh Lightbox atau script lain) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Lightbox2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <!-- AOS (Animate On Scroll) JS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800, // Durasi animasi
            once: true,    // Animasi hanya terjadi sekali
            easing: 'ease-in-out-quad', // Jenis easing
            offset: 50, // Offset dari trigger point (dalam px)
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Music player functionality
            const musicButton = document.getElementById('music-toggle');
            const audioElement = document.getElementById('background-music');
            
            if (musicButton && audioElement) {
                // Coba putar musik setelah interaksi pertama (misalnya, setelah amplop dibuka)
                // Beberapa browser memblokir autoplay tanpa interaksi pengguna
                <?php if ($envelopeOpened): ?>
                // Memberi sedikit delay agar tidak terlalu abrupt
                setTimeout(() => {
                    audioElement.play().then(() => {
                        musicButton.innerHTML = '<i class="fas fa-pause"></i>';
                        musicButton.classList.add('playing');
                    }).catch(error => {
                        console.log('Autoplay musik gagal, memerlukan interaksi pengguna:', error);
                        // Biarkan tombol play seperti semula jika gagal
                         musicButton.innerHTML = '<i class="fas fa-play"></i>';
                    });
                }, 500);
                <?php endif; ?>

                musicButton.addEventListener('click', function() {
                    if (audioElement.paused) {
                        audioElement.play().then(() => {
                            musicButton.innerHTML = '<i class="fas fa-pause"></i>';
                            musicButton.classList.add('playing');
                        }).catch(error => {
                            console.log('Pemutaran audio gagal:', error);
                        });
                    } else {
                        audioElement.pause();
                        musicButton.innerHTML = '<i class="fas fa-play"></i>';
                        musicButton.classList.remove('playing');
                    }
                });
            }
            
            // Countdown Timer
            const weddingDateTime = new Date('<?php echo $weddingDate; ?>').getTime();
            const countdownTimerDisplay = document.getElementById('countdown-timer-display');

            if (countdownTimerDisplay) { // Cek apakah elemen ada
                const daysElement = document.getElementById('days');
                const hoursElement = document.getElementById('hours');
                const minutesElement = document.getElementById('minutes');
                const secondsElement = document.getElementById('seconds');

                if (daysElement && hoursElement && minutesElement && secondsElement) {
                    function updateCountdown() {
                        const now = new Date().getTime();
                        const distance = weddingDateTime - now;
                        
                        if (distance < 0) {
                            clearInterval(countdownInterval);
                            countdownTimerDisplay.innerHTML = "<h2 class='text-2xl md:text-3xl font-great-vibes text-rose-600 col-span-full text-center py-4'>Hari yang dinanti telah tiba!</h2>";
                            return;
                        }
                        
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        daysElement.innerHTML = days.toString().padStart(2, '0');
                        hoursElement.innerHTML = hours.toString().padStart(2, '0');
                        minutesElement.innerHTML = minutes.toString().padStart(2, '0');
                        secondsElement.innerHTML = seconds.toString().padStart(2, '0');
                    }
                    
                    updateCountdown(); // Panggil sekali saat load
                    const countdownInterval = setInterval(updateCountdown, 1000); // Update setiap detik
                }
            }
            
            // RSVP: Show/hide guest count based on attendance
            const attendingRadios = document.querySelectorAll('input[name="attending"]');
            const guestCountWrapper = document.getElementById('guest-count-wrapper');
            const guestCountSelect = document.getElementById('guest_count');

            if (attendingRadios.length > 0 && guestCountWrapper && guestCountSelect) {
                attendingRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'yes') {
                            guestCountWrapper.classList.remove('hidden');
                            guestCountWrapper.style.maxHeight = guestCountWrapper.scrollHeight + "px"; // Untuk animasi
                            guestCountSelect.required = true;
                        } else {
                            guestCountWrapper.classList.add('hidden');
                             guestCountWrapper.style.maxHeight = null; // Untuk animasi
                            guestCountSelect.required = false;
                            guestCountSelect.value = "1"; // Reset ke default
                        }
                    });
                });
                 // Inisialisasi jika 'yes' sudah terpilih (misalnya dari data sebelumnya)
                const initiallyAttending = document.querySelector('input[name="attending"][value="yes"]:checked');
                if (initiallyAttending) {
                    guestCountWrapper.classList.remove('hidden');
                    guestCountWrapper.style.maxHeight = guestCountWrapper.scrollHeight + "px";
                    guestCountSelect.required = true;
                }

            }
            
            // Copy bank account number functionality
            const copyButtons = document.querySelectorAll('.copy-text');
            if (copyButtons.length > 0) {
                copyButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const textToCopy = this.getAttribute('data-text');
                        if (!textToCopy) return;
                        
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            const originalHtml = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-check mr-2"></i> Berhasil Disalin!';
                            this.classList.add('bg-green-100', 'text-green-700');
                            
                            setTimeout(() => {
                                this.innerHTML = originalHtml;
                                this.classList.remove('bg-green-100', 'text-green-700');
                            }, 2500);
                        }).catch(err => {
                            console.error('Gagal menyalin teks: ', err);
                            // Tambahkan fallback atau pesan error jika perlu
                            alert('Gagal menyalin. Silakan salin secara manual.');
                        });
                    });
                });
            }
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const hrefAttribute = this.getAttribute('href');
                    // Pastikan itu adalah hash internal dan bukan hanya "#" atau hash eksternal
                    if (hrefAttribute && hrefAttribute.startsWith('#') && hrefAttribute.length > 1) {
                        const targetElement = document.querySelector(hrefAttribute);
                        if (targetElement) {
                            e.preventDefault();
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start' // Atau 'center' sesuai preferensi
                            });
                        }
                    }
                });
            });
            
            // Initialize lightbox if available
            if (typeof lightbox !== 'undefined') {
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true,
                    'fadeDuration': 300,
                    'imageFadeDuration': 300,
                    'albumLabel': "Foto %1 dari %2"
                });
            }

            // --- Message Carousel (Contoh Implementasi Sederhana - PERLU DISESUAIKAN) ---
            // Ini adalah contoh SANGAT dasar. Untuk carousel yang lebih baik, gunakan library seperti SwiperJS atau Slick.
            const carouselInner = document.querySelector('.messages-carousel-inner');
            const prevButton = document.querySelector('.carousel-prev');
            const nextButton = document.querySelector('.carousel-next');
            const messages = document.querySelectorAll('.messages-carousel-inner .message-card');

            if (carouselInner && prevButton && nextButton && messages.length > 0) {
                let currentIndex = 0;
                const totalMessages = messages.length;

                function showMessage(index) {
                    // Jika menggunakan flexbox dan overflow, cara ini mungkin perlu disesuaikan
                    // Untuk implementasi sederhana, kita bisa scroll
                    if (messages[index]) {
                        messages[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                    }
                    // Atau jika menggunakan sistem slide absolut:
                    // messages.forEach((msg, i) => {
                    //     msg.style.transform = `translateX(-${index * 100}%)`;
                    // });
                }
                
                // Sembunyikan tombol jika hanya ada 1 pesan atau tidak ada
                if (totalMessages <= 1) {
                    if(prevButton) prevButton.style.display = 'none';
                    if(nextButton) nextButton.style.display = 'none';
                } else {
                     if(prevButton) prevButton.style.display = 'flex'; // atau 'block'
                     if(nextButton) nextButton.style.display = 'flex';
                }


                if (prevButton) {
                    prevButton.addEventListener('click', () => {
                        currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalMessages - 1;
                        showMessage(currentIndex);
                    });
                }

                if (nextButton) {
                    nextButton.addEventListener('click', () => {
                        currentIndex = (currentIndex < totalMessages - 1) ? currentIndex + 1 : 0;
                        showMessage(currentIndex);
                    });
                }
                // Inisialisasi tampilan pesan pertama jika menggunakan metode transform
                // showMessage(0); 
            }
            // --- Akhir Message Carousel ---


        });
    </script>
</body>
</html>