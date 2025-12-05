<?php
require_once __DIR__ . '/../../components/table.php';
require_once __DIR__ . '/../../../shared/helpers.php';

// Format date
if (!function_exists('formatNewsDate')) {
    function formatNewsDate(string $dt): string {
        return date('d M Y, H:i', strtotime($dt)); // Example: 27 Jan 2025, 18:11
    }
}
?>

<?php
renderTable([
    'id'        => 'newsTable',
    'title'     => 'News Management',
    'headers'   => ['ID', 'Title', 'Date Added', 'Content'],
    'rows'      => $newsList,
    'searchable'=> true,
    'renderRow' => function ($news) {
        return [
            $news['id'],
            '<strong class="text-gray-900">' . e($news['title']) . '</strong>',
            '<span class="text-sm text-gray-700 whitespace-nowrap">' . e(formatNewsDate($news['date_added'])) . '</span>',
            e(mb_strimwidth($news['content'], 0, 100, '…'))
        ];
    },
    'actions' => function ($news) {
        ob_start(); ?>
        <div class="flex items-center gap-2">

            <button onclick="toggleEditRow(<?= $news['id'] ?>)"
                    class="btn-square bg-blue-600">
                <i class="pi pi-pencil"></i> Edit
            </button>

            <form method="post"
                  onsubmit="return confirm('Delete this news item?')"
                  class="flex items-center p-0 m-0 leading-none">
                <input type="hidden" name="delete_news_id" value="<?= $news['id'] ?>">

                <button type="submit" name="delete_news"
                        class="btn-square bg-red-500">
                    <i class="pi pi-trash"></i> Delete
                </button>
            </form>

        </div>
        <?php return ob_get_clean();
    },
    'renderEditRow' => function ($news) {
        ob_start(); ?>

        <form method="post" class="flex flex-col gap-6">

            <input type="hidden" name="news_id" value="<?= $news['id'] ?>">

            <!-- Title -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Title</label>
                <input type="text"
                       name="title"
                       value="<?= e($news['title']) ?>"
                       class="input-edit px-4 py-2 rounded-md"
                       required>
            </div>

            <!-- Content -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Content</label>
                <textarea name="content"
                          rows="6"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          required><?= e($news['content']) ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        name="edit_news"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-check"></i>
                    Save Changes
                </button>

                <button type="button"
                        onclick="toggleEditRow(<?= $news['id'] ?>)"
                        class="btn-square bg-gray-300 text-gray-700 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-times"></i>
                    Cancel
                </button>
            </div>

        </form>

        <?php return ob_get_clean();
    },

    // Add form
    'addLabel' => 'Add News',
    'addForm'  => (function () {
        ob_start(); ?>

        <form method="post" class="flex flex-col gap-6">
            <input type="hidden" name="add_news" value="1">

            <!-- Title -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Title</label>
                <input type="text"
                       name="title"
                       class="input-edit px-4 py-2 rounded-md"
                       placeholder="Enter news title"
                       required>
            </div>

            <!-- Content -->
            <div class="flex flex-col gap-2">
                <label class="text-sm text-gray-700 font-semibold">Content</label>
                <textarea name="content"
                          rows="6"
                          class="input-edit-textarea px-4 py-3 rounded-md leading-relaxed"
                          placeholder="Write the news content..."
                          required></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                        class="btn-square bg-green-600 flex items-center gap-2 px-4 py-2">
                    <i class="pi pi-plus"></i>
                    Add News
                </button>

                <button type="button"
                        onclick="toggleAddForm_newsTable()"
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
