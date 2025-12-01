<?php
require_once __DIR__ . '/../../../admin_dashboard/views/content_blocks/content_blocks_functions.php';

// Fetch About Us block
$blocks = getContentBlocks($db);
$about = [];
foreach ($blocks as $block) {
    $about[$block['tag']] = $block['text'];
}
?>

<section id="about-us" class="py-16 bg-black text-[#fcb885]">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 px-6">

        <!-- Image -->
        <img src="images/cinema-about.png" alt="Our Cinema" class="w-full md:w-1/2">

        <!-- Text -->
        <div class="md:w-1/2">
            <h2 class="text-5xl font-[Limelight] uppercase mb-6">Our Cinema</h2>

            <div class="leading-relaxed text-lg space-y-4">
                <?= $about['about_us_text'] ?? 'About us content coming soon...' ?>
            </div>
        </div>

    </div>
</section>
