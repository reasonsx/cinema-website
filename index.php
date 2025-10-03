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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Limelight&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body class="bg-light text-black font-sans">

<!-- Header -->
<?php include 'header.php'; ?>


<!-- Hero Section -->
<section class="relative bg-secondary text-black text-center py-20">
    <h2 class="text-4xl font-bold">Welcome to MyCinema</h2>
    <p class="mt-4 text-lg">Book your tickets and enjoy the latest blockbusters.</p>
    <a href="#movies" class="btn">
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
                <a href="#" class="btn">
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
