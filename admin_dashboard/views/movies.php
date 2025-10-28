<section class="mb-10">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        <i class="pi pi-video text-primary text-xl"></i>
        All Movies
    </h2>

    <!-- Add Movie Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg shadow-sm hover:bg-secondary transition text-lg font-medium">
            <i class="pi pi-plus-circle"></i> Add New Movie
        </summary>
        <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4 mt-4 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="title" placeholder="Title" required
                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                <input type="number" name="release_year" placeholder="Release Year" required
                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                <input type="text" name="rating" placeholder="Rating" required
                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                <input type="number" name="length" placeholder="Length (min)" required
                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>

            <textarea name="description" placeholder="Description" rows="3" required
                      class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none"></textarea>

            <input type="file" name="poster" accept="image/*"
                   class="border border-gray-300 rounded-md px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none">

            <div>
                <label class="font-semibold text-gray-700">Actors</label>
                <div id="actors-container" class="flex flex-wrap gap-2 mt-2">
                    <?php foreach ($allActors as $actor): ?>
                        <div class="actor-btn cursor-pointer px-3 py-1 border border-gray-300 rounded-full hover:bg-primary hover:text-white transition text-sm"
                             data-id="<?= $actor['id'] ?>">
                            <?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Directors</label>
                <div id="directors-container" class="flex flex-wrap gap-2 mt-2">
                    <?php foreach ($allDirectors as $director): ?>
                        <div class="director-btn cursor-pointer px-3 py-1 border border-gray-300 rounded-full hover:bg-primary hover:text-white transition text-sm"
                             data-id="<?= $director['id'] ?>">
                            <?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="add_movie"
                    class="bg-primary text-white px-6 py-2 rounded-md shadow-sm hover:bg-secondary transition text-lg font-medium self-start">
                Add Movie
            </button>
        </form>
    </details>

    <!-- Movies Table -->
    <?php if (empty($movies)): ?>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-gray-600 text-center shadow-sm">
            No movies found.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-md">
            <table class="min-w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Poster</th>
                    <th class="px-5 py-3 text-left">Title</th>
                    <th class="px-5 py-3 text-left">Year</th>
                    <th class="px-5 py-3 text-left">Rating</th>
                    <th class="px-5 py-3 text-left">Length</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">Actors</th>
                    <th class="px-5 py-3 text-left">Directors</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                <?php foreach ($movies as $movie): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-600"><?= $movie['id'] ?></td>
                        <td class="px-5 py-3">
                            <?php if($movie['poster']): ?>
                                <img src="<?= $movie['poster'] ?>" width="60" class="rounded-md shadow-sm">
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($movie['title']) ?></td>
                        <td class="px-5 py-3 text-gray-700"><?= htmlspecialchars($movie['release_year']) ?></td>
                        <td class="px-5 py-3 text-gray-700"><?= htmlspecialchars($movie['rating']) ?></td>
                        <td class="px-5 py-3 text-gray-700"><?= htmlspecialchars($movie['length']) ?> min</td>
                        <td class="px-5 py-3 text-gray-600 italic"><?= htmlspecialchars($movie['description']) ?></td>
                        <td class="px-5 py-3 text-gray-700">
                            <?php
                            $stmt = $db->prepare("SELECT a.first_name, a.last_name FROM actors a JOIN actorAppearIn aa ON a.id=aa.actor_id WHERE aa.movie_id=?");
                            $stmt->execute([$movie['id']]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $a) {
                                echo '<span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded-full mr-1 mb-1">'
                                    . htmlspecialchars($a['first_name'].' '.$a['last_name']) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            <?php
                            $stmt = $db->prepare("SELECT d.first_name, d.last_name FROM directors d JOIN directorDirects dd ON d.id=dd.director_id WHERE dd.movie_id=?");
                            $stmt->execute([$movie['id']]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
                                echo '<span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded-full mr-1 mb-1">'
                                    . htmlspecialchars($d['first_name'].' '.$d['last_name']) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-3">
                                <form method="post" onsubmit="return confirm('Delete this movie?');">
                                    <input type="hidden" name="delete_movie_id" value="<?= $movie['id'] ?>">
                                    <button type="submit"
                                            class="flex items-center gap-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 px-4 py-1.5 text-sm font-medium transition shadow-sm">
                                        <i class="pi pi-trash text-red-600"></i> Delete
                                    </button>
                                </form>

                                <button type="button" onclick="toggleEditForm(<?= $movie['id'] ?>)"
                                        class="flex items-center gap-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 text-sm font-medium transition shadow-sm">
                                    <i class="pi pi-pencil text-gray-600"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Form Row -->
                    <tr id="edit-form-<?= $movie['id'] ?>" class="hidden bg-gray-50">
                        <td colspan="10" class="p-6">
                            <h3 class="text-xl font-semibold text-primary mb-4">Edit Movie</h3>
                            <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required
                                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                <input type="number" name="release_year" value="<?= htmlspecialchars($movie['release_year']) ?>" required
                                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                <input type="text" name="rating" value="<?= htmlspecialchars($movie['rating']) ?>" required
                                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                <input type="number" name="length" value="<?= htmlspecialchars($movie['length']) ?>" required
                                       class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                <textarea name="description" rows="3" required
                                          class="border border-gray-300 rounded-md px-3 py-2 text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none"><?= htmlspecialchars($movie['description']) ?></textarea>
                                <input type="file" name="poster" accept="image/*"
                                       class="border border-gray-300 rounded-md px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none">

                                <div class="flex gap-4 mt-4">
                                    <button type="submit" name="edit_movie"
                                            class="bg-primary text-white px-6 py-2 rounded-md shadow-sm hover:bg-secondary transition text-sm font-medium">
                                        Save Changes
                                    </button>
                                    <button type="button" onclick="toggleEditForm(<?= $movie['id'] ?>)"
                                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md shadow-sm hover:bg-gray-400 transition text-sm font-medium">
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
    <?php endif; ?>
</section>


<script>
function toggleEditForm(movieId) {
    const form = document.getElementById(`edit-form-${movieId}`);
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

document.addEventListener("DOMContentLoaded", () => {
  // Actor buttons
  document.querySelectorAll(".actor-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("bg-[var(--secondary)]");
      btn.classList.toggle("text-white");
      btn.classList.toggle("border-[var(--secondary)]");

      const parentRow = btn.closest("tr[id^='edit-form-']");
      if (!parentRow) return;

      const movieId = parentRow.id.replace("edit-form-", "");
      const actorInput = document.getElementById(`edit-actors-${movieId}`);
      const selectedActors = Array.from(parentRow.querySelectorAll(".actor-btn.bg-\\[var\\(--secondary\\)\\]"))
        .map(b => b.dataset.id);
      actorInput.value = selectedActors.join(",");
    });
  });

  // Director buttons
  document.querySelectorAll(".director-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("bg-[var(--secondary)]");
      btn.classList.toggle("text-white");
      btn.classList.toggle("border-[var(--secondary)]");

      const parentRow = btn.closest("tr[id^='edit-form-']");
      if (!parentRow) return;

      const movieId = parentRow.id.replace("edit-form-", "");
      const directorInput = document.getElementById(`edit-directors-${movieId}`);
      const selectedDirectors = Array.from(parentRow.querySelectorAll(".director-btn.bg-\\[var\\(--secondary\\)\\]"))
        .map(b => b.dataset.id);
      directorInput.value = selectedDirectors.join(",");
    });
  });
});
</script>
