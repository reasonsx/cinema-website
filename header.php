<!-- header.php -->
<header class="bg-black text-white shadow-md">
    <!-- Top Info Bar -->
    <div class="bg-dark text-gray-400 text-sm py-1">
        <div class="container mx-auto flex justify-between items-center px-6">
            <div class="flex gap-4">
                <span class="flex items-center gap-1"><i class="pi pi-clock"></i> Mon-Sun: 10AM - 12AM</span>
                <span class="flex items-center gap-1"><i class="pi pi-envelope"></i> info@mycinema.com</span>
            </div>
            <div class="flex gap-3 text-lg">
                <a href="#" class="hover:text-primary"><i class="pi pi-facebook"></i></a>
                <a href="#" class="hover:text-primary"><i class="pi pi-instagram"></i></a>
                <a href="#" class="hover:text-primary"><i class="pi pi-twitter"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="bg-black">
        <div class="container mx-auto flex justify-between items-center px-6 py-4">

            <!-- Logo / Brand -->
            <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-2 text-primary">
                <i class="pi pi-video text-primary"></i> MyCinema
            </h1>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="index.php" class="hover:text-secondary flex items-center gap-1 transition">Home</a>
                <a href="movies.php" class="hover:text-secondary flex items-center gap-1 transition">Movies</a>
                <a href="schedule.php" class="hover:text-secondary flex items-center gap-1 transition">Schedule</a>
                <a href="contact.php" class="hover:text-secondary flex items-center gap-1 transition">Contact</a>

                <!-- Search Bar -->
                <div>
                    <input type="text" placeholder="Search movies..." class="px-3 py-1 rounded-full text-black focus:outline-none focus:ring-2 focus:ring-primary" />
                </div>

                <!-- Login / Sign Up Buttons -->
                <div class="flex items-center gap-2 ml-4">
                    <a href="login.php" class="px-4 py-1 rounded-full border border-white text-white hover:bg-white hover:text-black transition">Login</a>
                    <a href="signup.php" class="px-4 py-1 rounded-full bg-primary text-white hover:bg-secondary transition">Sign Up</a>
                </div>
            </nav>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white focus:outline-none text-2xl">
                    <i class="pi pi-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-black px-6 pb-4">
            <ul class="flex flex-col gap-4">
                <li><a href="index.php" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-home"></i> Home</a></li>
                <li><a href="movies.php" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-ticket"></i> Movies</a></li>
                <li><a href="schedule.php" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-calendar"></i> Schedule</a></li>
                <li><a href="contact.php" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-phone"></i> Contact</a></li>
                <li>
                    <input type="text" placeholder="Search movies..." class="w-full px-3 py-1 rounded-full text-black focus:outline-none focus:ring-2 focus:ring-primary" />
                </li>
                <!-- Mobile Login / Sign Up Buttons -->
                <li class="flex flex-col gap-2 mt-2">
                    <a href="login.php" class="px-4 py-2 rounded-full border border-white text-white hover:bg-white hover:text-black text-center transition">Login</a>
                    <a href="signup.php" class="px-4 py-2 rounded-full bg-primary text-white hover:bg-secondary text-center transition">Sign Up</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Mobile menu toggle script -->
    <script>
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</header>
