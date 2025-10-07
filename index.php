<?php
// index.php - Cinema Website Starter with Brand Colors
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body class="bg-light text-black font-sans">

<!-- Header -->
<?php include 'header.php'; ?>


<!-- Hero Section -->
<section class="relative bg-[var(--primary)] text-black text-center h-[80vh]">
  <div class="container mx-auto flex flex-col items-center justify-start relative h-full gap-6 pt-20">
    <h1 class="text-6xl font-[Limelight] text-[var(--black)]">MYCINEMA</h1>
    <a href="#top-films" class="bg-[var(--black)] text-[var(--white)] px-6 py-2 rounded-full font-[Limelight] hover:bg-[var(--secondary)] transition-colors duration-300">
      TEXT
    </a>
    <img src="images/film-reel.png" alt="Film Reel" class="w-96 md:w-[35rem] lg:w-[45rem] absolute bottom-0">
  </div>
</section>





<!-- Now Playing Section -->
<section id="now-playing" class="bg-[var(--secondary)] py-16">
    <div class="container mx-auto text-center">
        <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-12">NOW PLAYING</h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 justify-items-center">
            <img src="images/dune.jpg" alt="Dune" class="w-40 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300">
            <img src="images/shrek.jpg" alt="Shrek" class="w-40 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300">
            <img src="images/star-wars.jpg" alt="Star Wars" class="w-40 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300">
            <img src="images/harry-potter.jpg" alt="Harry Potter" class="w-40 rounded-lg shadow-lg hover:scale-105 transition-transform duration-300">
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'footer.php'; ?>

</body>
</html>
