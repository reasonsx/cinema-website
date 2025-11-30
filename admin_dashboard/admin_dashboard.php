<?php
session_start();
require_once __DIR__ . '/../backend/connection.php';
require_once __DIR__ . '/../shared/csrf.php';
require_once __DIR__ . '/views/actors/actors_functions.php';
require_once __DIR__ . '/views/directors/directors_functions.php';
require_once __DIR__ . '/views/movies/movies_functions.php';
require_once __DIR__ . '/views/users/users_functions.php';
require_once __DIR__ . '/views/screening_rooms/screening_rooms_functions.php';
require_once __DIR__ . '/views/screenings/screenings_functions.php';
require_once __DIR__ . '/views/bookings/bookings_functions.php';
require_once __DIR__ . '/views/news/news_functions.php';
//require_once __DIR__ . '/views/contact_messages/contact_functions.php';

require_once __DIR__ . '/../auth/session.php';


$session = new SessionManager($db);
$session->requireLogin(); // ensures user is logged in

if (!$session->isAdmin()) {
    header('Location: /cinema-website/views/profile/profile.php'); // redirect non-admins to profile
    exit;
}

$userId = $session->getUserId();

// ------------------- Determine view -------------------
$allowedViews = ['movies', 'actors', 'directors', 'users', 'screening_rooms', 'screenings', 'bookings', 'news',
//    'contact_messages'
];

$view = $_GET['view'] ?? 'movies';
$view = in_array($view, $allowedViews) ? $view : 'movies';

$error = '';
$success = '';

// ------------------- Handle Form Submissions -------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($view) {
        case 'actors':
            require __DIR__ . "/views/actors/actors_controller.php";
            break;

//        case 'contact_messages':
//            require __DIR__ . "/views/contact_messages/contact_messages_controller.php";
//            break;

        case 'directors':
            require __DIR__ . "/views/directors/directors_controller.php";
            break;

        case 'movies':
            require __DIR__ . "/views/movies/movies_controller.php";
            break;

        case 'users':
            require __DIR__ . "/views/users/users_controller.php";
            break;

        case 'screening_rooms':
            require __DIR__ . "/views/screening_rooms/screening_rooms_controller.php";
            break;

        case 'screenings':
            require __DIR__ . "/views/screenings/screenings_controller.php";
            break;

        case 'bookings':
            require __DIR__ . "/views/bookings/bookings_controller.php";
            break;

        case 'news':
            require __DIR__ . "/views/news/news_controller.php";
            break;
    }
}

// ------------------- Fetch Data for Views -------------------
$actors = getActors($db);
$directors = getDirectors($db);
$movies = getMovies($db);
$users = getUsers($db);
$allActors = getActorsList($db);       // For movie form
$allDirectors = getDirectorsList($db);    // For movie form
$screeningRooms = getScreeningRooms($db); // <-- NEW
$screenings = getScreenings($db);
$bookings = getBookings($db);
$newsList = getNews($db);
//$newContactMessagesCount = countNewContactMessages($db);
//$contactMessages = listContactMessages($db);

// ------------------- Include Layout -------------------
include __DIR__ . '/../shared/head.php';
include __DIR__ . '/../shared/header.php';

?>
<section class="flex min-h-[80vh] bg-gray-100 px-4 py-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white p-6 rounded-2xl shadow-xl flex flex-col">
        <div class="mb-8 flex flex-col items-center">
            <h2 class="text-2xl font-bold text-primary mb-2">Admin Panel</h2>
            <img src="../images/adminProfilePic.jpg" alt="Admin" class="w-16 h-16 rounded-full border-2 border-primary">
            <span class="text-gray-600 mt-2">Administrator</span>
        </div>

        <nav class="flex-1">
            <ul class="space-y-3">
<!--                <li>-->
<!--                    <a href="?view=contact_messages"-->
<!--                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors --><?php //= $view === 'contact_messages' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?><!--">-->
<!--                        <i class="pi pi-inbox"></i> Messages-->
<!---->
<!--                        --><?php //if (!empty($newContactMessagesCount)): ?>
<!--                            <span class="ml-auto inline-flex items-center justify-center rounded-full bg-amber-500 text-white text-xs px-2 py-0.5">-->
<!--        --><?php //= (int)$newContactMessagesCount ?>
<!--      </span>-->
<!--                        --><?php //endif; ?>
<!--                    </a>-->
<!--                </li>-->
                <li>
                    <a href="?view=movies"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'movies' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-video"></i> All Movies
                    </a>
                </li>
                <li>
                    <a href="?view=actors"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'actors' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-users"></i> All Actors
                    </a>
                </li>
                <li>
                    <a href="?view=directors"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'directors' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-user-edit"></i> All Directors
                    </a>
                </li>
                <li>
                    <a href="?view=users"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'users' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-id-card"></i> All Users
                    </a>
                </li>
                <li>
                    <a href="?view=screening_rooms"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'screening_rooms' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-th-large"></i> Screening Rooms
                    </a>
                </li>
                <li>
                    <a href="?view=screenings"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'screenings' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-calendar"></i> All Screenings
                    </a>
                </li>
                <li>
                    <a href="?view=bookings"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'bookings' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-ticket"></i> All Bookings
                    </a>
                </li>
                <li>
                    <a href="?view=news"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'news' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-book"></i> All News
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-6">
            <a href="../auth/logout.php"
               class="w-full flex items-center justify-center gap-2 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                <i class="pi pi-sign-out"></i> Logout
            </a>

        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-6 p-6 bg-white rounded-2xl shadow-xl overflow-auto">
        <!-- Toast Messages -->
        <?php
        // Show success/error messages, either from POST handlers or from GET after redirect
        if (isset($_GET['message'])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">' . htmlspecialchars($_GET['message']) . '</div>';
        } elseif (!empty($error)) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">' . htmlspecialchars($error) . '</div>';
        } elseif (!empty($success)) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">' . htmlspecialchars($success) . '</div>';
        }
        ?>

        <!-- Dynamic View Content -->
        <?php
        // Include the proper view dynamically
        $viewFile = __DIR__ . "/views/{$view}/{$view}_view.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p class='text-gray-500'>View not found.</p>";
        }
        ?>
    </main>
</section>


<script src="assets/js/admin.js"></script>