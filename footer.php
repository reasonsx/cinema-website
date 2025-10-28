<!-- footer.php -->
<footer class="bg-black text-white pt-12">
    <div class="container mx-auto grid md:grid-cols-4 gap-8 px-6">

        <!-- About -->
        <div>
            <h4 class="text-lg font-semibold mb-4 text-primary flex items-center gap-2">
                <i class="pi pi-video"></i> Eclipse Cinema
            </h4>
            <p class="text-gray-400 text-sm">
                Experience movies like never before. Book tickets, watch trailers, and enjoy your favorite blockbusters in comfort.
            </p>
        </div>

        <!-- Quick Links -->
        <div>
            <h4 class="text-lg font-semibold mb-4 text-primary">Quick Links</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
                <li><a href="#" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-home"></i> Home</a></li>
                <li><a href="#" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-ticket"></i> Movies</a></li>
                <li><a href="#" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-calendar"></i> Schedule</a></li>
                <li><a href="#" class="hover:text-secondary flex items-center gap-2"><i class="pi pi-phone"></i> Contact</a></li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div>
            <h4 class="text-lg font-semibold mb-4 text-primary">Contact</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
                <li class="flex items-center gap-2"><i class="pi pi-map-marker"></i> 123 Cinema Street, Esbjerg</li>
                <li class="flex items-center gap-2"><i class="pi pi-phone"></i> +45 12 34 56 78</li>
                <li class="flex items-center gap-2"><i class="pi pi-envelope"></i> info@eclipsecinema.com</li>
            </ul>
        </div>

        <!-- Social Media -->
        <div>
            <h4 class="text-lg font-semibold mb-4 text-primary">Follow Us</h4>
            <div class="flex gap-4 text-xl">
                <a href="#" class="hover:text-secondary"><i class="pi pi-facebook"></i></a>
                <a href="#" class="hover:text-secondary"><i class="pi pi-instagram"></i></a>
                <a href="#" class="hover:text-secondary"><i class="pi pi-twitter"></i></a>
                <a href="#" class="hover:text-secondary"><i class="pi pi-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="mt-10 bg-dark text-gray-400 text-center py-4 text-sm">
        <p>&copy; <?php echo date("Y"); ?> MyCinema. All rights reserved.</p>
    </div>
</footer>
