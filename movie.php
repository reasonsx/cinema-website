<?php
// movie.php
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<?php include 'header.php'; ?>

<section class="py-12 px-6 md:px-16 bg-light">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">

        <!-- Movie Poster Placeholder -->
        <div class="flex justify-center">
            <div class="w-full max-w-sm aspect-[2/3] bg-gray-200 rounded-2xl shadow-lg flex items-center justify-center text-gray-500">
                <span class="text-sm">Movie Poster</span>
            </div>
        </div>

        <!-- Movie Info -->
        <div class="md:col-span-2 flex flex-col gap-6">
            <h1 class="text-4xl font-header text-primary">Movie Title</h1>

            <p class="text-gray-700 leading-relaxed">
                Movie description goes here. A short synopsis about the plot, characters, and
                what makes the movie exciting for the audience.
            </p>

            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                <p><strong class="text-black">Release Year:</strong> YYYY</p>
                <p><strong class="text-black">Duration:</strong> 0h 00m</p>
                <p><strong class="text-black">Director:</strong> Director Name</p>
                <p><strong class="text-black">Genre:</strong> Genre List</p>
            </div>

            <!-- Cast -->
            <div>
                <h3 class="text-xl font-header text-secondary mb-2">Cast</h3>
                <p class="text-gray-700">Actor 1, Actor 2, Actor 3, Actor 4</p>
            </div>
        </div>
    </div>
</section>

<!-- Booking Section -->
<section class="py-12 px-6 md:px-16 bg-white">
    <div class="max-w-2xl mx-auto bg-light rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-header text-primary mb-6 text-center">Book Tickets</h2>

        <form action="seats.php" method="GET" class="flex flex-col gap-6">
            <!-- Venue -->
            <div class="flex flex-col">
                <label for="venue" class="text-sm text-gray-600 mb-1">Select Venue</label>
                <select id="venue" name="venue" required
                        class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">-- Choose Venue --</option>
                    <option value="cinema1">Cinema 1</option>
                    <option value="cinema2">Cinema 2</option>
                    <option value="cinema3">Cinema 3</option>
                </select>
            </div>

            <!-- Day -->
            <div class="flex flex-col">
                <label for="day" class="text-sm text-gray-600 mb-1">Select Day</label>
                <input type="date" id="day" name="day" required
                       class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <!-- Time -->
            <div class="flex flex-col">
                <label for="time" class="text-sm text-gray-600 mb-1">Select Time</label>
                <select id="time" name="time" required
                        class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">-- Choose Time --</option>
                    <option value="14:00">2:00 PM</option>
                    <option value="17:00">5:00 PM</option>
                    <option value="20:00">8:00 PM</option>
                </select>
            </div>

            <!-- Hidden Movie ID -->
            <input type="hidden" name="movie_id" value="1"> <!-- Later dynamic -->

            <!-- Submit -->
            <button type="submit" class="btn w-full text-center justify-center items-center">
                <i class="pi pi-ticket"></i> Continue to Seat Selection
            </button>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
