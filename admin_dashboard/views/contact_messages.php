<?php
// Safety
$messages = $contactMessages ?? [];

include 'components/table.php';

renderTable([
    'title' => 'Contact Messages',
    'headers' => ['#', 'Status', 'Name', 'Email', 'Subject', 'Message', 'Date', 'Actions'],
    'rows' => $messages,
    'renderRow' => function ($m) {
        $excerpt = mb_strlen($m['message']) > 100 ? mb_substr($m['message'], 0, 100) . '…' : $m['message'];

        $statusBadge = $m['status'] === 'read'
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-medium">
             <i class="pi pi-check-circle text-emerald-600"></i> Read
           </span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-medium">
             <i class="pi pi-envelope text-amber-600"></i> New
           </span>';

        $actions = '
        <div class="flex flex-wrap items-center justify-start gap-3">

          <!-- View Button -->
          <details class="group relative">
            <summary class="cursor-pointer flex items-center justify-center gap-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px] text-center">
              <i class="pi pi-eye text-gray-600"></i> View
            </summary>
            <div class="absolute z-10 mt-2 w-80 right-0 rounded-xl border border-gray-200 bg-white shadow-xl p-4 text-gray-800 text-sm">
              '.nl2br(htmlspecialchars($m['message'])).'
              <div class="mt-3 text-xs text-gray-500 border-t pt-2">
                IP: '.htmlspecialchars($m['ip'] ?? '-').'<br>
                UA: '.htmlspecialchars($m['user_agent'] ?? '-').'
              </div>
            </div>
          </details>

          <!-- Status toggle -->
          '.($m['status'] === 'read'
                ? '<form method="post" class="inline">
                   <input type="hidden" name="id" value="'.(int)$m['id'].'">
                   <button name="mark_new"
                     class="flex items-center justify-center gap-1.5 rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
                     <i class="pi pi-refresh text-amber-700"></i> Mark as New
                   </button>
                 </form>'
                : '<form method="post" class="inline">
                   <input type="hidden" name="id" value="'.(int)$m['id'].'">
                   <button name="mark_read"
                     class="flex items-center justify-center gap-1.5 rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
                     <i class="pi pi-check text-emerald-700"></i> Mark as Read
                   </button>
                 </form>'
            ).'

          <!-- Delete -->
          <form method="post" class="inline" onsubmit="return confirm(\'Delete this message?\');">
            <input type="hidden" name="id" value="'.(int)$m['id'].'">
            <button name="delete_message"
              class="flex items-center justify-center gap-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 px-4 py-1.5 text-sm font-medium transition shadow-sm min-w-[120px]">
              <i class="pi pi-trash text-red-600"></i> Delete
            </button>
          </form>

        </div>';

        return [
            (int)$m['id'],
            $statusBadge,
            htmlspecialchars($m['name']),
            '<a href="mailto:'.htmlspecialchars($m['email']).'" class="text-blue-600 hover:text-blue-800 font-medium">'
            .htmlspecialchars($m['email']).'</a>',
            htmlspecialchars($m['subject']),
            '<span class="text-gray-600 italic">'.nl2br(htmlspecialchars($excerpt)).'</span>',
            htmlspecialchars($m['created_at']),
            $actions
        ];
    }
]);
?>
