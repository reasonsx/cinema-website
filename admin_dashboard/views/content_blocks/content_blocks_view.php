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
renderTable([
    'id'        => 'contentBlocksTable',
    'title'     => 'Content Blocks Management',
    'headers'   => ['ID', 'Tag', 'Title', 'Text'],
    'rows'      => $contentBlocks,   // <-- MUST be loaded in admin_dashboard.php
    'searchable'=> true,

    // ---------- Table Row Renderer ----------
    'renderRow' => function ($block) {
        return [
            $block['id'],
            '<strong class="text-gray-900">' . e($block['tag']) . '</strong>',
            e($block['title']),
            formatBlockText($block['text'])
        ];
    },

    // ---------- Row Action Buttons ----------
    'actions' => function ($block) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $block['id'] ?>)"
                    class="flex items-center justify-center gap-2
                           px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold
                           hover:bg-blue-700 transition">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this content block?')"
                  class="flex items-center p-0 m-0 leading-none">
                <input type="hidden" name="delete_block_id" value="<?= $block['id'] ?>">

                <button type="submit" name="delete_block"
                        class="flex items-center justify-center gap-2
                               px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-semibold
                               hover:bg-red-600 transition">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },

    // ---------- Edit Row Renderer ----------
    'renderEditRow' => function ($block) {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 gap-4">

            <input type="hidden" name="block_id" value="<?= $block['id'] ?>">

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Tag (unique)</label>
                <input type="text" name="tag"
                       value="<?= e($block['tag']) ?>"
                       class="input-edit" required>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Title (optional)</label>
                <input type="text" name="title"
                       value="<?= e($block['title']) ?>"
                       class="input-edit">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Text</label>
                <textarea name="text" rows="5"
                          class="input-edit-textarea"
                          required><?= e($block['text']) ?></textarea>
            </div>

            <div class="flex gap-4 mt-2">
                <button type="submit" name="edit_block"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    <i class="pi pi-check"></i> Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $block['id'] ?>)"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    },

    // ---------- Add New Block Form ----------
    'addLabel' => 'Add Content Block',
    'addForm'  => (function () {
        ob_start(); ?>
        <form method="post" class="grid grid-cols-1 gap-4">
            <input type="hidden" name="add_block" value="1">

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Tag (unique)</label>
                <input type="text" name="tag" class="input-edit" required>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Title (optional)</label>
                <input type="text" name="title" class="input-edit">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm text-gray-600 font-medium">Text</label>
                <textarea name="text" rows="5"
                          class="input-edit-textarea"
                          required></textarea>
            </div>

            <div class="flex gap-4 mt-2">
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-[var(--primary)] text-white text-sm font-semibold
                               hover:bg-[var(--secondary)] transition">
                    <i class="pi pi-plus"></i> Add Block
                </button>

                <button type="button"
                        onclick="toggleAddForm_contentBlocksTable()"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                               bg-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-400 transition">
                    <i class="pi pi-times"></i> Cancel
                </button>
            </div>

        </form>
        <?php return ob_get_clean();
    })(),
]);
?>
