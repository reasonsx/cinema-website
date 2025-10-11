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


function deleteDirector($db, $directorId): array {
    try {
        //  remove links to movies first
        $stmt = $db->prepare("DELETE FROM directorDirects WHERE director_id = ?");
        $stmt->execute([$directorId]);

        // Delete the director
        $stmt = $db->prepare("DELETE FROM directors WHERE id = ?");
        $stmt->execute([$directorId]);

        return ["Director deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function editDirectorHandler($db, $data): array {
    try {
        $stmt = $db->prepare("UPDATE directors 
            SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, description = ? 
            WHERE id = ?");
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['description'],
            $data['id']
        ]);
        return ["Director updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}
