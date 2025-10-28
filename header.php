<!-- header.php -->
<?php
$current = basename($_SERVER['PHP_SELF']);
function isActive($page, $current) {
    return $current === $page ? 'text-[var(--secondary)] after:scale-x-100' : 'text-white/90 hover:text-[var(--secondary)]';
}
?>
<header class="sticky top-0 z-50">
    <!-- Top Info Bar -->
    <div class="bg-black/90 text-gray-300 text-xs md:text-sm border-b border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-2 flex justify-between items-center">
            <div class="flex gap-4 md:gap-6">
                <span class="flex items-center gap-2"><i class="pi pi-clock text-[var(--secondary)]"></i> Mon–Sun: 10:00–24:00</span>
                <span class="hidden sm:flex items-center gap-2"><i class="pi pi-envelope text-[var(--secondary)]"></i> info@mycinema.com</span>
            </div>
            <div class="flex gap-3 text-lg">
                <a href="#" class="text-white/70 hover:text-[var(--secondary)] transition"><i class="pi pi-facebook"></i></a>
                <a href="#" class="text-white/70 hover:text-[var(--secondary)] transition"><i class="pi pi-instagram"></i></a>
                <a href="#" class="text-white/70 hover:text-[var(--secondary)] transition"><i class="pi pi-twitter"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="bg-black/70 backdrop-blur supports-[backdrop-filter]:backdrop-blur-md border-b border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
            <!-- Brand -->
            <a href="index.php" class="flex items-center gap-2">
                <i class="pi pi-video text-[var(--secondary)] text-2xl md:text-3xl"></i>
                <span class="text-xl md:text-2xl font-bold tracking-wide text-white">MyCinema</span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="movies-list.php"
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)] after:transition
                  <?= isActive('movies-list.php', $current) ?>">
                    WHAT'S ON
                </a>
                <a href="schedule.php"
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)] after:transition
                  <?= isActive('schedule.php', $current) ?>">
                    Schedule
                </a>
                <a href="contact.php"
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)] after:transition
                  <?= isActive('contact.php', $current) ?>">
                    Contact
                </a>

                <!-- Search -->
                <div class="relative group">
                    <input type="text" placeholder="Search movies…"
                           class="w-56 lg:w-64 pr-10 pl-4 py-2 rounded-full bg-white/90 text-black placeholder-black/60 border border-white/20 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)] focus:border-transparent shadow" />
                    <i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-black/60 group-focus-within:text-[var(--secondary)]"></i>
                </div>

                <!-- Auth -->
                <div class="flex items-center gap-2 ml-2">
                    <a href="login.php"
                       class="px-4 py-2 rounded-full border border-white/30 text-white hover:bg-white hover:text-black transition">
                        Login
                    </a>
                    <a href="signup.php"
                       class="px-4 py-2 rounded-full bg-[var(--secondary)] text-black font-semibold border border-[var(--secondary)]/60 hover:shadow-[0_0_18px_var(--secondary)] transition">
                        Sign Up
                    </a>
                </div>
            </nav>

            <!-- Mobile toggle -->
            <button id="mobile-menu-button" class="md:hidden text-white text-2xl" aria-label="Open menu" aria-expanded="false">
                <i class="pi pi-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
             class="md:hidden max-h-0 overflow-hidden transition-[max-height] duration-300 ease-in-out bg-black/85 border-t border-white/10">
            <div class="px-6 py-4 space-y-4">
                <a href="index.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-home"></i> Home</a>
                <a href="movies-list.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-ticket"></i> Movies</a>
                <a href="schedule.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-calendar"></i> Schedule</a>
                <a href="contact.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-phone"></i> Contact</a>

                <div class="relative group pt-2">
                    <input type="text" placeholder="Search movies…"
                           class="w-full pr-10 pl-4 py-2 rounded-full bg-white/90 text-black placeholder-black/60 border border-white/20 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)] focus:border-transparent shadow" />
                    <i class="pi pi-search absolute right-3 top-1/2 -translate-y-1/2 text-black/60 group-focus-within:text-[var(--secondary)]"></i>
                </div>

                <div class="flex gap-2 pt-2">
                    <a href="login.php" class="flex-1 px-4 py-2 rounded-full border border-white/30 text-white text-center hover:bg-white hover:text-black transition">Login</a>
                    <a href="signup.php" class="flex-1 px-4 py-2 rounded-full bg-[var(--secondary)] text-black font-semibold text-center border border-[var(--secondary)]/60 hover:shadow-[0_0_18px_var(--secondary)] transition">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu JS -->
    <script>
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        function toggleMenu() {
            const isOpen = mobileMenu.style.maxHeight && mobileMenu.style.maxHeight !== '0px';
            mobileMenu.style.maxHeight = isOpen ? '0px' : mobileMenu.scrollHeight + 'px';
            menuButton.setAttribute('aria-expanded', String(!isOpen));
        }

        menuButton.addEventListener('click', toggleMenu);

        // Close menu on route change (optional improvement if using Turbolinks/partial reloads)
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                mobileMenu.style.maxHeight = '0px';
                menuButton.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</header>
