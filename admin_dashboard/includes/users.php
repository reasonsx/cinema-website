<?php
function getUsers($db) {
    $stmt = $db->prepare("SELECT id, firstname, lastname, email, isAdmin FROM users ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addUser($db, $data): array {
    try {
        $stmt = $db->prepare("INSERT INTO users (firstname, lastname, email, password, isAdmin) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            isset($data['isAdmin']) ? 1 : 0
        ]);
        return ["User added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function editUser($db, $data): array {
    try {
        $stmt = $db->prepare("UPDATE users SET firstname = ?, lastname = ?, isAdmin = ? WHERE id = ?");
        $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            isset($data['isAdmin']) ? 1 : 0,
            $data['user_id']
        ]);
        return ["User updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

function deleteUser($db, $userId): array {
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return ["User deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}
?>
