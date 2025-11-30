<?php
session_start();
require_once 'backend/connection.php';
require_once 'admin_dashboard/views/movies/movies_functions.php';
require_once 'admin_dashboard/views/screenings/screenings_functions.php';
require_once 'admin_dashboard/views/news/news_functions.php';

// Fetch data
$screenings = getScreenings($db);
$movies     = getMovies($db);
$newsList   = getNews($db);
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'shared/head.php'; ?>

<body class="bg-light text-black font-sans">

<?php include 'shared/header.php'; ?>

<!-- HERO -->
<section class="relative h-[80vh] overflow-hidden flex items-center justify-center">

    <!-- Video Element -->
    <video id="heroVideo" autoplay loop muted playsinline
           class="absolute inset-0 w-full h-full object-cover opacity-40 transition-opacity duration-700">
        <source src="/cinema-website/assets/videos/hero1.mp4" type="video/mp4">
    </video>

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Bottom Fade -->
    <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-b from-transparent to-black"></div>

    <!-- Foreground Content -->
    <div class="relative z-[5] flex flex-col items-center gap-6 px-6 text-center">
        <h1 class="text-6xl font-[Limelight] text-white drop-shadow-lg">Cinema Eclipse</h1>
        <a href="#now-playing" class="btn-full">Explore Now</a>
    </div>
</section>

<script>
    const videos = [
        "/cinema-website/assets/videos/hero1.mp4",
        "/cinema-website/assets/videos/hero2.mp4",
        "/cinema-website/assets/videos/hero3.mp4"
    ];

    let index = 0;
    const video = document.getElementById("heroVideo");

    setInterval(() => {
        index = (index + 1) % videos.length;

        // Fade out
        video.classList.add("opacity-0");

        setTimeout(() => {
            video.src = videos[index];
            video.load();
            video.play();

            // Fade in
            video.classList.remove("opacity-0");
        }, 700);

    }, 7000); // change every 7 seconds
</script>


<!-- Now Playing -->
<?php include 'components/frontpage/now-playing/now-playing-section.php'; ?>


<!-- About Us -->
<?php include 'components/frontpage/about/about-section.php'; ?>


<!-- News -->
<?php include 'components/frontpage/news/news-section.php'; ?>


<!-- Contact -->
<?php include 'components/frontpage/contact/contact-form.php'; ?>


<?php include 'shared/footer.php'; ?>

</body>
</html>
