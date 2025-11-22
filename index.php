<?php
session_start();
require_once 'include/connection.php';
require_once 'admin_dashboard/includes/movies.php';
require_once 'admin_dashboard/includes/screenings.php';
require_once 'admin_dashboard/includes/news.php';

// Fetch data
$screenings = getScreenings($db);
$movies     = getMovies($db);
$newsList   = getNews($db);
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
        <a href="#now-playing" class="btn-full">Explore Now</a>

        <img src="images/film-reel.png"
             alt="Film Reel"
             class="w-96 md:w-[35rem] lg:w-[45rem] absolute bottom-0">
    </div>
</section>


<!-- Now Playing -->
<?php include 'components/frontpage/now-playing/now-playing-section.php'; ?>


<!-- About Us -->
<?php include 'components/frontpage/about/about-section.php'; ?>


<!-- News -->
<?php include 'components/frontpage/news/news-section.php'; ?>


<!-- Contact -->
<?php include 'components/frontpage/contact/contact-form.php'; ?>


<?php include 'footer.php'; ?>

</body>
</html>
