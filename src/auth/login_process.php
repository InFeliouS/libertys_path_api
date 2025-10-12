<?php
// src/auth/login_process.php
// (Session is already started in public/index.php)

// Load the database connection
require_once __DIR__ . '/../config/db.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Ensure the PDO instance is available
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            throw new Exception("Database connection is not established.");
        }

        // Look up the teacher (now also fetch role)
        $stmt = $pdo->prepare("
            SELECT id, username, password, role
            FROM teachers
            WHERE username = :username
            LIMIT 1
        ");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify credentials
        if ($user && password_verify($password, $user['password'])) {
            $teacherId = (int)$user['id'];
            $role      = strtoupper((string)($user['role'] ?? 'TEACHER')); // 'ADMIN' | 'TEACHER'

            // Load assigned sections for TEACHER role (Admin = all → store empty array)
            $sections = [];
            if ($role !== 'ADMIN') {
                $s = $pdo->prepare("
                    SELECT section_id
                    FROM teacher_sections
                    WHERE teacher_id = :tid
                    ORDER BY section_id
                ");
                $s->bindValue(':tid', $teacherId, PDO::PARAM_INT);
                $s->execute();
                $sections = array_map('intval', $s->fetchAll(PDO::FETCH_COLUMN));
            }

            // === Keep your original session vars (for compatibility) ===
            $_SESSION['teacher_id']       = $teacherId;
            $_SESSION['teacher_username'] = $user['username'];

            // === Add role/sections (simple keys) ===
            $_SESSION['role']     = $role;
            $_SESSION['sections'] = $sections;

            // === Also provide a structured payload (optional, used by guards) ===
            $_SESSION['auth'] = [
                'teacher_id' => $teacherId,
                'username'   => $user['username'],
                'role'       => $role,      // 'ADMIN' | 'TEACHER'
                'sections'   => $sections,  // [] for ADMIN = all
            ];
            // Optional legacy mirror
            $_SESSION['user'] = [
                'username' => $user['username'],
                'role'     => $role,
            ];

            // Redirect to dashboard via front-controller (exactly like your original)
            header("Location: ./index.php?r=dashboard");
            exit;
        } else {
            echo "Invalid username or password!";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Optionally close the connection
$pdo = null;
