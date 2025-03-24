<?php
require '../php/config/db.php';

// Get parameters from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$condition = isset($_GET['condition']) ? $_GET['condition'] : '';
$scene = isset($_GET['scene']) ? $_GET['scene'] : '';

// Build query based on parameters
$query = "SELECT id, character_name, dialogue FROM dialogues WHERE 1=1";
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