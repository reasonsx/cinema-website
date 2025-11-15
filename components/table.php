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
        $id         = $options['id']         ?? 'table_' . uniqid();
        $title      = $options['title']      ?? '';
        $headers    = $options['headers']    ?? [];
        $rows       = $options['rows']       ?? [];
        $renderRow  = $options['renderRow']  ?? null;

        // NEW: OPTIONAL INLINE EDIT ROW
        $renderEditRow = $options['renderEditRow'] ?? null;

        $emptyText  = $options['emptyText']  ?? 'No data available.';
        $searchable = $options['searchable'] ?? false;
        $compact    = $options['compact']    ?? false;
        ?>

        <section class="flex flex-col gap-4">

            <?php if ($title): ?>
            <h2 class="text-3xl font-[Limelight] text-[var(--primary)]"><?= htmlspecialchars($title) ?></h2>
            <?php endif; ?>

            <?php if ($searchable): ?>
                <input type="text" id="<?= $id ?>_search"
                       placeholder="Search..."
                       class="px-3 py-2 border rounded w-64">
            <?php endif; ?>

            <?php if (empty($rows)): ?>
                <div class="p-6 border bg-gray-50 text-gray-600 text-center rounded">
                    <?= htmlspecialchars($emptyText) ?>
                </div>
            <?php else: ?>

            <div class="overflow-x-auto rounded-lg border bg-white shadow">
                <table id="<?= $id ?>" class="min-w-full <?= $compact ? 'text-xs' : 'text-sm' ?>">
                    <thead class="bg-gray-100 text-gray-700 uppercase font-semibold">
                    <tr>
                        <?php foreach ($headers as $header): ?>
                            <th class="px-5 py-3 text-left"><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                    <?php foreach ($rows as $row): ?>
                        <?php $cols = $renderRow ? $renderRow($row) : $row; ?>

                        <!-- MAIN ROW -->
                        <tr class="hover:bg-gray-50" id="row-<?= $row['id'] ?>">
                            <?php foreach ($cols as $col): ?>
                                <td class="px-5 py-3"><?= $col ?></td>
                            <?php endforeach; ?>

                            <td class="px-5 py-3 flex gap-2">

                                <button onclick="toggleEditRow(<?= $row['id'] ?>)"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm h-fit">
                                    Edit
                                </button>

                                <form method="post" onsubmit="return confirm('Delete this item?')">
                                    <input type="hidden" name="delete_actor_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_actor"
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm h-fit">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>

                        <!-- INLINE EDIT ROW (HIDDEN) -->
                        <?php if ($renderEditRow): ?>
                            <tr id="edit-row-<?= $row['id'] ?>" class="hidden bg-gray-50">
                                <td colspan="<?= count($headers) + 1 ?>" class="p-6">
                                    <?= $renderEditRow($row) ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </section>

        <?php if ($searchable): ?>
            <script>
                document.getElementById("<?= $id ?>_search").addEventListener("input", function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll("#<?= $id ?> tbody tr").forEach(row => {
                        row.style.display = row.innerText.toLowerCase().includes(q) ? "" : "none";
                    });
                });
            </script>
        <?php endif; ?>

        <script>
            function toggleEditRow(id) {
                const row = document.getElementById("edit-row-" + id);
                row.classList.toggle("hidden");
                if (!row.classList.contains("hidden")) {
                    row.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            }
        </script>

    <?php
    }
}
?>
