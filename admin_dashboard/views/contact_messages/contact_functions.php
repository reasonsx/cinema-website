<?php

// This file provides functions for handling contact form messages in the database.

// Save a new contact message to the database.
function addContactMessage($db, array $payload): array {
    try {
        $stmt = $db->prepare("\n    INSERT INTO contact_messages (name, email, subject, message, ip, user_agent)\n    VALUES (?, ?, ?, ?, ?, ?)\n");
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

// Retrieve all contact messages.
function listContactMessages($db): array {
    $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mark a contact message as read.
function markContactAsRead($db, int $id): void {
    $stmt = $db->prepare("UPDATE contact_messages SET status='read' WHERE id=?");
    $stmt->execute([$id]);
}

// Mark a contact message as new.
function markContactAsNew($db, int $id): void {
    $stmt = $db->prepare("UPDATE contact_messages SET status='new' WHERE id=?");
    $stmt->execute([$id]);
}

// Delete a contact message.
function deleteContactMessage($db, int $id): void {
    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id=?");
    $stmt->execute([$id]);
}

// Count how many contact messages are marked as new.
function countNewContactMessages(PDO $db): int {
    $stmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
    return (int)$stmt->fetchColumn();
}
