<?php
session_start();
require_once 'include/connection.php'; // DB connection

// Include modules
require_once 'admin_dashboard/includes/actors.php';
require_once 'admin_dashboard/includes/directors.php';
require_once 'admin_dashboard/includes/movies.php';
require_once 'admin_dashboard/includes/users.php';

// Redirect non-admins
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: login.php');
    exit;
}

// ------------------- Determine view -------------------
$allowedViews = ['movies', 'actors', 'directors', 'users'];
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
            }
            if (isset($_POST['delete_actor'])) {
                [$success, $error] = deleteActor($db, $_POST['delete_actor_id']);
            }
            if (isset($_POST['edit_actor'])) {
                [$success, $error] = editActorHandler($db, $_POST);
            }
            break;
            
         case 'directors':
            if (isset($_POST['add_director'])) {
                [$success, $error] = addDirectorHandler($db, $_POST);
            }
            if (isset($_POST['delete_director'])) {
                [$success, $error] = deleteDirector($db, $_POST['delete_director_id']);
            }
            if (isset($_POST['edit_director'])) {
                [$successMessage, $errorMessage] = editDirectorHandler($db, $_POST);
            }

            break;

       case 'movies':
            if (isset($_POST['add_movie'])) {
                [$success, $error] = addMovieHandler($db, $_POST, $_FILES);
            }
            if (isset($_POST['edit_movie'])) {
                [$success, $error] = editMovieHandler($db, $_POST, $_FILES);
            }
            if (isset($_POST['delete_movie'])) {
                [$success, $error] = deleteMovie($db, $_POST['delete_movie_id']);
            }
            break;


        case 'users':
                if (isset($_POST['add_user'])) {
                    [$success, $error] = addUser($db, $_POST);
                }
                if (isset($_POST['edit_user'])) {
                    [$success, $error] = editUser($db, $_POST);
                }
                if (isset($_POST['delete_user'])) {
                    [$success, $error] = deleteUser($db, $_POST['delete_user_id']);
                }
                break;

    }
}

// ------------------- Fetch Data for Views -------------------
$actors       = getActors($db);
$directors    = getDirectors($db);
$movies       = getMovies($db);
$users        = getUsers($db);
$allActors    = getActorsList($db);       // For movie form
$allDirectors = getDirectorsList($db);    // For movie form

// ------------------- Include Layout -------------------
include 'head.php';
include 'header.php';
?>

<section class="flex min-h-[80vh] px-4">
    <!-- Sidebar -->
    <aside class="w-1/4 bg-white p-4 rounded-xl shadow-lg mr-4">
        <h3 class="text-xl font-bold mb-4">Admin Panel</h3>
        <ul class="flex flex-col gap-2">
            <li><a href="?view=movies" class="<?= $view === 'movies' ? 'text-primary' : 'text-gray-700' ?>">All Movies</a></li>
            <li><a href="?view=actors" class="<?= $view === 'actors' ? 'text-primary' : 'text-gray-700' ?>">All Actors</a></li>
            <li><a href="?view=directors" class="<?= $view === 'directors' ? 'text-primary' : 'text-gray-700' ?>">All Directors</a></li>
            <li><a href="?view=users" class="<?= $view === 'users' ? 'text-primary' : 'text-gray-700' ?>">All Users</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <?php if (!empty($error)) : ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($success)) : ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

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
