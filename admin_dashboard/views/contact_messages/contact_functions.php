<?php
//
//function listContactMessages(PDO $db): array {
//    $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
//    return $stmt->fetchAll(PDO::FETCH_ASSOC);
//}
//
//function markContactAsRead(PDO $db, int $id): void {
//    $stmt = $db->prepare("UPDATE contact_messages SET status='read' WHERE id=?");
//    $stmt->execute([$id]);
//}
//
//function markContactAsNew(PDO $db, int $id): void {
//    $stmt = $db->prepare("UPDATE contact_messages SET status='new' WHERE id=?");
//    $stmt->execute([$id]);
//}
//
//function deleteContactMessage(PDO $db, int $id): void {
//    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id=?");
//    $stmt->execute([$id]);
//}
