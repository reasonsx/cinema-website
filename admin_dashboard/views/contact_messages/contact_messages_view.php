<?php
//// Safety
//$messages = $contactMessages ?? [];
//backend '../../components/table.php';
//require_once __DIR__ . '/../../../shared/helpers.php';
//
//renderTable([
//    'title' => 'Contact Messages',
//    'headers' => ['ID', 'Status', 'Name', 'Email', 'Subject', 'Message', 'Date', 'Actions'],
//    'rows' => $messages,
//    'searchable' => true,   // ✅ enable built-in search input
//    'compact' => false,
//    'renderRow' => function ($m) {
//        $excerpt = mb_strlen($m['message']) > 100
//            ? mb_substr($m['message'], 0, 100) . '…'
//            : $m['message'];
//
//        $statusBadge = $m['status'] === 'read'
//            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-medium">
//                 <i class="pi pi-check-circle text-emerald-600"></i> Read
//               </span>'
//            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-medium">
//                 <i class="pi pi-envelope text-amber-600"></i> New
//               </span>';
//
//        $actions = '
//<div class="flex flex-col items-stretch justify-stretch w-full">
//  <div class="flex flex-col gap-3.5">
//    <!-- View Button -->
//    <details class="group relative w-full">
//      <summary class="cursor-pointer flex items-center justify-center gap-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 text-sm font-medium transition shadow-sm text-center w-full">
//        <i class="pi pi-eye text-gray-600"></i> View
//      </summary>
//      <div class="absolute z-10 mt-2 w-80 right-0 rounded-xl border border-gray-200 bg-white shadow-xl p-4 text-gray-800 text-sm">
//        '.nl2br(htmlspecialchars($m['message'])).'
//        <div class="mt-3 text-xs text-gray-500 border-t pt-2">
//          IP: '.htmlspecialchars($m['ip'] ?? '-').'<br>
//          UA: '.htmlspecialchars($m['user_agent'] ?? '-').'
//        </div>
//      </div>
//    </details>
//
//    <!-- Status toggle -->
//    '.($m['status'] === 'read'
//                ? '<form method="post" class="w-full">
//                 <input type="hidden" name="id" value="'.(int)$m['id'].'">
//                 <button name="mark_new"
//                   class="flex items-center justify-center gap-1.5 rounded-md bg-amber-100 text-amber-800 hover:bg-amber-200 px-4 py-2 text-sm font-medium transition shadow-sm w-full">
//                   <i class="pi pi-refresh text-amber-700"></i> Mark as New
//                 </button>
//               </form>'
//                : '<form method="post" class="w-full">
//                 <input type="hidden" name="id" value="'.(int)$m['id'].'">
//                 <button name="mark_read"
//                   class="flex items-center justify-center gap-1.5 rounded-md bg-emerald-100 text-emerald-800 hover:bg-emerald-200 px-4 py-2 text-sm font-medium transition shadow-sm w-full">
//                   <i class="pi pi-check text-emerald-700"></i> Mark as Read
//                 </button>
//               </form>').'
//  </div>
//
//  <!-- Delete -->
//  <form method="post" class="w-full mt-3" onsubmit="return confirm(\'Delete this message?\');">
//    <input type="hidden" name="id" value="'.(int)$m['id'].'">
//    <button name="delete_message"
//      class="flex items-center justify-center gap-1.5 rounded-md bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 text-sm font-medium transition shadow-sm w-full">
//      <i class="pi pi-trash text-red-600"></i> Delete
//    </button>
//  </form>
//</div>';
//
//        return [
//            (int)$m['id'],
//            $statusBadge,
//            htmlspecialchars($m['name']),
//            '<a href="mailto:'.htmlspecialchars($m['email']).'" class="text-blue-600 hover:text-blue-800 font-medium">'
//            .htmlspecialchars($m['email']).'</a>',
//            htmlspecialchars($m['subject']),
//            '<span class="text-gray-600 italic">'.nl2br(htmlspecialchars($excerpt)).'</span>',
//            htmlspecialchars($m['created_at']),
//            $actions
//        ];
//    }
//]);
//?>
