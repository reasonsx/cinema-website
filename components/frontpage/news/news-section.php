<section id="news" class="py-16 bg-black text-white">
    <div class="mx-auto max-w-7xl px-6">

        <!-- Header -->
        <div class="text-center mb-12">
            <h2 class="text-5xl font-[Limelight] tracking-wide text-[var(--secondary)]">NEWS</h2>
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="h-[2px] w-16 bg-white/15"></span>
                <i class="pi pi-star text-[var(--secondary)]"></i>
                <span class="h-[2px] w-16 bg-white/15"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($newsList as $news): ?>
                <?php
                $id = (int)$news['id'];
                $title = htmlspecialchars($news['title'] ?? 'Untitled');
                $dateAdded = isset($news['date_added']) ? date('M d, Y', strtotime($news['date_added'])) : '';
                $content = htmlspecialchars($news['content'] ?? '');
                $excerpt = mb_strlen($content) > 260 ? mb_substr($content, 0, 260) . '…' : $content;
                ?>
                <article
                    class="group flex flex-col justify-between rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl overflow-hidden">

                    <div class="flex-1 flex flex-col px-5 pt-5 pb-6">

                        <div class="flex items-start justify-between gap-3 mb-4">
                            <h3 class="text-2xl font-[Limelight] leading-snug text-white">
                                <?= $title ?>
                            </h3>

                            <div class="shrink-0 inline-flex items-center gap-2 rounded-full border border-white/15 bg-black/40 px-3 py-1 text-xs text-white/80">
                                <i class="pi pi-calendar text-[var(--secondary)]"></i>
                                <span><?= $dateAdded ?></span>
                            </div>
                        </div>

                        <div class="h-px bg-white/10 mb-4"></div>

                        <p class="text-white/85 leading-relaxed mb-5"><?= $excerpt ?></p>

                        <!-- Button -->
                        <div class="mt-auto">
                            <a href="news-details.php?id=<?= $id ?>" class="btn">
                                <i class="pi pi-angle-right"></i>
                                Read more
                            </a>
                        </div>

                    </div>

                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
