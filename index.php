<?php
session_start();
require_once 'include/connection.php';

// Fetch data
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
    <a href="#now-playing" class="bg-[var(--black)] text-[var(--white)] px-6 py-2 rounded-full font-[Limelight] hover:bg-[var(--secondary)] transition-colors duration-300">
      Explore Now
    </a>
    <img src="images/film-reel.png" alt="Film Reel" class="w-96 md:w-[35rem] lg:w-[45rem] absolute bottom-0">
  </div>
</section>

<!-- Now Playing Section -->
<section id="now-playing" class="bg-[var(--secondary)] py-16">
    <div class="max-w-7xl mx-auto text-center relative px-6">
        <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-12">NOW PLAYING</h2>

        <!-- Carousel wrapper with arrows outside -->
        <div class="relative flex items-center">
            
            <!-- Left arrow -->
            <button onclick="scrollCarousel(-1)" class="bg-black/50 text-white p-4 rounded-full hover:bg-black transition z-10">
                &#10094;
            </button>

            <!-- Carousel items -->
            <div id="movies-carousel" class="flex gap-6 overflow-hidden mx-4 flex-1">
                <?php foreach ($movies as $movie): ?>
                    <div class="w-60 flex-shrink-0 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300">
                        <a href="movie.php?id=<?= $movie['id'] ?>">
                            <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="w-full h-80 object-cover rounded-lg">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Right arrow -->
            <button onclick="scrollCarousel(1)" class="bg-black/50 text-white p-4 rounded-full hover:bg-black transition z-10">
                &#10095;
            </button>

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
<!-- About Us Section -->
<section id="about-us" class="py-16 bg-black text-[#fcb885]">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 px-6">
        <!-- Image -->
        <img src="images/cinema-about.png" alt="Our Cinema" class="w-full md:w-1/2 ">

        <!-- Text -->
        <div class="md:w-1/2">
            <h2 class="text-5xl font-[Limelight] uppercase mb-6">Our Cinema</h2>
            <p class="mb-4 leading-relaxed">
                Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Sit Amet Facilisis Urna. Praesent Ac Nisi At Magna Tempus Facilisis. Quisque Euismod, Turpis Id Lacinia Elementum, Nibh Libero Cursus Nulla, Non Interdum Risus Mi Vel Massa.
            </p>
            <p class="leading-relaxed">
                Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Sit Amet Facilisis Urna. Praesent Ac Nisi At.
            </p>
        </div>
    </div>
</section>


<!-- Contact Us Section -->
<!-- Contact Us Section -->
<section id="contact-us" class="py-16 bg-[var(--primary)] text-black">
    <div class="container mx-auto text-center max-w-xl">
        <h2 class="text-5xl font-[Limelight] mb-8 text-[var(--black)]">CONTACT US</h2>
        <form class="flex flex-col gap-4">
    <input type="text" placeholder="Subject"
        class="w-full px-4 py-2 rounded border border-black bg-[var(--primary)] text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]">
    <input type="email" placeholder="Email"
        class="w-full px-4 py-2 rounded border border-black bg-[var(--primary)] text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]">
    <textarea placeholder="Message Text"
        class="w-full px-4 py-2 rounded border border-black bg-[var(--primary)] text-black placeholder-black h-32 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]"></textarea>
    <button type="submit"
        class="bg-black text-[var(--primary)] px-6 py-2 rounded transition-colors duration-300">Submit</button>
</form>

    </div>
</section>

<!-- News Section -->
<section id="news" class="py-16 bg-black text-white">
    <div class="mx-auto max-w-7xl px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h2 class="text-5xl font-[Limelight] tracking-wide text-[var(--secondary)]">NEWS</h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="h-[2px] w-16 bg-white/15"></span>
                <i class="pi pi-star text-[var(--secondary)]"></i>
                <span class="h-[2px] w-16 bg-white/15"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($newsList as $news): ?>
                <?php
                $id = (int)$news['id'];
                $title = htmlspecialchars($news['title'] ?? 'Untitled');
                $dateAdded = isset($news['date_added']) ? date('M d, Y', strtotime($news['date_added'])) : '';
                $content = htmlspecialchars($news['content'] ?? '');
                $excerpt = mb_strlen($content) > 260 ? mb_substr($content, 0, 260) . '…' : $content;
                ?>
                <article class="group rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl hover:shadow-[0_0_30px_rgba(248,161,90,0.25)] transition overflow-hidden">
                    <!-- Card header -->
                    <div class="px-5 pt-5">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-2xl font-[Limelight] leading-snug text-white group-hover:text-[var(--secondary)] transition">
                                <?= $title ?>
                            </h3>
                            <div class="shrink-0 inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/40 px-3 py-1 text-xs text-white/80">
                                <i class="pi pi-calendar text-[var(--secondary)]"></i>
                                <span><?= $dateAdded ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mx-5 my-4 h-px bg-white/10"></div>

                    <!-- Excerpt -->
                    <div class="px-5 pb-6">
                        <p class="text-white/85 leading-relaxed mb-5"><?= $excerpt ?></p>

                        <a href="news-details.php?id=<?= $id ?>"
                           class="inline-flex items-center gap-2 rounded-full border border-[var(--secondary)]/60 px-5 py-2.5 text-sm font-semibold text-[var(--secondary)] hover:bg-[var(--secondary)] hover:text-black transition">
                            <i class="pi pi-angle-right"></i>
                            Read more
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>





<?php include 'footer.php'; ?>
</body>
</html>
