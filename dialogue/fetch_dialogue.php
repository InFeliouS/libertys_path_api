<?php
require __DIR__ . '/../php/config/db.php'; // Ensure correct path

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $ids = $_GET['ids'] ?? null; // Expecting a comma-separated list of IDs

    if (!$ids) {
        echo json_encode(["error" => "Missing ids parameter"]);
        exit;
    }

    try {
        // Convert comma-separated IDs into an array
        $idArray = explode(',', $ids);
        $placeholders = implode(',', array_fill(0, count($idArray), '?'));

        // Prepare SQL with dynamic placeholders
        $stmt = $pdo->prepare("SELECT id, character_name, dialogue, dialogue_condition FROM dialogues WHERE id IN ($placeholders) ORDER BY id ASC");

        // Execute the query with the IDs as parameters
        $stmt->execute($idArray);

        $dialogues = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($dialogues) {
            echo json_encode(["success" => true, "dialogues" => $dialogues]);
        } else {
            echo json_encode(["error" => "No dialogues found for the provided IDs"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
