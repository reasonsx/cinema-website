<section id="now-playing" class="bg-black py-16">
    <div class="max-w-7xl mx-auto text-center relative px-6">

        <!-- Header -->
        <div class="text-center mb-12">
            <h2 class="text-5xl font-[Limelight] tracking-wide text-[var(--secondary)]">NOW PLAYING</h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="h-[2px] w-16 bg-white/15"></span>
                <i class="pi pi-star text-[var(--secondary)]"></i>
                <span class="h-[2px] w-16 bg-white/15"></span>
            </div>
        </div>

        <!-- Carousel Wrapper -->
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

        <!-- "ALL MOVIES" BUTTON -->
        <div class="mt-10 flex justify-center">
            <a href="/cinema-website/movies-list.php" class="btn">
                All Movies
            </a>
        </div>

    </div>
</section>

<script>
    const carousel = document.getElementById('movies-carousel');
    const scrollAmount = 300;

    function scrollCarousel(direction) {
        carousel.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
