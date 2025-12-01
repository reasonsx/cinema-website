<?php
// No session_start() here; must be called in main page
$current = basename($_SERVER['PHP_SELF']);

if (!function_exists('isActive')) {
    function isActive($page, $current) {
        return $current === $page ? 'text-[var(--secondary)] after:scale-x-100' : 'text-white/90 hover:text-[var(--secondary)]';
    }
}

// Default user variables
$userName = null;

// If logged in, fetch user first name
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../backend/connection.php';
    $stmt = $db->prepare("SELECT firstname FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && isset($user['firstname'])) {
        $userName = $user['firstname'];
    }
}
?>


<header class="sticky top-0 z-50">

    <!-- Main Navigation -->
    <div class="bg-black/70 backdrop-blur supports-[backdrop-filter]:backdrop-blur-md border-b border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
            <!-- Brand -->
            <a href="/cinema-website/index.php" class="flex items-center gap-2">
                <span class="text-xl md:text-2xl tracking-wide text-white font-[Limelight]">Cinema Eclipse</span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="/cinema-website/views/movies_list/movies_list.php"
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px] after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)] after:transition
                  <?= isActive('movies_list.php', $current) ?>">
                    WHAT'S ON
                </a>

                <!-- Auth -->
                <div class="flex items-center gap-2 ml-2">
                    <?php if ($userName): ?>
                        <a href="/cinema-website/views/profile/profile.php"
                           class="px-4 py-2 rounded-full border border-white/30 text-white hover:bg-white hover:text-black transition flex items-center gap-2">
                            <i class="pi pi-user"></i> <?= $userName ?>
                        </a>
                    <?php else: ?>
                        <a href="/cinema-website/auth/login.php"
                           class="px-4 py-2 rounded-full border border-white/30 text-white hover:bg-white hover:text-black transition">
                            Login
                        </a>
                        <a href="/cinema-website/auth/signup.php"
                           class="px-4 py-2 rounded-full bg-[var(--secondary)] text-black font-semibold border border-[var(--secondary)]/60">
                            Sign Up
                        </a>
                    <?php endif; ?>
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
                <a href="/cinema-website/index.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-home"></i> Home</a>
                <a href="/cinema-website/movies_list.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]"><i class="pi pi-ticket"></i> Movies</a>

                <div class="flex gap-2 pt-2">
                    <?php if ($userName): ?>
                        <a href="/cinema-website/views/profile/profile.php" class="flex-1 px-4 py-2 rounded-full border border-white/30 text-white text-center hover:bg-white hover:text-black transition"><i class="pi pi-user"></i> <?= $userName ?></a>
                    <?php else: ?>
                        <a href="/cinema-website/auth/login.php" class="flex-1 px-4 py-2 rounded-full border border-white/30 text-white text-center hover:bg-white hover:text-black transition">Login</a>
                        <a href="/cinema-website/auth/signup.php" class="flex-1 px-4 py-2 rounded-full bg-[var(--secondary)] text-black font-semibold text-center border border-[var(--secondary)]/60 hover:shadow-[0_0_18px_var(--secondary)] transition">Sign Up</a>
                    <?php endif; ?>
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

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                mobileMenu.style.maxHeight = '0px';
                menuButton.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</header>
