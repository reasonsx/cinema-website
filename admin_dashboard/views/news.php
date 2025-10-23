<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">News Management</h2>

    <!-- Add News Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add News
        </summary>
        <form method="post" class="flex flex-col gap-4 mt-4">
            <input type="text" name="title" placeholder="News Title" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <textarea name="content" placeholder="News Content" rows="5" required
                class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]"></textarea>
            <button type="submit" name="add_news"
                class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                Add News
            </button>
        </form>
    </details>

    <!-- News Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
            <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Date Added</th>
                    <th class="px-4 py-2 text-left">Content</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $news): ?>
                    <tr class="hover:text-black transition-colors duration-300">
                        <td class="px-4 py-2"><?= $news['id'] ?></td>
                        <td class="px-4 py-2 font-bold"><?= htmlspecialchars($news['title']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars(date("Y-m-d H:i", strtotime($news['date_added']))) ?></td>
                        <td class="px-4 py-2"><?= nl2br(htmlspecialchars(substr($news['content'], 0, 100))) ?>...</td>
                        <td class="px-4 py-2">
                            <form method="post" style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to delete this news item?');">
                                <input type="hidden" name="delete_news_id" value="<?= $news['id'] ?>">
                                <button type="submit" name="delete_news"
                                    class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                    Delete
                                </button>
                            </form>
                            <button type="button" onclick="toggleEditNewsForm(<?= $news['id'] ?>)"
                                class="bg-[var(--primary)] text-[var(--white)] px-3 py-1 rounded shadow hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-sm">
                                Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Edit News Row -->
                    <tr id="edit-news-<?= $news['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="5" class="p-6 border-t-4 border-[var(--primary)]">
                            <h3 class="text-3xl font-[Limelight] text-[var(--primary)] mb-4">Edit News</h3>
                            <form method="post" class="flex flex-col gap-4">
                                <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($news['title']) ?>" required
                                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">
                                <textarea name="content" rows="5" required
                                    class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 focus:outline-none focus:border-[var(--secondary)]"><?= htmlspecialchars($news['content']) ?></textarea>
                                <div class="flex gap-4 mt-4">
                                    <button type="submit" name="edit_news"
                                        class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
                                        Save Changes
                                    </button>
                                    <button type="button" onclick="toggleEditNewsForm(<?= $news['id'] ?>)"
                                        class="bg-gray-400 text-white px-6 py-2 rounded-lg shadow-md hover:bg-gray-500 transition-colors duration-300 font-[Limelight] text-lg">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
function toggleEditNewsForm(newsId) {
    const form = document.getElementById(`edit-news-${newsId}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
