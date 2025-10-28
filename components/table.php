<?php
/**
 * components/table.php
 *
 * Usage:
 * include 'components/table.php';
 * renderTable([
 *   'title' => 'All Movies',
 *   'headers' => ['#', 'Title', 'Year', 'Rating', 'Actions'],
 *   'rows' => $movies, // array of associative arrays
 *   'renderRow' => function($movie) {
 *       return [
 *           $movie['id'],
 *           htmlspecialchars($movie['title']),
 *           htmlspecialchars($movie['release_year']),
 *           htmlspecialchars($movie['rating']),
 *           '<button class="btn-edit">Edit</button>'
 *       ];
 *   }
 * ]);
 */

if (!function_exists('renderTable')) {
    function renderTable(array $options) {
        $title = $options['title'] ?? '';
        $headers = $options['headers'] ?? [];
        $rows = $options['rows'] ?? [];
        $renderRow = $options['renderRow'] ?? null;
        ?>
        <section class="mb-10">
            <?php if ($title): ?>
                <h2 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="pi pi-table text-primary text-xl"></i>
                    <?= htmlspecialchars($title) ?>
                </h2>
            <?php endif; ?>

            <?php if (empty($rows)): ?>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-gray-600 text-center shadow-sm">
                    No data available.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-md">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase text-xs font-semibold">
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th class="px-5 py-3 text-left"><?= htmlspecialchars($header) ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
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
        <?php
    }
}
?>
