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

<section class="flex min-h-[80vh] px-4">
    <!-- Sidebar -->
    <aside class="w-[200px] bg-white p-4 rounded-xl shadow-lg mr-4">
        <h3 class="text-xl font-bold mb-4">Admin Panel</h3>
        <ul class="flex flex-col gap-2">
            <li><a href="?view=movies" class="<?= $view === 'movies' ? 'text-primary' : 'text-gray-700' ?>">All
                    Movies</a></li>
            <li><a href="?view=actors" class="<?= $view === 'actors' ? 'text-primary' : 'text-gray-700' ?>">All
                    Actors</a></li>
            <li><a href="?view=directors" class="<?= $view === 'directors' ? 'text-primary' : 'text-gray-700' ?>">All
                    Directors</a></li>
            <li><a href="?view=users" class="<?= $view === 'users' ? 'text-primary' : 'text-gray-700' ?>">All Users</a>
            </li>
            <li><a href="?view=screening_rooms"
                    class="<?= $view === 'screening_rooms' ? 'text-primary' : 'text-gray-700' ?>">All Screening
                    Rooms</a></li> <!-- NEW -->
            <li><a href="?view=screenings" class="<?= $view === 'screenings' ? 'text-primary' : 'text-gray-700' ?>">All
                    Screenings</a></li>
            <li><a href="?view=bookings" class="<?= $view === 'bookings' ? 'text-primary' : 'text-gray-700' ?>">All
                    Bookings</a></li>
            <li><a href="?view=news" class="<?= $view === 'news' ? 'text-primary' : 'text-gray-700' ?>">All News</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <?php
        // Show success/error messages, either from POST handlers or from GET after redirect
        if (isset($_GET['message'])) {
            echo '<p class="text-green-500 mb-4">' . htmlspecialchars($_GET['message']) . '</p>';
        } elseif (!empty($error)) {
            echo '<p class="text-red-500 mb-4">' . htmlspecialchars($error) . '</p>';
        } elseif (!empty($success)) {
            echo '<p class="text-green-500 mb-4">' . htmlspecialchars($success) . '</p>';
        }
        ?>


        <?php
        // Include the proper view dynamically
        $viewFile = __DIR__ . "/admin_dashboard/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>View not found.</p>";
        }
        ?>
    </main>
</section>

<?php include 'footer.php'; ?>
<script src="admin_dashboard/assets/js/admin.js"></script>