<?php
// Safety: ensure $contactMessages exists
$messages = $contactMessages ?? [];
?>

<section class="mb-10">
    <h2 class="text-2xl font-bold text-primary mb-4 flex items-center gap-2">
        <i class="pi pi-inbox"></i> Contact Messages
    </h2>

    <?php if (empty($messages)): ?>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-gray-600">
            No messages yet.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Subject</th>
                    <th class="px-4 py-2 text-left">Message (excerpt)</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $m): ?>
                    <?php
                    $excerpt = mb_strlen($m['message']) > 120 ? mb_substr($m['message'], 0, 120) . '…' : $m['message'];
                    ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= (int)$m['id'] ?></td>
                        <td class="px-4 py-2">
                            <?php if ($m['status'] === 'read'): ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 text-emerald-800 px-3 py-1">
                    <i class="pi pi-check-circle"></i> Read
                  </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 text-amber-800 px-3 py-1">
                    <i class="pi pi-envelope"></i> New
                  </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2"><?= htmlspecialchars($m['name']) ?></td>
                        <td class="px-4 py-2">
                            <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="text-primary hover:underline">
                                <?= htmlspecialchars($m['email']) ?>
                            </a>
                        </td>
                        <td class="px-4 py-2"><?= htmlspecialchars($m['subject']) ?></td>
                        <td class="px-4 py-2 text-gray-700"><?= nl2br(htmlspecialchars($excerpt)) ?></td>
                        <td class="px-4 py-2 text-gray-500"><?= htmlspecialchars($m['created_at']) ?></td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- View full (small inline modal via details/summary) -->
                                <details class="group">
                                    <summary class="cursor-pointer inline-flex items-center gap-2 rounded border border-gray-300 px-3 py-1 hover:bg-gray-100">
                                        <i class="pi pi-eye"></i> View
                                    </summary>
                                    <div class="mt-2 max-w-md whitespace-pre-wrap rounded-lg border border-gray-200 bg-white p-3 text-gray-800 shadow">
                                        <?= nl2br(htmlspecialchars($m['message'])) ?>
                                        <div class="mt-3 text-xs text-gray-500">
                                            IP: <?= htmlspecialchars($m['ip'] ?? '-') ?> • UA: <?= htmlspecialchars($m['user_agent'] ?? '-') ?>
                                        </div>
                                    </div>
                                </details>

                                <?php if ($m['status'] === 'read'): ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                        <button name="mark_new" class="inline-flex items-center gap-2 rounded border border-gray-300 px-3 py-1 hover:bg-gray-100">
                                            <i class="pi pi-refresh"></i> Mark New
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                        <button name="mark_read" class="inline-flex items-center gap-2 rounded border border-gray-300 px-3 py-1 hover:bg-gray-100">
                                            <i class="pi pi-check"></i> Mark Read
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" class="inline" onsubmit="return confirm('Delete this message?');">
                                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                    <button name="delete_message" class="inline-flex items-center gap-2 rounded border border-red-300 text-red-700 px-3 py-1 hover:bg-red-50">
                                        <i class="pi pi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
