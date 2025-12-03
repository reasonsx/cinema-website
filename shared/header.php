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

// Load contact data from content blocks
require_once __DIR__ . '/../admin_dashboard/views/content_blocks/content_blocks_functions.php';
$blocks = getContentBlocks($db);

$contact = [];
foreach ($blocks as $block) {
    $contact[$block['tag']] = $block['text'];
}
?>

<header class="sticky top-0 z-50">

    <!-- Contact / Address Bar -->
    <div class="bg-black text-white/80 text-sm border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-2 flex justify-between items-center">

            <!-- Address -->
            <div class="flex items-center gap-2">
                <i class="pi pi-map-marker text-[var(--secondary)]"></i>
                <span><?= $contact['contact_address'] ?? 'Address unavailable' ?></span>
            </div>

            <!-- Contact -->
            <div class="flex items-center gap-4">

                <!-- Phone -->
                <div class="flex items-center gap-1">
                    <i class="pi pi-phone text-[var(--secondary)]"></i>
                    <span><?= $contact['contact_phone'] ?? 'Phone unavailable' ?></span>
                </div>

                <!-- Email -->
                <div class="hidden md:flex items-center gap-1">
                    <i class="pi pi-envelope text-[var(--secondary)]"></i>
                    <span><?= $contact['contact_email'] ?? 'Email unavailable' ?></span>
                </div>

            </div>

        </div>
    </div>

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
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px]
                          after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)]
                          after:transition <?= isActive('movies_list.php', $current) ?>">
                    WHAT'S ON
                </a>

                <!-- NEWS LINK -->
                <a href="/cinema-website/views/news_list/news_list.php"
                   class="relative pb-0.5 transition after:absolute after:left-0 after:-bottom-0.5 after:h-[2px]
                          after:w-full after:origin-left after:scale-x-0 after:bg-[var(--secondary)]
                          after:transition <?= isActive('news_list.php', $current) ?>">
                    NEWS
                </a>

                <!-- Auth -->
                <div class="flex items-center gap-2 ml-2">
                    <?php if ($userName): ?>
                        <a href="/cinema-website/views/profile/profile.php"
                           class="btn">
                            <i class="pi pi-user"></i> <?= $userName ?>
                        </a>
                    <?php else: ?>
                        <a href="/cinema-website/auth/login.php"
                           class="btn-white">
                            Login
                        </a>
                        <a href="/cinema-website/auth/signup.php"
                           class="btn-full">
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

                <a href="/cinema-website/index.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]">
                    <i class="pi pi-home"></i> Home
                </a>

                <a href="/cinema-website/movies_list.php" class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]">
                    <i class="pi pi-ticket"></i> Movies
                </a>

                <!-- NEWS LINK -->
                <a href="/cinema-website/views/news_list/news_list.php"
                   class="flex items-center gap-2 text-white/90 hover:text-[var(--secondary)]">
                    <i class="pi pi-megaphone"></i> News
                </a>

                <div class="flex gap-2 pt-2">
                    <?php if ($userName): ?>
                        <a href="/cinema-website/views/profile/profile.php"
                           class="btn">
                            <i class="pi pi-user"></i> <?= $userName ?>
                        </a>
                    <?php else: ?>
                        <a href="/cinema-website/auth/login.php"
                           class="btn-white">
                            Login
                        </a>
                        <a href="/cinema-website/auth/signup.php"
                           class="btn-full">
                            Sign Up
                        </a>
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
