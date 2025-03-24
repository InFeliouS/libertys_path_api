<?php
require __DIR__ . '/../php/config/db.php'; // Ensure correct path

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $character_name = $_POST['character_name'] ?? null;
    $scene = $_POST['scene'] ?? null;
    $dialogue = $_POST['dialogue'] ?? null;
    $condition = $_POST['condition'] ?? null;

    if (!$character_name || !$scene || !$dialogue) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insert new dialogue (without `previous_dialogue_id`)
        $stmt = $pdo->prepare("INSERT INTO dialogues (character_name, scene, dialogue, dialogue_condition) 
                               VALUES (:character_name, :scene, :dialogue, :dialogue_condition)");
        $stmt->execute([
            ':character_name' => $character_name,
            ':scene' => $scene,
            ':dialogue' => $dialogue,
            ':dialogue_condition' => $condition
        ]);

        $newId = $pdo->lastInsertId();

        $pdo->commit();
        echo json_encode(["success" => "Dialogue added successfully", "new_id" => $newId]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
