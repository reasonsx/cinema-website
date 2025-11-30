<?php //require_once __DIR__ . '/contact_functions.php';
//$messages = $contactMessages ?? [];
//?>
<!---->
<!--<section>-->
<!--    <h2 class="text-3xl font-bold mb-6">Contact Messages</h2>-->
<!---->
<!--    --><?php //if (empty($messages)): ?>
<!--        <div class="p-6 bg-gray-100 text-gray-600 rounded-lg">-->
<!--            No messages found.-->
<!--        </div>-->
<!--    --><?php //else: ?>
<!---->
<!--        <div class="space-y-6">-->
<!---->
<!--            --><?php //foreach ($messages as $m): ?>
<!---->
<!--                <div class="border rounded-xl p-5 bg-white shadow">-->
<!---->
<!--                    <!-- Header -->-->
<!--                    <div class="flex items-center justify-between">-->
<!--                        <h3 class="text-xl font-semibold">-->
<!--                            --><?php //= htmlspecialchars($m['subject']) ?>
<!--                        </h3>-->
<!---->
<!--                        --><?php //if ($m['status'] === "new"): ?>
<!--                            <span class="px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-xs">NEW</span>-->
<!--                        --><?php //else: ?>
<!--                            <span class="px-3 py-1 bg-emerald-200 text-emerald-800 rounded-full text-xs">READ</span>-->
<!--                        --><?php //endif; ?>
<!--                    </div>-->
<!---->
<!--                    <!-- Info -->-->
<!--                    <p class="text-gray-600 mt-2">-->
<!--                        <strong>Name:</strong> --><?php //= htmlspecialchars($m['name']) ?><!--<br>-->
<!--                        <strong>Email:</strong> --><?php //= htmlspecialchars($m['email']) ?><!--<br>-->
<!--                        <strong>Date:</strong> --><?php //= htmlspecialchars($m['created_at']) ?>
<!--                    </p>-->
<!---->
<!--                    <!-- Message -->-->
<!--                    <p class="mt-4 text-gray-800 whitespace-pre-line">-->
<!--                        --><?php //= htmlspecialchars($m['message']) ?>
<!--                    </p>-->
<!---->
<!--                    <!-- Actions -->-->
<!--                    <div class="flex gap-3 mt-6">-->
<!---->
<!--                        --><?php //if ($m['status'] === "new"): ?>
<!--                            <form method="post">-->
<!--                                <input type="hidden" name="id" value="--><?php //= $m['id'] ?><!--">-->
<!--                                <button name="mark_read"-->
<!--                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg">-->
<!--                                    Mark Read-->
<!--                                </button>-->
<!--                            </form>-->
<!--                        --><?php //else: ?>
<!--                            <form method="post">-->
<!--                                <input type="hidden" name="id" value="--><?php //= $m['id'] ?><!--">-->
<!--                                <button name="mark_new"-->
<!--                                        class="px-4 py-2 bg-amber-600 text-white rounded-lg">-->
<!--                                    Mark New-->
<!--                                </button>-->
<!--                            </form>-->
<!--                        --><?php //endif; ?>
<!---->
<!--                        <form method="post" onsubmit="return confirm('Delete message?');">-->
<!--                            <input type="hidden" name="id" value="--><?php //= $m['id'] ?><!--">-->
<!--                            <button name="delete_message"-->
<!--                                    class="px-4 py-2 bg-red-600 text-white rounded-lg">-->
<!--                                Delete-->
<!--                            </button>-->
<!--                        </form>-->
<!---->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!---->
<!--            --><?php //endforeach; ?>
<!---->
<!--        </div>-->
<!---->
<!--    --><?php //endif; ?>
<!---->
<!--</section>-->
