<?php

/**
 * Execute a prepared statement safely.
 */
function dbRun(PDO $db, string $sql, array $params = []): bool
{
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Fetch all rows from a query.
 */
function dbFetchAll(PDO $db, string $sql, array $params = []): array
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Standardized success/error response.
 */
function response(bool $ok, string $msg): array
{
    return $ok ? [$msg, ""] : ["", $msg];
}

/**
 * Validate incoming actor data.
 */
function validateActor(array $data): ?string
{
    if (empty($data['first_name']) || empty($data['last_name'])) {
        return "First and last name are required.";
    }

    if (!in_array($data['gender'], ['Male', 'Female', 'Other'], true)) {
        return "Invalid gender.";
    }

    if (!empty($data['date_of_birth']) && !strtotime($data['date_of_birth'])) {
        return "Invalid date of birth.";
    }

    return null;
}

/**
 * Insert a new actor record.
 */
function addActorHandler(PDO $db, array $data): array
{
    if ($err = validateActor($data)) {
        return response(false, $err);
    }

    $ok = dbRun(
        $db,
        "INSERT INTO actors (first_name, last_name, date_of_birth, gender, description)
         VALUES (?, ?, ?, ?, ?)",
        [
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'] ?: null,
            $data['gender'],
            $data['description']
        ]
    );

    return response($ok, $ok ? "Actor added successfully!" : "Failed to add actor.");
}

/**
 * Retrieve all actors.
 */
function getActors(PDO $db): array
{
    return dbFetchAll(
        $db,
        "SELECT * FROM actors ORDER BY id DESC"
    );
}

/**
 * Retrieve ID + name list of actors.
 */
function getActorsList(PDO $db): array
{
    return dbFetchAll(
        $db,
        "SELECT id, first_name, last_name FROM actors ORDER BY id DESC"
    );
}

/**
 * Delete actor and related records.
 */
function deleteActor(PDO $db, int $actorId): array
{
    $db->beginTransaction();

    try {
        dbRun($db, "DELETE FROM actorAppearIn WHERE actor_id = ?", [$actorId]);
        $ok = dbRun($db, "DELETE FROM actors WHERE id = ?", [$actorId]);

        $db->commit();
        return response($ok, "Actor deleted successfully!");
    } catch (PDOException $e) {
        $db->rollBack();
        return response(false, "Database error: " . $e->getMessage());
    }
}

/**
 * Update an existing actor record.
 */
function editActorHandler(PDO $db, array $data): array
{
    if ($err = validateActor($data)) {
        return response(false, $err);
    }

    $ok = dbRun(
        $db,
        "UPDATE actors
         SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, description = ?
         WHERE id = ?",
        [
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'] ?: null,
            $data['gender'],
            $data['description'],
            $data['actor_id']
        ]
    );

    return response($ok, $ok ? "Actor updated successfully!" : "Failed to update actor.");
}
