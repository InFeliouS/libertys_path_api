<?php
// public/api/v1/student_login.php
header('Content-Type: application/json');

// Load DB connection
require_once __DIR__ . '/../../../src/config/db.php';

// Load QPR attempt initializer function
require_once __DIR__ . '/../../../src/utils/student_helpers.php';

// Decode the incoming JSON
$payload  = json_decode(file_get_contents('php://input'), true);
$username = trim($payload['username']  ?? '');
$password =          $payload['password'] ?? '';

// Prepare the default response
$response = [
    'status'     => 'error',
    'message'    => '',
    'student_id' => '',
    'username'   => ''
];

// Basic validation
if ($username === '' || $password === '') {
    $response['message'] = 'Username and password are required.';
    echo json_encode($response);
    exit;
}

try {
    // Look up the account and join to student info
    $stmt = $pdo->prepare("
        SELECT 
          sa.student_id, 
          sa.password AS hash 
        FROM student_accounts AS sa
        WHERE sa.username = :username
    ");
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['hash'])) {
        // Successful login

        // Initialize QPR attempt rows if missing
        insert_attempt_rows_if_missing($pdo, $row['student_id']);

        $response['status']     = 'success';
        $response['message']    = 'Login successful';
        $response['student_id'] = (string)$row['student_id'];
        $response['username']   = $username;
    } else {
        $response['message'] = 'Invalid username or password';
    }
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
