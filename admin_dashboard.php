<?php
session_start();
require_once 'include/connection.php';

// Redirect non-admins
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header('Location: login.php');
    exit;
}

// Determine which view to show
$view = $_GET['view'] ?? 'movies';
$movies = [];
$users = [];
$error = '';

try {
    if ($view === 'movies') {
        $stmt = $db->query("SELECT id, title, release_year, rating, description, length, poster FROM movies ORDER BY id DESC");
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($view === 'users') {
        // Fetch users excluding password
        $stmt = $db->query("SELECT id, firstname, lastname, email, isAdmin FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="flex min-h-[80vh] px-4">
    <!-- Sidebar -->
    <aside class="w-1/4 bg-white p-4 rounded-xl shadow-lg mr-4">
        <h3 class="text-xl font-bold mb-4">Admin Panel</h3>
        <ul class="flex flex-col gap-2">
            <li><a href="admin_dashboard.php?view=movies" class="<?= $view === 'movies' ? 'text-primary' : 'text-gray-700' ?>">All Movies</a></li>
            <li><a href="admin_dashboard.php?view=venues" class="<?= $view === 'venues' ? 'text-primary' : 'text-gray-700' ?>">All Venues</a></li>
            <li><a href="admin_dashboard.php?view=actors" class="<?= $view === 'actors' ? 'text-primary' : 'text-gray-700' ?>">All Actors</a></li>
            <li><a href="admin_dashboard.php?view=directors" class="<?= $view === 'directors' ? 'text-primary' : 'text-gray-700' ?>">All Directors</a></li>
            <li><a href="admin_dashboard.php?view=screenings" class="<?= $view === 'screenings' ? 'text-primary' : 'text-gray-700' ?>">All Screenings</a></li>
            <li><a href="admin_dashboard.php?view=bookings" class="<?= $view === 'bookings' ? 'text-primary' : 'text-gray-700' ?>">All Bookings</a></li>
            <li><a href="admin_dashboard.php?view=users" class="<?= $view === 'users' ? 'text-primary' : 'text-gray-700' ?>">All Users</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
        <?php if (!empty($error)) : ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($view === 'movies') : ?>
            <h2 class="text-2xl font-bold mb-4">All Movies</h2>
            <?php if (!empty($movies)) : ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2">ID</th>
                            <th class="border-b px-4 py-2">Poster</th>
                            <th class="border-b px-4 py-2">Title</th>
                            <th class="border-b px-4 py-2">Release Year</th>
                            <th class="border-b px-4 py-2">Rating</th>
                            <th class="border-b px-4 py-2">Length (min)</th>
                            <th class="border-b px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie) : ?>
                            <tr>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['id']); ?></td>
                                <td class="border-b px-4 py-2">
                                    <?php if (!empty($movie['poster'])) : ?>
                                        <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="w-20 h-auto rounded">
                                    <?php else : ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['release_year']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['rating']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['length']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($movie['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No movies found.</p>
            <?php endif; ?>

        <?php elseif ($view === 'users') : ?>
            <h2 class="text-2xl font-bold mb-4">All Users</h2>
            <?php if (!empty($users)) : ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2">ID</th>
                            <th class="border-b px-4 py-2">First Name</th>
                            <th class="border-b px-4 py-2">Last Name</th>
                            <th class="border-b px-4 py-2">Email</th>
                            <th class="border-b px-4 py-2">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($user['id']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($user['firstname']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($user['lastname']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="border-b px-4 py-2"><?php echo $user['isAdmin'] ? 'Yes' : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No users found.</p>
            <?php endif; ?>

        <?php else : ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo ucfirst($view); ?></h2>
            <p>Coming soon...</p>
        <?php endif; ?>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
