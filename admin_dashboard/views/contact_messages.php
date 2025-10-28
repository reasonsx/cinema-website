<?php
// Safety: ensure $contactMessages exists
$messages = $contactMessages ?? [];
?>

<section class="mb-10">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        <i class="pi pi-inbox text-primary text-xl"></i>
        Contact Messages
    </h2>

    <?php if (empty($messages)): ?>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-gray-600 text-center shadow-sm">
            No messages yet.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-md">
            <table class="min-w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Subject</th>
                    <th class="px-5 py-3 text-left">Message</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                <?php foreach ($messages as $m): ?>
                    <?php $excerpt = mb_strlen($m['message']) > 100 ? mb_substr($m['message'], 0, 100) . '…' : $m['message']; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-600"><?= (int)$m['id'] ?></td>

                        <td class="px-5 py-3">
                            <?php if ($m['status'] === 'read'): ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-medium">
                    <i class="pi pi-check-circle text-emerald-600"></i> Read
                  </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-medium">
                    <i class="pi pi-envelope text-amber-600"></i> New
                  </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-5 py-3 text-gray-800 font-medium"><?= htmlspecialchars($m['name']) ?></td>

                        <td class="px-5 py-3">
                            <a href="mailto:<?= htmlspecialchars($m['email']) ?>"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                <?= htmlspecialchars($m['email']) ?>
                            </a>
                        </td>

                        <td class="px-5 py-3 text-gray-700"><?= htmlspecialchars($m['subject']) ?></td>

                        <td class="px-5 py-3 text-gray-600 italic"><?= nl2br(htmlspecialchars($excerpt)) ?></td>

                        <td class="px-5 py-3 text-gray-500 text-sm"><?= htmlspecialchars($m['created_at']) ?></td>

                        <td class="px-5 py-3">
                            <div class="flex flex-wrap items-center justify-start gap-3">

                                <!-- View Button -->
                                <details class="group relative">
                                    <summary class="cursor-pointer flex items-center justify-center gap-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px] text-center">
                                        <i class="pi pi-eye text-gray-600"></i> View
                                    </summary>
                                    <div class="absolute z-10 mt-2 w-80 right-0 rounded-xl border border-gray-200 bg-white shadow-xl p-4 text-gray-800 text-sm">
                                        <?= nl2br(htmlspecialchars($m['message'])) ?>
                                        <div class="mt-3 text-xs text-gray-500 border-t pt-2">
                                            IP: <?= htmlspecialchars($m['ip'] ?? '-') ?><br>
                                            UA: <?= htmlspecialchars($m['user_agent'] ?? '-') ?>
                                        </div>
                                    </div>
                                </details>

                                <!-- Mark Buttons (Read/New) -->
                                <?php if ($m['status'] === 'read'): ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                        <button name="mark_new"
                                                class="flex items-center justify-center gap-1.5 rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
                                            <i class="pi pi-refresh text-amber-700"></i> Mark as New
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                        <button name="mark_read"
                                                class="flex items-center justify-center gap-1.5 rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
                                            <i class="pi pi-check text-emerald-700"></i> Mark as Read
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Delete -->
                                <form method="post" class="inline" onsubmit="return confirm('Delete this message?');">
                                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                    <button name="delete_message"
                                            class="flex items-center justify-center gap-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
                                        <i class="pi pi-trash text-red-600"></i> Delete
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
