<?php
function addDirectorHandler($db, $data): array {
    try {
        $stmt = $db->prepare("INSERT INTO directors (first_name, last_name, date_of_birth, gender, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'], $data['description']]);
        return ["Director added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: ".$e->getMessage()];
    }
}

function getDirectors($db) {
    $stmt = $db->prepare("SELECT * FROM directors ORDER BY last_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDirectorsList($db) {
    $stmt = $db->prepare("SELECT id, first_name, last_name FROM directors ORDER BY last_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
