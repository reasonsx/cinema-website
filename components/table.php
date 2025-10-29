<?php
/**
 * Reusable Table Component
 *
 * Example usage:
 * renderTable([
 *   'title' => 'All Movies',
 *   'headers' => ['#', 'Title', 'Year', 'Rating', 'Actions'],
 *   'rows' => $movies,
 *   'renderRow' => function($movie) {
 *       return [
 *           $movie['id'],
 *           htmlspecialchars($movie['title']),
 *           htmlspecialchars($movie['release_year']),
 *           htmlspecialchars($movie['rating']),
 *           '<button class="btn-edit">Edit</button>'
 *       ];
 *   },
 *   'emptyText' => 'No movies available',
 *   'searchable' => true,
 *   'id' => 'moviesTable'
 * ]);
 */

if (!function_exists('renderTable')) {
    function renderTable(array $options)
    {
        $id = $options['id'] ?? 'table_' . uniqid();
        $title = $options['title'] ?? '';
        $headers = $options['headers'] ?? [];
        $rows = $options['rows'] ?? [];
        $renderRow = $options['renderRow'] ?? null;
        $emptyText = $options['emptyText'] ?? 'No data available.';
        $searchable = $options['searchable'] ?? false;
        $highlight = $options['highlight'] ?? false;
        $compact = $options['compact'] ?? false;
        ?>

        <section class="flex flex-col gap-4">
            <?php if ($title): ?>
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <h2 class="text-3xl font-[Limelight] text-[var(--primary)]">
                        <?= htmlspecialchars($title) ?>
                    </h2>

                    <?php if ($searchable): ?>
                        <div class="relative">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--secondary)] opacity-70"></i>
                            <input
                                    type="text"
                                    id="<?= $id ?>_search"
                                    placeholder="Search..."
                                    class="pl-9 pr-3 py-2 rounded-lg border border-gray-300 bg-white/90 text-gray-700
                                       focus:ring-2 focus:ring-[var(--secondary)] focus:outline-none w-64 md:w-80 transition"
                            >
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($rows)): ?>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-gray-600 text-center shadow-sm">
                    <?= htmlspecialchars($emptyText) ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                    <table id="<?= $id ?>" class="min-w-full <?= $compact ? 'text-xs' : 'text-sm' ?>">
                        <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase font-semibold">
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th class="px-5 py-3 text-left"><?= htmlspecialchars($header) ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <?php foreach ($rows as $row): ?>
                            <?php $cols = $renderRow ? $renderRow($row) : $row; ?>
                            <tr class="<?= $highlight ? 'hover:bg-[var(--secondary)]/10' : 'hover:bg-gray-50' ?> transition">
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

        <?php if ($searchable): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.getElementById('<?= $id ?>_search');
                const table = document.getElementById('<?= $id ?>');
                input?.addEventListener('input', () => {
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
