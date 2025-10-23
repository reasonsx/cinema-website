<?php
function addNews($db, $data): array {
    try {
        $stmt = $db->prepare("INSERT INTO news (title, content, date_added) VALUES (?, ?, NOW())");
        $stmt->execute([$data['title'], $data['content']]);
        return ["News added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function getNews($db): array {
    $stmt = $db->prepare("SELECT * FROM news ORDER BY date_added DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function editNews($db, $data): array {
    try {
        $stmt = $db->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['content'], $data['news_id']]);
        return ["News updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function deleteNews($db, $newsId): array {
    try {
        $stmt = $db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$newsId]);
        return ["News deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

