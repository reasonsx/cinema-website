<?php require_once __DIR__ . '/csrf.php'; ?>

<footer class="bg-transparent text-white pt-12">
    <div class="mt-10 bg-dark text-gray-400 text-center py-4 text-sm">
        <p>&copy; <?= date("Y") ?> Cinema Eclipse. All rights reserved.</p>
    </div>
</footer>

<script>
    document.querySelectorAll("form").forEach(form => {
        if (!form.querySelector("input[name='csrf_token']")) {

            const token = "<?= generateCsrfToken() ?>";

            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "csrf_token";
            input.value = token;

            form.appendChild(input);
        }
    });
</script>
