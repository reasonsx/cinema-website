<?php

// Only define once
if (!function_exists('renderTable')) {

    /**
     * Reusable Admin Table Component
     *
     * Accepts:
     * - id (string)                Unique table ID
     * - title (string)             Table header title
     * - headers (array)            Visible column headers
     * - rows (array)               Raw row data
     * - renderRow (callable)       Row-cell renderer
     * - actions (callable)         Buttons column
     * - renderEditRow (callable)   Inline expand editor
     * - addForm (HTML string)      Add-entry form
     * - search (bool)              Search box toggle
     * - compact (bool)             Small font mode
     */
    function renderTable(array $options)
    {
        // Extract options
        $id            = $options['id']            ?? 'table_' . uniqid();
        $title         = $options['title']         ?? '';
        $headers       = $options['headers']       ?? [];
        $rows          = $options['rows']          ?? [];
        $renderRow     = $options['renderRow']     ?? null;
        $renderEditRow = $options['renderEditRow'] ?? null;
        $actions       = $options['actions']       ?? null;
        $addLabel      = $options['addLabel']      ?? 'Add New';
        $addForm       = $options['addForm']       ?? null;
        $emptyText     = $options['emptyText']     ?? 'No data available.';
        $searchable    = $options['searchable']    ?? false;
        $compact       = $options['compact']       ?? false;
        ?>

        <section class="flex flex-col gap-4">

            <!-- ---------- TITLE + ADD BUTTON ---------- -->
            <div class="flex items-center justify-between">
                <?php if ($title): ?>
                    <h2 class="text-3xl font-bold">
                        <?= e($title) ?>
                    </h2>
                <?php endif; ?>

                <?php if ($addForm): ?>
                    <button onclick="toggleAddForm_<?= $id ?>()"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg
                           bg-green-600 text-white text-sm font-semibold
                           hover:bg-green-700 transition">
                        <i class="pi pi-plus"></i> <?= e($addLabel) ?>
                    </button>
                <?php endif; ?>
            </div>

            <!-- ---------- ADD FORM ---------- -->
            <?php if ($addForm): ?>
                <div id="add-form-<?= $id ?>" class="hidden p-6 bg-gray-50 border rounded-lg shadow-inner">
                    <?= $addForm ?>
                </div>
            <?php endif; ?>

            <!-- ---------- SEARCH BAR ---------- -->
            <?php if ($searchable): ?>
                <input id="<?= $id ?>_search"
                       type="text"
                       placeholder="Search..."
                       class="px-3 py-2 border rounded-md w-64" />
            <?php endif; ?>

            <!-- ---------- EMPTY STATE ---------- -->
            <?php if (empty($rows)): ?>
                <div class="p-6 border bg-gray-50 text-gray-600 text-center rounded">
                    <?= e($emptyText) ?>
                </div>

            <?php else: ?>

                <!-- ---------- TABLE WRAPPER ---------- -->
                <div class="overflow-x-auto rounded-lg border bg-white shadow">

                    <table id="<?= $id ?>" class="min-w-full <?= $compact ? 'text-xs' : 'text-sm' ?>">

                        <thead class="bg-gray-100 text-gray-700 uppercase font-semibold">
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th class="px-5 py-3 text-left"><?= e($header) ?></th>
                            <?php endforeach; ?>

                            <!-- Actions column -->
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

                                <!-- Actions -->
                                <td class="px-5 py-3 flex items-center gap-2">
                                    <?= $actions ? $actions($row) : '' ?>
                                </td>
                            </tr>

                            <!-- INLINE EDIT FORM -->
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

        <!-- ---------- SEARCH SCRIPT ---------- -->
        <?php if ($searchable): ?>
        <script>
            document.getElementById("<?= $id ?>_search")?.addEventListener("input", e => {
                const q = e.target.value.toLowerCase();
                const rows = document.querySelectorAll("#<?= $id ?> tbody tr:not(.no-results-row)");
                let visibleCount = 0;

                rows.forEach(row => {
                    const match = row.innerText.toLowerCase().includes(q);
                    row.style.display = match ? "" : "none";
                    if (match) visibleCount++;
                });

                let noRow = document.querySelector("#<?= $id ?> tbody .no-results-row");

                if (visibleCount === 0) {
                    if (!noRow) {
                        const tbody = document.querySelector("#<?= $id ?> tbody");
                        noRow = document.createElement("tr");
                        noRow.className = "no-results-row";
                        noRow.innerHTML = `
                <td colspan="<?= count($headers) + 1 ?>"
                    class="text-center py-6 text-gray-500 italic">
                    No results found
                </td>`;
                        tbody.appendChild(noRow);
                    }
                } else {
                    noRow?.remove();
                }
            });
        </script>
    <?php endif; ?>

        <!-- ---------- TOGGLE EDIT ROW ---------- -->
        <script>
            function toggleEditRow(id) {
                const row = document.getElementById("edit-row-" + id);
                if (!row) return;
                row.classList.toggle("hidden");
                if (!row.classList.contains("hidden")) {
                    row.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            }
        </script>

        <!-- ---------- TOGGLE ADD FORM ---------- -->
        <script>
            function toggleAddForm_<?= $id ?>() {
                const form = document.getElementById("add-form-<?= $id ?>");
                if (!form) return;
                form.classList.toggle("hidden");
                if (!form.classList.contains("hidden")) {
                    form.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            }
        </script>

        <?php
    } // end function
} // end if not exists
?>
