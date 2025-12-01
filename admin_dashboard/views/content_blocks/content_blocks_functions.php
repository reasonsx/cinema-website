<?php

// CREATE
function addContentBlock(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("
            INSERT INTO content_blocks (tag, title, text)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$data['tag'], $data['title'], $data['text']]);
        return ["Content block added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// READ ALL
function getContentBlocks(PDO $db): array {
    $stmt = $db->prepare("SELECT * FROM content_blocks ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// READ ONE
function getContentBlockById(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT * FROM content_blocks WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

// UPDATE
function editContentBlock(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("
            UPDATE content_blocks
            SET tag = ?, title = ?, text = ?
            WHERE id = ?
        ");
        $stmt->execute([$data['tag'], $data['title'], $data['text'], $data['block_id']]);

        return ["Content block updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// DELETE
function deleteContentBlock(PDO $db, int $id): array {
    try {
        $stmt = $db->prepare("DELETE FROM content_blocks WHERE id = ?");
        $stmt->execute([$id]);
        return ["Content block deleted!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

