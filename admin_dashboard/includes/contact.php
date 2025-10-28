<?php
function addContactMessage($db, array $payload): array {
    try {
        $stmt = $db->prepare("
    INSERT INTO contact_messages (name, email, subject, message, ip, user_agent)
    VALUES (?, ?, ?, ?, ?, ?)
");
        $stmt->execute([
            $payload['name'],
            $payload['email'],
            $payload['subject'],
            $payload['message'],
            $payload['ip'] ?? null,
            $payload['ua'] ?? null
        ]);
        return ["Saved", ""];
    } catch (PDOException $e) {
        return ["", "DB error: " . $e->getMessage()];
    }
}

function listContactMessages($db): array {
    $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function markContactAsRead($db, int $id): void {
    $stmt = $db->prepare("UPDATE contact_messages SET status='read' WHERE id=?");
    $stmt->execute([$id]);
}

function markContactAsNew($db, int $id): void {
    $stmt = $db->prepare("UPDATE contact_messages SET status='new' WHERE id=?");
    $stmt->execute([$id]);
}

function deleteContactMessage($db, int $id): void {
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id=?");
    $stmt->execute([$id]);
}

function countNewContactMessages(PDO $db): int {
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
    return (int)$stmt->fetchColumn();
}
