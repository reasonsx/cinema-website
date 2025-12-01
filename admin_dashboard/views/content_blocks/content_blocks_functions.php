<?php

function getContentBlocks(PDO $db): array {
    $stmt = $db->query("SELECT * FROM content_blocks ORDER BY tag ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getContentBlock(PDO $db, int $id) {
    $stmt = $db->prepare("SELECT * FROM content_blocks WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createContentBlock(PDO $db, string $tag, ?string $title, ?string $text): bool {
    $stmt = $db->prepare("INSERT INTO content_blocks (tag, title, text) VALUES (?, ?, ?)");
    return $stmt->execute([$tag, $title, $text]);
}

function updateContentBlock(PDO $db, int $id, string $tag, ?string $title, ?string $text): bool {
    $stmt = $db->prepare("UPDATE content_blocks SET tag = ?, title = ?, text = ? WHERE id = ?");
    return $stmt->execute([$tag, $title, $text, $id]);
}

function deleteContentBlock(PDO $db, int $id): bool {
    $stmt = $db->prepare("DELETE FROM content_blocks WHERE id = ?");
    return $stmt->execute([$id]);
}
