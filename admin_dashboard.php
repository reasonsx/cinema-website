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
$movies = $users = $actors = $directors = [];
$error = '';
$success = '';

// ------------------- Handle Form Submissions -------------------

// Add new actor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_actor'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);

    try {
        $stmt = $db->prepare("INSERT INTO actors (first_name, last_name, date_of_birth, gender, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $dob, $gender, $description]);
        $success = "Actor added successfully!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Add new director
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_director'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);

    try {
        $stmt = $db->prepare("INSERT INTO directors (first_name, last_name, date_of_birth, gender, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $dob, $gender, $description]);
        $success = "Director added successfully!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Add new movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $title = trim($_POST['title']);
    $release_year = trim($_POST['release_year']);
    $rating = trim($_POST['rating']);
    $length = trim($_POST['length']);
    $description = trim($_POST['description']);
    $posterPath = '';

    // Handle poster upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['poster']['type'];
        $fileSize = $_FILES['poster']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize < 10 * 1024 * 1024) {
            $targetDir = 'images/';
            $fileName = time() . '_' . basename($_FILES['poster']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
                $posterPath = $targetFile;
            } else {
                $error = "Failed to upload poster image.";
            }
        } else {
            $error = "Invalid file type or size. Only JPEG, PNG, GIF under 10MB allowed.";
        }
    }

    if (empty($error)) {
        try {
            // Insert movie
            $stmt = $db->prepare("INSERT INTO movies (title, release_year, rating, length, description, poster) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $release_year, $rating, $length, $description, $posterPath]);
            $movie_id = $db->lastInsertId();

            // Link selected actors
                if (!empty($_POST['actors'])) {
                    $stmt = $db->prepare("INSERT INTO actorAppearIn (actor_id, movie_id) VALUES (?, ?)");
                    foreach ($_POST['actors'] as $actor_id) {
                        $stmt->execute([$actor_id, $movie_id]);
                    }
                }

                // Link selected directors
                if (!empty($_POST['directors'])) {
                    $stmt = $db->prepare("INSERT INTO directorDirects (director_id, movie_id) VALUES (?, ?)");
                    foreach ($_POST['directors'] as $director_id) {
                        $stmt->execute([$director_id, $movie_id]);
                    }
                }



            $success = "Movie added successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// ------------------- Fetch Data for Views -------------------
try {
    if ($view === 'movies') {
        $stmt = $db->query("SELECT * FROM movies ORDER BY id DESC");
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($view === 'users') {
        $stmt = $db->query("SELECT id, firstname, lastname, email, isAdmin FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($view === 'actors') {
        $stmt = $db->query("SELECT * FROM actors ORDER BY last_name");
        $actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($view === 'directors') {
        $stmt = $db->query("SELECT * FROM directors ORDER BY last_name");
        $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Always fetch lists for movie form
    $stmt = $db->query("SELECT id, first_name, last_name FROM actors ORDER BY last_name");
    $allActors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->query("SELECT id, first_name, last_name FROM directors ORDER BY last_name");
    $allDirectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <p class="text-red-500 mb-4"><?= $error ?></p>
        <?php elseif (!empty($success)) : ?>
            <p class="text-green-500 mb-4"><?= $success ?></p>
        <?php endif; ?>

        <!-- Movies View -->
        <?php if ($view === 'movies') : ?>
            <h2 class="text-2xl font-bold mb-4">All Movies</h2>

            <!-- Add New Movie -->
            <details class="mb-6">
                <summary class="cursor-pointer text-primary font-semibold">Add New Movie</summary>
                <form action="" method="post" enctype="multipart/form-data" class="flex flex-col gap-3 mt-3">
                    <input type="text" name="title" placeholder="Title" required class="border p-2 rounded">
                    <input type="number" name="release_year" placeholder="Release Year" required class="border p-2 rounded">
                    <input type="text" name="rating" placeholder="Rating" required class="border p-2 rounded">
                    <input type="number" name="length" placeholder="Length (min)" required class="border p-2 rounded">
                    <textarea name="description" placeholder="Description" required class="border p-2 rounded"></textarea>
                    <input type="file" name="poster" accept="image/*">

                    <!-- Actors -->
                        <label>Actors:</label>
                        <div class="flex flex-wrap gap-2 mb-2" id="actors-container">
                            <?php foreach ($allActors as $actor) : ?>
                                <div class="actor-btn border px-3 py-1 rounded cursor-pointer" 
                                    data-id="<?= $actor['id'] ?>">
                                    <?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Directors -->
                        <label>Directors:</label>
                        <div class="flex flex-wrap gap-2 mb-2" id="directors-container">
                            <?php foreach ($allDirectors as $director) : ?>
                                <div class="director-btn border px-3 py-1 rounded cursor-pointer" 
                                    data-id="<?= $director['id'] ?>">
                                    <?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>


                    <button type="submit" name="add_movie" class="btn bg-primary text-white rounded mt-2">Add Movie</button>
                </form>
            </details>

            <?php if (!empty($movies)) : ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2">ID</th>
                            <th class="border-b px-4 py-2">Poster</th>
                            <th class="border-b px-4 py-2">Title</th>
                            <th class="border-b px-4 py-2">Release Year</th>
                            <th class="border-b px-4 py-2">Rating</th>
                            <th class="border-b px-4 py-2">Length</th>
                            <th class="border-b px-4 py-2">Description</th>
                            <th class="border-b px-4 py-2">Actors</th>
                            <th class="border-b px-4 py-2">Directors</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movies as $movie) : ?>
                            <tr>
                                <td class="border-b px-4 py-2"><?= $movie['id'] ?></td>
                                <td class="border-b px-4 py-2">
                                    <?php if (!empty($movie['poster'])) : ?>
                                        <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="w-20 h-auto rounded">
                                    <?php else : ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($movie['title']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($movie['release_year']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($movie['rating']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($movie['length']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($movie['description']) ?></td>

                                <!-- List Actors -->
                                <td class="border-b px-4 py-2">
                                    <?php
                                    $stmt = $db->prepare("SELECT a.first_name, a.last_name FROM actors a 
                                                          JOIN actorAppearIn aa ON a.id = aa.actor_id 
                                                          WHERE aa.movie_id = ?");
                                    $stmt->execute([$movie['id']]);
                                    $movieActors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($movieActors as $a) {
                                        echo htmlspecialchars($a['first_name'].' '.$a['last_name']).'<br>';
                                    }
                                    ?>
                                </td>

                                <!-- List Directors -->
                                <td class="border-b px-4 py-2">
                                    <?php
                                    $stmt = $db->prepare("SELECT d.first_name, d.last_name FROM directors d 
                                                          JOIN directorDirects dd ON d.id = dd.director_id 
                                                          WHERE dd.movie_id = ?");
                                    $stmt->execute([$movie['id']]);
                                    $movieDirectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($movieDirectors as $d) {
                                        echo htmlspecialchars($d['first_name'].' '.$d['last_name']).'<br>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No movies found.</p>
            <?php endif; ?>

        <!-- Users View -->
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
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($user['id']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($user['firstname']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($user['lastname']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="border-b px-4 py-2"><?= $user['isAdmin'] ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No users found.</p>
            <?php endif; ?>

        <!-- Actors View -->
        <?php elseif ($view === 'actors') : ?>
            <h2 class="text-2xl font-bold mb-4">All Actors</h2>

            <!-- Add Actor -->
            <details class="mb-6">
                <summary class="cursor-pointer text-primary font-semibold">Add New Actor</summary>
                <form action="" method="post" class="flex flex-col gap-3 mt-3">
                    <input type="text" name="first_name" placeholder="First Name" required class="border p-2 rounded">
                    <input type="text" name="last_name" placeholder="Last Name" required class="border p-2 rounded">
                    <input type="date" name="date_of_birth" placeholder="Date of Birth" class="border p-2 rounded">
                    <select name="gender" class="border p-2 rounded">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <textarea name="description" placeholder="Description" class="border p-2 rounded"></textarea>
                    <button type="submit" name="add_actor" class="btn bg-primary text-white rounded mt-2">Add Actor</button>
                </form>
            </details>

            <?php if (!empty($actors)) : ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2">ID</th>
                            <th class="border-b px-4 py-2">Name</th>
                            <th class="border-b px-4 py-2">DOB</th>
                            <th class="border-b px-4 py-2">Gender</th>
                            <th class="border-b px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actors as $actor) : ?>
                            <tr>
                                <td class="border-b px-4 py-2"><?= $actor['id'] ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($actor['date_of_birth']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($actor['gender']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($actor['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No actors found.</p>
            <?php endif; ?>

        <!-- Directors View -->
        <?php elseif ($view === 'directors') : ?>
            <h2 class="text-2xl font-bold mb-4">All Directors</h2>

            <!-- Add Director -->
            <details class="mb-6">
                <summary class="cursor-pointer text-primary font-semibold">Add New Director</summary>
                <form action="" method="post" class="flex flex-col gap-3 mt-3">
                    <input type="text" name="first_name" placeholder="First Name" required class="border p-2 rounded">
                    <input type="text" name="last_name" placeholder="Last Name" required class="border p-2 rounded">
                    <input type="date" name="date_of_birth" placeholder="Date of Birth" class="border p-2 rounded">
                    <select name="gender" class="border p-2 rounded">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <textarea name="description" placeholder="Description" class="border p-2 rounded"></textarea>
                    <button type="submit" name="add_director" class="btn bg-primary text-white rounded mt-2">Add Director</button>
                </form>
            </details>

            <?php if (!empty($directors)) : ?>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b px-4 py-2">ID</th>
                            <th class="border-b px-4 py-2">Name</th>
                            <th class="border-b px-4 py-2">DOB</th>
                            <th class="border-b px-4 py-2">Gender</th>
                            <th class="border-b px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($directors as $director) : ?>
                            <tr>
                                <td class="border-b px-4 py-2"><?= $director['id'] ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($director['date_of_birth']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($director['gender']) ?></td>
                                <td class="border-b px-4 py-2"><?= htmlspecialchars($director['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No directors found.</p>
            <?php endif; ?>

        <?php else : ?>
            <h2 class="text-2xl font-bold mb-4"><?= ucfirst($view) ?></h2>
            <p>Coming soon...</p>
        <?php endif; ?>
    </main>
</section>

<?php include 'footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = (container, inputName) => {
        container.addEventListener('click', e => {
            const target = e.target;
            if (!target.classList.contains('actor-btn') && !target.classList.contains('director-btn')) return;

            // Toggle selected class for styling
            target.classList.toggle('selected');

            // Remove old hidden inputs
            container.querySelectorAll('input[type="hidden"]').forEach(el => el.remove());

            // Add new hidden inputs for each selected
            Array.from(container.querySelectorAll('.selected')).forEach(el => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputName;
                input.value = el.dataset.id;
                container.appendChild(input);
            });
        });
    };

    const actorContainer = document.getElementById('actors-container');
    const directorContainer = document.getElementById('directors-container');

    toggleButtons(actorContainer, 'actors[]');
    toggleButtons(directorContainer, 'directors[]');
});

</script>


</body>
</html>
