<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

// Format long text
if (!function_exists('formatBlockText')) {
    function formatBlockText(string $text): string {
        return e(mb_strimwidth($text, 0, 120, '…'));
    }
}
?>

<?php
renderTable(options: [
    'id'        => 'contentBlocksTable',
    'title'     => 'Content Blocks Management',
    'headers'   => ['ID', 'Tag', 'Title', 'Text'],
    'rows'      => $contentBlocks,   // <-- MUST be loaded in admin_dashboard.php
    'searchable'=> true,

    'renderRow' => function ($block) {
        return [
            $block['id'],
            '<strong class="text-gray-900">' . e($block['tag']) . '</strong>',
            e($block['title']),
            formatBlockText($block['text'])
        ];
    },

    // Action buttons
    'actions' => function ($block) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $block['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this content block?')"
                  class="flex items-center p-0 m-0 leading-none">
                <input type="hidden" name="delete_block_id" value="<?= $block['id'] ?>">

                <button type="submit" name="delete_block"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    'renderEditRow' => function ($block) {
        ob_start(); ?>

        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="block_id" value="<?= $block['id'] ?>">

            <!-- Tag -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Tag (unique)</label>
                <input type="text"
                       name="tag"
                       value="<?= e($block['tag']) ?>"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Title (optional) -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">
                    Title <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text"
                       name="title"
                       value="<?= e($block['title']) ?>"
                       class="input-edit px-4 py-2 rounded-md">
            </div>

            <!-- Text -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Text</label>
                <textarea name="text"
                          rows="6"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          required><?= e($block['text']) ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_block"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i>
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $block['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>

        </form>

        <?php return ob_get_clean();
    },

    // Add form
    'addLabel' => 'Add Content Block',
    'addForm'  => (function () {
        ob_start(); ?>

        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="add_block" value="1">

            <!-- Tag -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Tag (unique)</label>
                <input type="text"
                       name="tag"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Unique identifier"
                       required>
            </div>

            <!-- Title (optional) -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">
                    Title <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text"
                       name="title"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Optional title">
            </div>

            <!-- Text -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Text</label>
                <textarea name="text"
                          rows="6"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Write the content block text..."
                          required></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i>
                    Add Content Block
                </button>

                <button type="button"
                        onclick="toggleAddForm_contentBlocksTable()"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>

        </form>

        <?php return ob_get_clean();
    })(),
]);
?>
