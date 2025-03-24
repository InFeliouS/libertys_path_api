<?php
require __DIR__ . '/../php/config/db.php'; // Ensure correct path

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    try {
        // Start a transaction
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }

        // Prepare the delete query
        $stmt = $pdo->prepare("DELETE FROM dialogues WHERE id = ?");
        $stmt->execute([$delete_id]);

        // Commit the transaction
        $pdo->commit();

        // Redirect back to the dialogue management page
        header("Location: manage_dialogue.php?message=Dialogue deleted successfully");
        exit;
    } catch (PDOException $e) {
        // Roll back only if a transaction was started
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Handle the error properly
        die("Error deleting dialogue: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>
