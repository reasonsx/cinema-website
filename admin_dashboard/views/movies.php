<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] drop-shadow-md mb-6">All Movies</h2>

    <!-- Add Movie Form -->
    <details class="mb-8">
        <summary class="cursor-pointer inline-block bg-[var(--primary)] text-[var(--white)] px-6 py-3 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg">
            Add New Movie
        </summary>
        <form method="post" enctype="multipart/form-data" class="flex flex-col gap-4 mt-4">
            <input type="text" name="title" placeholder="Title" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="number" name="release_year" placeholder="Release Year" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="text" name="rating" placeholder="Rating" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <input type="number" name="length" placeholder="Length (min)" required
                   class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]">
            <textarea name="description" placeholder="Description" rows="3" required
                      class="border-b-2 border-[var(--primary)] bg-transparent text-black px-2 py-1 placeholder-[var(--primary)] focus:outline-none focus:border-[var(--secondary)]"></textarea>
            <input type="file" name="poster" accept="image/*"
                   class="border-b-2 border-[var(--primary)] bg-transparent px-2 py-1 focus:outline-none focus:border-[var(--secondary)]">

            <!-- Actors -->
            <label class="text-black font-semibold">Actors:</label>
            <div id="actors-container" class="flex flex-wrap gap-2">
                <?php foreach ($allActors as $actor): ?>
                    <div class="actor-btn cursor-pointer px-3 py-1 border-2 border-[var(--primary)] rounded-lg hover:text-black transition-colors duration-300"
                         data-id="<?= $actor['id'] ?>">
                        <?= htmlspecialchars($actor['first_name'].' '.$actor['last_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Directors -->
            <label class="text-black font-semibold">Directors:</label>
            <div id="directors-container" class="flex flex-wrap gap-2">
                <?php foreach ($allDirectors as $director): ?>
                    <div class="director-btn cursor-pointer px-3 py-1 border-2 border-[var(--primary)] rounded-lg hover:text-black transition-colors duration-300"
                         data-id="<?= $director['id'] ?>">
                        <?= htmlspecialchars($director['first_name'].' '.$director['last_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" name="add_movie"
                    class="bg-[var(--primary)] text-[var(--white)] px-6 py-2 rounded-lg shadow-md hover:bg-[var(--secondary)] transition-colors duration-300 font-[Limelight] text-lg mt-4">
                Add Movie
            </button>
        </form>
    </details>

    <!-- Movies Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
            <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Poster</th>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Year</th>
                    <th class="px-4 py-2 text-left">Rating</th>
                    <th class="px-4 py-2 text-left">Length</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Actors</th>
                    <th class="px-4 py-2 text-left">Directors</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr class="hover:text-black transition-colors duration-300">
                        <td class="px-4 py-2"><?= $movie['id'] ?></td>
                        <td class="px-4 py-2">
                            <?php if($movie['poster']): ?>
                                <img src="<?= $movie['poster'] ?>" width="50" class="rounded">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2"><?= htmlspecialchars($movie['title']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($movie['release_year']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($movie['rating']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($movie['length']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($movie['description']) ?></td>
                        <td class="px-4 py-2">
                            <?php
                            $stmt = $db->prepare("SELECT a.first_name, a.last_name FROM actors a JOIN actorAppearIn aa ON a.id=aa.actor_id WHERE aa.movie_id=?");
                            $stmt->execute([$movie['id']]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $a) {
                                echo htmlspecialchars($a['first_name'].' '.$a['last_name']).'<br>';
                            }
                            ?>
                        </td>
                        <td class="px-4 py-2">
                            <?php
                            $stmt = $db->prepare("SELECT d.first_name, d.last_name FROM directors d JOIN directorDirects dd ON d.id=dd.director_id WHERE dd.movie_id=?");
                            $stmt->execute([$movie['id']]);
                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
                                echo htmlspecialchars($d['first_name'].' '.$d['last_name']).'<br>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
