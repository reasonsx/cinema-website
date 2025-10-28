<?php
session_start();
require_once 'include/connection.php'; // DB connection

// Include modules
require_once 'admin_dashboard/includes/actors.php';
require_once 'admin_dashboard/includes/directors.php';
require_once 'admin_dashboard/includes/movies.php';
require_once 'admin_dashboard/includes/users.php';
require_once 'admin_dashboard/includes/screening_rooms.php';
require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/bookings.php';
require_once 'admin_dashboard/includes/news.php';


// Redirect non-admins
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: login.php');
    exit;
}

// ------------------- Determine view -------------------
$allowedViews = ['movies', 'actors', 'directors', 'users', 'screening_rooms', 'screenings', 'bookings', 'news'];

$view = $_GET['view'] ?? 'movies';
$view = in_array($view, $allowedViews) ? $view : 'movies';

$error = '';
$success = '';

// ------------------- Handle Form Submissions -------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($view) {
        case 'actors':
            if (isset($_POST['add_actor'])) {
                [$success, $error] = addActorHandler($db, $_POST);
                header("Location: admin_dashboard.php?view=actors&message=" . urlencode($success ?: $error));
                exit;
            }

            if (isset($_POST['edit_actor'])) {
                [$success, $error] = editActorHandler($db, $_POST);
                header("Location: admin_dashboard.php?view=actors&message=" . urlencode($success ?: $error));
                exit;
            }

            if (isset($_POST['delete_actor'])) {
                [$success, $error] = deleteActor($db, $_POST['delete_actor_id']);
                header("Location: admin_dashboard.php?view=actors&message=" . urlencode($success ?: $error));
                exit;
            }
            break;

        case 'directors':
            if (isset($_POST['add_director'])) {
                [$success, $error] = addDirectorHandler($db, $_POST);
                header("Location: admin_dashboard.php?view=directors&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['delete_director'])) {
                [$success, $error] = deleteDirector($db, $_POST['delete_director_id']);
                header("Location: admin_dashboard.php?view=directors&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['edit_director'])) {
                [$success, $error] = editDirectorHandler($db, $_POST);
                header("Location: admin_dashboard.php?view=directors&message=" . urlencode($success ?: $error));
                exit;
            }
            break;

        case 'movies':
            if (isset($_POST['add_movie'])) {
                [$success, $error] = addMovieHandler($db, $_POST, $_FILES);
                header("Location: admin_dashboard.php?view=movies&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['edit_movie'])) {
                [$success, $error] = editMovieHandler($db, $_POST, $_FILES);
                header("Location: admin_dashboard.php?view=movies&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['delete_movie'])) {
                [$success, $error] = deleteMovie($db, $_POST['delete_movie_id']);
                header("Location: admin_dashboard.php?view=movies&message=" . urlencode($success ?: $error));
                exit;
            }
            break;

        case 'users':
            if (isset($_POST['add_user'])) {
                [$success, $error] = addUser($db, $_POST);
                header("Location: admin_dashboard.php?view=users&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['edit_user'])) {
                [$success, $error] = editUser($db, $_POST);
                header("Location: admin_dashboard.php?view=users&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['delete_user'])) {
                [$success, $error] = deleteUser($db, $_POST['delete_user_id']);
                header("Location: admin_dashboard.php?view=users&message=" . urlencode($success ?: $error));
                exit;
            }
            break;

        case 'screening_rooms':
            // Add new room
            if (isset($_POST['add_room'])) {
                [$success, $error] = addScreeningRoom($db, $_POST);
                header("Location: admin_dashboard.php?view=screening_rooms&message=" . urlencode($success ?: $error));
                exit;
            }

            // Edit existing room
            if (isset($_POST['edit_room'])) {
                [$success, $error] = editScreeningRoom($db, $_POST);
                header("Location: admin_dashboard.php?view=screening_rooms&message=" . urlencode($success ?: $error));
                exit;
            }

            // Delete room
            if (isset($_POST['delete_room'])) {
                [$success, $error] = deleteScreeningRoom($db, $_POST['room_id']);
                header("Location: admin_dashboard.php?view=screening_rooms&message=" . urlencode($success ?: $error));
                exit;
            }
            break;
        case 'screenings':
            if (isset($_POST['add_screening'])) {
                [$success, $error] = addScreening($db, $_POST);
                header("Location: admin_dashboard.php?view=screenings&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['edit_screening'])) {
                [$success, $error] = editScreening($db, $_POST);
                header("Location: admin_dashboard.php?view=screenings&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['delete_screening'])) {
                [$success, $error] = deleteScreening($db, $_POST['screening_id']);
                header("Location: admin_dashboard.php?view=screenings&message=" . urlencode($success ?: $error));
                exit;
            }
            break;
        case 'bookings':
            if (isset($_POST['add_booking'])) {
                [$success, $error] = addBooking($db, $_POST);
                header("Location: admin_dashboard.php?view=bookings&message=" . urlencode($successMsg ?: $errorMsg));
                exit;
            }
            if (isset($_POST['edit_booking'])) {
                [$successMsg, $errorMsg] = editBooking($db, $_POST);
                header("Location: admin_dashboard.php?view=bookings&message=" . urlencode($successMsg ?: $errorMsg));
                exit;
            }

            if (isset($_POST['delete_booking'])) {
                [$successMsg, $errorMsg] = deleteBooking($db, $_POST['delete_booking']);
                header("Location: admin_dashboard.php?view=bookings&message=" . urlencode($successMsg ?: $errorMsg));
                exit;
            }

            break;
        case 'news':
            if (isset($_POST['add_news'])) {
                [$success, $error] = addNews($db, $_POST);
                header("Location: admin_dashboard.php?view=news&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['edit_news'])) {
                [$success, $error] = editNews($db, $_POST);
                header("Location: admin_dashboard.php?view=news&message=" . urlencode($success ?: $error));
                exit;
            }
            if (isset($_POST['delete_news'])) {
                [$success, $error] = deleteNews($db, $_POST['delete_news_id']);
                header("Location: admin_dashboard.php?view=news&message=" . urlencode($success ?: $error));
                exit;
            }

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

// ------------------- Include Layout -------------------
include 'head.php';
include 'header.php';
?>
<section class="flex min-h-[80vh] bg-gray-100 px-4 py-6">
    <!-- Sidebar -->
    <aside class="w-64 bg-white p-6 rounded-2xl shadow-xl flex flex-col">
        <div class="mb-8 flex flex-col items-center">
            <h2 class="text-2xl font-bold text-primary mb-2">Admin Panel</h2>
            <img src="admin_dashboard/assets/images/admin-avatar.png" alt="Admin" class="w-16 h-16 rounded-full border-2 border-primary">
            <span class="text-gray-600 mt-2">Administrator</span>
        </div>

        <nav class="flex-1">
            <ul class="space-y-3">
                <li>
                    <a href="?view=movies" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'movies' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-film"></i> All Movies
                    </a>
                </li>
                <li>
                    <a href="?view=actors" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'actors' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-users"></i> All Actors
                    </a>
                </li>
                <li>
                    <a href="?view=directors" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'directors' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-user-edit"></i> All Directors
                    </a>
                </li>
                <li>
                    <a href="?view=users" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'users' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-id-card"></i> All Users
                    </a>
                </li>
                <li>
                    <a href="?view=screening_rooms" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'screening_rooms' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-th-large"></i> Screening Rooms
                    </a>
                </li>
                <li>
                    <a href="?view=screenings" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'screenings' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-calendar"></i> All Screenings
                    </a>
                </li>
                <li>
                    <a href="?view=bookings" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'bookings' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-ticket"></i> All Bookings
                    </a>
                </li>
                <li>
                    <a href="?view=news" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors <?= $view === 'news' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-200' ?>">
                        <i class="pi pi-newspaper"></i> All News
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-6">
            <a href="logout.php" class="w-full flex items-center justify-center gap-2 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
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
        $viewFile = __DIR__ . "/admin_dashboard/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p class='text-gray-500'>View not found.</p>";
        }
        ?>
    </main>
</section>


<?php include 'footer.php'; ?>
<script src="admin_dashboard/assets/js/admin.js"></script>