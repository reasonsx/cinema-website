<?php
function addActorHandler($db, $data): array {
    try {
        $stmt = $db->prepare("INSERT INTO actors (first_name, last_name, date_of_birth, gender, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'], $data['description']]);
        return ["Actor added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: ".$e->getMessage()];
    }
}

function getActors($db) {
    $stmt = $db->prepare("SELECT * FROM actors ORDER BY last_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActorsList($db) {
    $stmt = $db->prepare("SELECT id, first_name, last_name FROM actors ORDER BY last_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
