<?php
// Fetch all users
function getUsers(PDO $db): array {
    $stmt = $db->prepare("SELECT id, firstname, lastname, email, isAdmin FROM users ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch a single user by ID
function getUserById(PDO $db, int $userId): ?array {
    $stmt = $db->prepare("SELECT id, firstname, lastname, email, isAdmin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

// Add new user (admin functionality)
function addUser(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("
            INSERT INTO users (firstname, lastname, email, password, isAdmin)
            VALUES (?, ?, ?, ?, ?)
        ");
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

// Edit user (admin functionality)
function editUser(PDO $db, array $data): array {
    try {
        $stmt = $db->prepare("
            UPDATE users
            SET firstname = ?, lastname = ?, isAdmin = ?
            WHERE id = ?
        ");
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

// Delete user
function deleteUser(PDO $db, int $userId): array {
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return ["User deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// ✅ Update user’s own profile (used in profile.php)
function updateUserProfile(PDO $db, int $userId, array $data): array {
    try {
        // Fetch the current hashed password
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $storedHash = $stmt->fetchColumn();

        if (!$storedHash || !password_verify($data['current_password'], $storedHash)) {
            return ["", "Incorrect current password."];
        }

        $query = "UPDATE users SET firstname = ?, lastname = ?, email = ?";
        $params = [
            trim($data['firstname']),
            trim($data['lastname']),
            trim($data['email'])
        ];

        if (!empty($data['new_password'])) {
            $query .= ", password = ?";
            $params[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = ?";
        $params[] = $userId;

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        return ["Profile updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Error updating profile: " . $e->getMessage()];
    }
}

// ✅ Check login credentials (used in login.php)
function verifyUserLogin(PDO $db, string $email, string $password): ?array {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}
?>
