<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Generate CSRF once per session
// TODO idk we need to think about this and IP for messages

// Ensure CSRF exists
if (empty($_SESSION['csrf'])) {
    try {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
?>

<form
    action="contact-submit.php"
    method="post"
    class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl px-8 py-10 flex flex-col gap-6 text-left text-white"
>
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

    <!-- Name -->
    <div>
        <label for="name" class="block text-sm uppercase tracking-wide text-white/60 mb-2">Your Name</label>
        <input
            type="text"
            id="name"
            name="name"
            required
            placeholder="Enter your name"
            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]"
        >
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm uppercase tracking-wide text-white/60 mb-2">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            required
            placeholder="Enter your email"
            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]"
        >
    </div>

    <!-- Subject -->
    <div>
        <label for="subject" class="block text-sm uppercase tracking-wide text-white/60 mb-2">Subject</label>
        <input
            type="text"
            id="subject"
            name="subject"
            required
            placeholder="Enter subject"
            class="w-full rounded-full bg-white/5 border border-white/15 px-5 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]"
        >
    </div>

    <!-- Message -->
    <div>
        <label for="message" class="block text-sm uppercase tracking-wide text-white/60 mb-2">Message</label>
        <textarea
            id="message"
            name="message"
            rows="5"
            required
            placeholder="Type your message here..."
            class="w-full rounded-2xl bg-white/5 border border-white/15 px-5 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-[var(--secondary)]"
        ></textarea>
    </div>

    <!-- Submit Button -->
    <div class="text-center pt-3">
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--secondary)] px-6 py-3 text-sm font-semibold text-black border border-[var(--secondary)]/60"
        >
            <i class="pi pi-send"></i>
            SEND MESSAGE
        </button>
    </div>
</form>
