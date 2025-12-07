<?php

// Fetch all users
function getUsers(PDO $db): array
{
    $stmt = $db->prepare("SELECT id, firstname, lastname, email, isAdmin FROM users ORDER BY id DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch single user by ID
function getUserById(PDO $db, int $userId): ?array
{
    $stmt = $db->prepare("SELECT id, firstname, lastname, email, isAdmin FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

// Add new user (admin)
function addUser(PDO $db, array $data): array
{
    try {
        $stmt = $db->prepare(
            "INSERT INTO users (firstname, lastname, email, password, isAdmin)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            trim($data['firstname']),
            trim($data['lastname']),
            trim($data['email']),
            password_hash($data['password'], PASSWORD_DEFAULT),
            !empty($data['isAdmin']) ? 1 : 0
        ]);

        return ["User added successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// Edit existing user (admin)
function editUser(PDO $db, array $data): array
{
    try {
        $stmt = $db->prepare(
            "UPDATE users SET firstname = ?, lastname = ?, isAdmin = ? WHERE id = ?"
        );

        $stmt->execute([
            trim($data['firstname']),
            trim($data['lastname']),
            !empty($data['isAdmin']) ? 1 : 0,
            (int)$data['user_id']
        ]);

        return ["User updated successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// Delete user
function deleteUser(PDO $db, int $userId): array
{
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        return ["User deleted successfully!", ""];
    } catch (PDOException $e) {
        return ["", "Database error: " . $e->getMessage()];
    }
}

// Update user profile (user-facing)
function updateUserProfile(PDO $db, int $userId, array $data): array
{
    try {
        // Verify current password
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

        // Update password if provided
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

// Verify login
function verifyUserLogin(PDO $db, string $email, string $password): ?array
{
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}

