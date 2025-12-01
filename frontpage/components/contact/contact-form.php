<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../admin_dashboard/views/content_blocks/content_blocks_functions.php';

// Fetch all contact-related blocks
$blocks = getContentBlocks($db);

// Convert to associative array for easy access
$contact = [];
foreach ($blocks as $block) {
    $contact[$block['tag']] = $block['text'];
}
?>


<section id="contact-us" class="py-20 bg-black text-white">
    <div class="mx-auto max-w-6xl px-6">

        <!-- Header -->
        <div class="text-center mb-14">
            <h2 class="text-5xl font-[Limelight] tracking-wide text-[var(--secondary)]">CONTACT US</h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="h-[2px] w-16 bg-white/15"></span>
                <i class="pi pi-star text-[var(--secondary)]"></i>
                <span class="h-[2px] w-16 bg-white/15"></span>
            </div>
        </div>

        <!-- Status message -->
        <?php if (!empty($_SESSION['contact_status'])): ?>
            <?php
            $type = $_SESSION['contact_status']['type'];
            $msg  = $_SESSION['contact_status']['msg'];
            unset($_SESSION['contact_status']);
            ?>
            <div class="mb-6 p-4 rounded-xl border
                <?= $type === 'success'
                ? 'bg-green-500/20 text-green-300 border-green-500/30'
                : 'bg-red-500/20 text-red-300 border-red-500/30' ?>">
                <i class="pi <?= $type === 'success' ? 'pi-check-circle' : 'pi-times-circle' ?> mr-2"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- GRID: form + info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            <!-- CONTACT FORM BOX -->
            <form
                    action="/cinema-website/frontpage/components/contact/contact-submit.php"
                    method="post"
                    class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl px-8 py-10
                       flex flex-col gap-6 text-left text-white"
            >
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                <!-- Name -->
                <div>
                    <label class="block text-sm uppercase tracking-wide text-white/60 mb-2">Your Name</label>
                    <input
                            type="text" name="name" required
                            placeholder="Enter your name"
                            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3
                               placeholder-white/40 focus:ring-2 focus:ring-[var(--secondary)]"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm uppercase tracking-wide text-white/60 mb-2">Email</label>
                    <input
                            type="email" name="email" required
                            placeholder="Enter your email"
                            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3
                               placeholder-white/40 focus:ring-2 focus:ring-[var(--secondary)]"
                    >
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm uppercase tracking-wide text-white/60 mb-2">Subject</label>
                    <input
                            type="text" name="subject" required
                            placeholder="Enter subject"
                            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3
                               placeholder-white/40 focus:ring-2 focus:ring-[var(--secondary)]"
                    >
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm uppercase tracking-wide text-white/60 mb-2">Message</label>
                    <textarea
                            name="message" rows="5" required
                            placeholder="Type your message here..."
                            class="w-full rounded-2xl bg-white/5 border border-white/15 px-5 py-3
                               placeholder-white/40 focus:ring-2 focus:ring-[var(--secondary)]"
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="text-center pt-3">
                    <button type="submit" class="btn">
                        <i class="pi pi-send"></i>
                        SEND MESSAGE
                    </button>
                </div>
            </form>

            <!-- CONTACT DETAILS BOX -->
            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl px-10 py-12
                        flex flex-col gap-12">

                <!-- Contact Methods Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 text-center">

                    <!-- Address -->
<div class="flex flex-col items-center gap-2">
    <i class="pi pi-map-marker text-3xl text-[var(--secondary)]"></i>
    <p class="font-semibold text-white"><?= $contact['contact_address'] ? 'Visit Us' : '' ?></p>
    <p class="text-white/80"><?= $contact['contact_address'] ?? 'N/A' ?></p>
</div>

<!-- Phone -->
<div class="flex flex-col items-center gap-2">
    <i class="pi pi-phone text-3xl text-[var(--secondary)]"></i>
    <p class="font-semibold text-white"><?= $contact['contact_phone'] ? 'Call Us' : '' ?></p>
    <p class="text-white/80"><?= $contact['contact_phone'] ?? 'N/A' ?></p>
</div>

<!-- Email -->
<div class="flex flex-col items-center gap-2">
    <i class="pi pi-envelope text-3xl text-[var(--secondary)]"></i>
    <p class="font-semibold text-white"><?= $contact['contact_email'] ? 'Email' : '' ?></p>
    <p class="text-white/80"><?= $contact['contact_email'] ?? 'N/A' ?></p>
</div>

<!-- Opening Hours -->
<div class="text-center">
    <h4 class="text-2xl font-[Limelight] tracking-wide text-[var(--secondary)] mb-4">
        <?= $contact['contact_hours'] ? 'OPENING HOURS' : '' ?>
    </h4>
    <div class="text-white/80 leading-relaxed">
        <?= $contact['contact_hours'] ?? 'N/A' ?>
    </div>
</div>


            </div>

        </div> <!-- END GRID -->

    </div>
</section>


