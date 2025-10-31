<?php
/**
 * ------------------------------------------------------------------------
 * 🧩 Reusable Table Component
 * ------------------------------------------------------------------------
 * Generates a stylized, responsive, and optionally searchable table.
 * Designed for dashboards, admin panels, and data listings.
 *
 * ✅ Features:
 * - Optional search input (real-time filtering)
 * - Clean and modern Tailwind CSS design
 * - Flexible row rendering using callbacks
 *
 * ------------------------------------------------------------------------
 * 🧱 Example Usage:
 * ------------------------------------------------------------------------
 * renderTable([
 *   'id' => 'moviesTable',
 *   'title' => 'All Movies',
 *   'headers' => ['#', 'Title', 'Year', 'Rating', 'Actions'],
 *   'rows' => $movies,
 *   'renderRow' => fn($movie) => [
 *       $movie['id'],
 *       htmlspecialchars($movie['title']),
 *       htmlspecialchars($movie['release_year']),
 *       htmlspecialchars($movie['rating']),
 *       '<button class="btn-edit">Edit</button>'
 *   ],
 *   'emptyText' => 'No movies available',
 *   'searchable' => true,
 *   'compact' => false,
 * ]);
 * ------------------------------------------------------------------------
 */

if (!function_exists('renderTable')) {
    function renderTable(array $options)
    {
        // 🧾 Configuration & Defaults
        $id         = $options['id']         ?? 'table_' . uniqid();
        $title      = $options['title']      ?? '';
        $headers    = $options['headers']    ?? [];
        $rows       = $options['rows']       ?? [];
        $renderRow  = $options['renderRow']  ?? null;
        $emptyText  = $options['emptyText']  ?? 'No data available.';
        $searchable = $options['searchable'] ?? false;
        $compact    = $options['compact']    ?? false;
        ?>

        <!-- Table Container -->
        <section class="flex flex-col gap-4">

            <!-- Title & Search Bar -->
            <?php if ($title): ?>
                <div class="flex flex-col items-start gap-2">
                    <!-- Table Title -->
                    <h2 class="text-3xl font-[Limelight] text-[var(--primary)]">
                        <?= htmlspecialchars($title) ?>
                    </h2>

                    <!-- Optional Search Input -->
                    <?php if ($searchable): ?>
                        <div class="relative mt-1">
                            <input
                                    type="text"
                                    id="<?= $id ?>_search"
                                    placeholder="Search..."
                                    class="pl-9 pr-3 py-2 !rounded-lg border border-gray-300 bg-white/90 text-gray-700
                                       !focus:outline-none
                                       w-64 md:w-80 transition placeholder-gray-400"
                            >
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


            <!-- Empty State -->
            <?php if (empty($rows)): ?>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-gray-600 text-center shadow-sm">
                    <?= htmlspecialchars($emptyText) ?>
                </div>
            <?php else: ?>

                <!-- Table Structure -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table id="<?= $id ?>" class="min-w-full <?= $compact ? 'text-xs' : 'text-sm' ?>">
                        <!-- Table Header -->
                        <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase font-semibold">
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th class="px-5 py-3 text-left"><?= htmlspecialchars($header) ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-gray-100">
                        <?php foreach ($rows as $row): ?>
                            <?php $cols = $renderRow ? $renderRow($row) : $row; ?>
                            <tr class="hover:bg-gray-50 transition">
                                <?php foreach ($cols as $col): ?>
                                    <td class="px-5 py-3 text-gray-700"><?= $col ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Live Search Script -->
        <?php if ($searchable): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.getElementById('<?= $id ?>_search');
                const table = document.getElementById('<?= $id ?>');
                if (!input || !table) return;

                input.addEventListener('input', () => {
                    const query = input.value.toLowerCase();
                    table.querySelectorAll('tbody tr').forEach(row => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            });
        </script>
    <?php endif; ?>

        <?php
    }
}
?>
