<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/movies.php';

// Fetch all movies
$movies = getMovies($db);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-black text-white font-sans">

<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="bg-[var(--primary)] text-black text-center py-16">
    <div class="container mx-auto px-6">
        <h1 class="text-6xl font-[Limelight] mb-6">All Movies</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Discover the latest movies now showing in our cinema. Click on a movie to learn more!
        </p>
    </div>
</section>

<!-- Movies Grid -->
<section class="py-16 bg-black">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-[Limelight] mb-10 text-center text-[var(--secondary)]">Now Playing</h2>

        <?php if (!empty($movies)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
                <?php foreach ($movies as $movie): ?>
                    <?php
                    // Safe fallbacks
                    $id = htmlspecialchars($movie['id'] ?? '');
                    $title = htmlspecialchars($movie['title'] ?? 'Untitled');
                    $poster = htmlspecialchars($movie['poster'] ?? 'assets/default-poster.jpg');
                    $genre = htmlspecialchars($movie['genre'] ?? 'Unknown genre');
                    $description = htmlspecialchars(substr($movie['description'] ?? 'No description available.', 0, 100));
                    $release_date = !empty($movie['release_date'])
                        ? date('M d, Y', strtotime($movie['release_date']))
                        : 'Release date unknown';
                    ?>
                    <div class="bg-[var(--secondary)] text-black rounded-xl shadow-lg overflow-hidden hover:scale-105 transition-transform duration-300">
                        <a href="movie.php?id=<?= $id ?>">
                            <img src="<?= $poster ?>" alt="<?= $title ?>" class="w-full h-80 object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="text-xl font-[Limelight] mb-1"><?= $title ?></h3>
                            <p class="text-sm text-black/70 mb-2"><?= $genre ?></p>
                            <p class="text-sm text-black/80"><?= $description ?>...</p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-xs text-black/60"><?= $release_date ?></span>
                                <a href="movie.php?id=<?= $id ?>"
                                   class="bg-black text-[var(--secondary)] px-3 py-1 rounded-full text-sm hover:bg-[var(--primary)] transition">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400 text-lg mt-10">
                No movies available at the moment. Please check back later!
            </p>
        <?php endif; ?>
    </div>
</section>


<?php include 'footer.php'; ?>

</body>
</html>

