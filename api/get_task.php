<?php
require '../php/config/db.php';

// Get parameters from request
$task_name = isset($_GET['task_name']) ? $_GET['task_name'] : '';
$scene = isset($_GET['scene']) ? $_GET['scene'] : '';
$condition = isset($_GET['condition']) ? $_GET['condition'] : '';

// Validate required parameters
if (empty($task_name)) {
    echo json_encode(["success" => false, "message" => "Task name is required"]);
    exit;
}

// Build query based on parameters
$query = "SELECT id, task_name, task_content, dialogue_condition FROM tasks WHERE task_name = :task_name";
$params = [':task_name' => $task_name];

if (!empty($scene)) {
    $query .= " AND scene = :scene";
    $params[':scene'] = $scene;
}

if (!empty($condition)) {
    $query .= " AND dialogue_condition = :condition";
    $params[':condition'] = $condition;
}

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

// Return results
if ($task) {
    echo json_encode(["success" => true, "task" => $task]);
} else {
    echo json_encode(["success" => false, "message" => "No task found with the given name"]);
}
?>