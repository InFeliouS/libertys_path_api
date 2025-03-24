<?php
require __DIR__ . '/../php/config/db.php'; // Correct path to db.php

$message = "";
$messageClass = "";
$character_name = $_POST["character_name"] ?? "";
$scene = $_POST["scene"] ?? "";
$dialogue = $_POST["dialogue"] ?? "";
$condition = $_POST["condition"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate required fields
        if (empty($character_name) || empty($scene) || empty($dialogue)) {
            throw new Exception("Scene, Character Name, and Dialogue are required.");
        }

        // Find the lowest available ID
        $stmt = $pdo->query("SELECT id FROM dialogues ORDER BY id ASC");
        $existing_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $new_id = 1;
        foreach ($existing_ids as $index => $id) {
            if ($id != $index + 1) { 
                $new_id = $index + 1;
                break;
            }
            $new_id = count($existing_ids) + 1;
        }

        // Insert new dialogue with assigned ID
        $stmt = $pdo->prepare("INSERT INTO dialogues (id, scene, character_name, dialogue, dialogue_condition) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$new_id, $scene, $character_name, $dialogue, $condition ?: null]);

        $message = "Dialogue added successfully!";
        $messageClass = "success";

        // Reset form inputs after successful submission
        $character_name = "";
        $scene = "";
        $dialogue = "";
        $condition = "";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageClass = "error";
    }
}

// Fetch dialogues for display
try {
    $stmt = $pdo->query("SELECT id, scene, character_name, dialogue, dialogue_condition FROM dialogues ORDER BY id ASC");
    $dialogues = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage NPC Dialogue</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #ddd; }
        form { margin-bottom: 20px; }
        textarea {
            width: 100%; 
            height: 150px; 
            resize: none; 
            font-size: 16px;
        }
    </style>
</head>
<body>

    <h2>Add NPC Dialogue</h2>

    <?php if ($message): ?>
        <div class="message <?php echo $messageClass; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        Scene Name: <input type="text" name="scene" value="<?php echo htmlspecialchars($scene); ?>" required> <br>
        Character Name: <input type="text" name="character_name" value="<?php echo htmlspecialchars($character_name); ?>" required> <br>
        Dialogue: <br>
        <textarea name="dialogue" required><?php echo htmlspecialchars($dialogue); ?></textarea> <br>
        Dialogue Condition: <input type="text" name="condition" value="<?php echo htmlspecialchars($condition); ?>"> <br>
        <button type="submit">Add Dialogue</button>
    </form>

    <h2>Existing Dialogues</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Scene</th>
            <th>Character Name</th>
            <th>Dialogue</th>
            <th>Condition</th>
            <th>Action</th>
        </tr>
        <?php foreach ($dialogues as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row["id"]); ?></td>
            <td><?php echo htmlspecialchars($row["scene"]); ?></td>
            <td><?php echo htmlspecialchars($row["character_name"]); ?></td>
            <td><?php echo htmlspecialchars($row["dialogue"]); ?></td>
            <td><?php echo htmlspecialchars($row["dialogue_condition"]); ?></td>
            <td><a href="delete_dialogue.php?delete_id=<?php echo $row["id"]; ?>" onclick="return confirm('Delete this dialogue?');">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
