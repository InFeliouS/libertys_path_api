<?php
require '../php/config/db.php';

// Get parameters from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$condition = isset($_GET['condition']) ? $_GET['condition'] : '';
$scene = isset($_GET['scene']) ? $_GET['scene'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';
$task_name = isset($_GET['task_name']) ? $_GET['task_name'] : '';

// Build query based on parameters
$query = "SELECT id, character_name, dialogue, task_name FROM dialogues WHERE 1=1";
$params = [];

// Add filters based on provided parameters
if ($id > 0) {
    $query .= " AND id = :id";
    $params[':id'] = $id;
}

if (!empty($condition)) {
    $query .= " AND dialogue_condition = :condition";
    $params[':condition'] = $condition;
}

if (!empty($scene)) {
    $query .= " AND scene = :scene";
    $params[':scene'] = $scene;
}

if (!empty($level)) {
    $query .= " AND level = :level";
    $params[':level'] = $level;
}

if (!empty($task_name)) {
    $query .= " AND task_name = :task_name";
    $params[':task_name'] = $task_name;
}

// Order by ID to ensure proper sequence
$query .= " ORDER BY id ASC";

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$dialogues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return results
if ($dialogues) {
    echo json_encode(["success" => true, "dialogues" => $dialogues]);
} else {
    echo json_encode(["success" => false, "message" => "No dialogues found"]);
}
?>