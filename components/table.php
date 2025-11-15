<?php
if (!function_exists('renderTable')) {
    function renderTable(array $options)
    {
        $id             = $options['id']         ?? 'table_' . uniqid();
        $title          = $options['title']      ?? '';
        $headers        = $options['headers']    ?? [];
        $rows           = $options['rows']       ?? [];
        $renderRow      = $options['renderRow']  ?? null;
        $renderEditRow  = $options['renderEditRow'] ?? null;
        $actions        = $options['actions']    ?? null;     // ⭐ NEW REUSABLE ACTIONS SLOT

        $emptyText      = $options['emptyText']  ?? 'No data available.';
        $searchable     = $options['searchable'] ?? false;
        $compact        = $options['compact']    ?? false;
        ?>
        <section class="flex flex-col gap-4">

            <?php if ($title): ?>
                <h2 class="text-3xl font-[Limelight] text-[var(--primary)]"><?= htmlspecialchars($title) ?></h2>
            <?php endif; ?>

            <?php if ($searchable): ?>
                <input id="<?= $id ?>_search"
                       type="text"
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

                                <!-- ⭐ DYNAMIC ACTION BUTTONS -->
                                <td class="px-5 py-3 flex items-center gap-2">
                                    <?= $actions ? $actions($row) : '' ?>
                                </td>
                            </tr>

                            <!-- INLINE EDIT ROW -->
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
            document.getElementById("<?= $id ?>_search").addEventListener("input", e => {
                const q = e.target.value.toLowerCase();
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
