<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/movies.php';
require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/news.php';

$screenings = getScreenings($db); // All screenings
$movies = getMovies($db);         // All movies
$newsList = getNews($db);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="relative bg-[var(--primary)] text-black text-center h-[80vh]">
    <div class="container mx-auto flex flex-col items-center justify-start relative h-full gap-6 pt-20">
        <h1 class="text-6xl font-[Limelight] text-[var(--black)]">Eclipse Cinema</h1>
        <a href="#now-playing" class="btn">Explore Now</a>
        <img src="images/film-reel.png" alt="Film Reel" class="w-96 md:w-[35rem] lg:w-[45rem] absolute bottom-0">
    </div>
</section>

<!-- "Now Playing" SECTION -->
<!-- "Now Playing" SECTION -->
<section id="now-playing" class="bg-black py-16">
    <div class="max-w-7xl mx-auto text-center relative px-6">
        <div class="text-center mb-12">
            <h2 class="text-5xl font-[Limelight] tracking-wide text-[var(--secondary)]">NOW PLAYING</h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="h-[2px] w-16 bg-white/15"></span>
                <i class="pi pi-star text-[var(--secondary)]"></i>
                <span class="h-[2px] w-16 bg-white/15"></span>
            </div>
        </div>

        <div class="relative flex items-center px-4">

            <!-- LEFT ARROW -->
            <button onclick="scrollCarousel(-1)"
                    class="w-12 h-12 flex items-center justify-center rounded-full bg-black/50 text-white hover:bg-black transition z-10">
                <i class="pi pi-angle-left text-2xl"></i>
            </button>

            <!-- Carousel -->
            <div id="movies-carousel" class="flex gap-6 overflow-hidden mx-4 flex-1 p-2">
                <?php foreach ($movies as $movie): ?>
                    <div class="w-60 flex-shrink-0 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300 overflow-visible">
                        <a href="movie.php?id=<?= $movie['id'] ?>">
                            <img src="<?= htmlspecialchars($movie['poster']) ?>"
                                 alt="<?= htmlspecialchars($movie['title']) ?>"
                                 class="w-full h-80 object-cover rounded-lg">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT ARROW -->
            <button onclick="scrollCarousel(1)"
                    class="w-12 h-12 flex items-center justify-center rounded-full bg-black/50 text-white hover:bg-black transition z-10">
                <i class="pi pi-angle-right text-2xl"></i>
            </button>
        </div>

        <!-- ALL MOVIES BUTTON -->
        <div class="mt-10 flex justify-center">
            <a href="/cinema-website/movies-list.php"
               class="btn">
                All Movies
            </a>
        </div>

    </div>
</section>


<script>
    const carousel = document.getElementById('movies-carousel');
    const scrollAmount = 300; // adjust to move multiple posters at a time

    function scrollCarousel(direction) {
        carousel.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>

<!-- About Us Section -->
<section id="about-us" class="py-16 bg-black text-[#fcb885]">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 px-6">
        <!-- Image -->
        <img src="images/cinema-about.png" alt="Our Cinema" class="w-full md:w-1/2 ">

        <!-- Text -->
        <div class="md:w-1/2">
            <h2 class="text-5xl font-[Limelight] uppercase mb-6">Our Cinema</h2>
            <p class="mb-4 leading-relaxed">
                Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Sit Amet Facilisis Urna. Praesent Ac Nisi
                At Magna Tempus Facilisis. Quisque Euismod, Turpis Id Lacinia Elementum, Nibh Libero Cursus Nulla, Non
                Interdum Risus Mi Vel Massa.
            </p>
            <p class="leading-relaxed">
                Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Sit Amet Facilisis Urna. Praesent Ac Nisi
                At.
            </p>
        </div>
    </div>
</section>


<!-- News Section -->
<?php include 'components/news/news-section.php'; ?>


<!-- Contact Us Section -->
<?php include 'components/contact/contact-form.php'; ?>


<?php include 'footer.php'; ?>
</body>
</html>
