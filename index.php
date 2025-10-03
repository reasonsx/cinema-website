<?php
// index.php - Cinema Website Starter with Brand Colors
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Website</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- PrimeIcons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/primeicons/primeicons.css" />

    <!-- Custom Colors -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light text-black font-sans">

<!-- Header -->
<header class="bg-black text-white shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6 py-4">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="pi pi-video text-primary"></i> MyCinema
        </h1>
        <nav class="flex gap-6">
            <a href="#" class="hover:text-secondary flex items-center gap-1">
                <i class="pi pi-home"></i> Home
            </a>
            <a href="#" class="hover:text-secondary flex items-center gap-1">
                <i class="pi pi-ticket"></i> Movies
            </a>
            <a href="#" class="hover:text-secondary flex items-center gap-1">
                <i class="pi pi-calendar"></i> Schedule
            </a>
            <a href="#" class="hover:text-secondary flex items-center gap-1">
                <i class="pi pi-phone"></i> Contact
            </a>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="relative bg-secondary text-black text-center py-20">
    <h2 class="text-4xl font-bold">Welcome to MyCinema</h2>
    <p class="mt-4 text-lg">Book your tickets and enjoy the latest blockbusters.</p>
    <a href="#movies" class="mt-6 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-secondary transition">
        <i class="pi pi-ticket mr-2"></i> Book Now
    </a>
</section>

<!-- Movies Section -->
<section id="movies" class="container mx-auto px-6 py-12">
    <h3 class="text-3xl font-bold mb-6 flex items-center gap-2 text-primary">
        <i class="pi pi-video"></i> Now Showing
    </h3>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Movie Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="https://via.placeholder.com/400x250" alt="Movie Poster" class="w-full h-56 object-cover">
            <div class="p-4">
                <h4 class="text-xl font-semibold mb-2">Movie Title</h4>
                <p class="text-black text-sm mb-4">Short description of the movie...</p>
                <a href="#" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-md font-medium hover:bg-secondary transition">
                    <i class="pi pi-play"></i> Watch Trailer
                </a>
            </div>
        </div>
        <!-- Duplicate movie cards as needed -->
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?>

</body>
</html>
