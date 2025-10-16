<?php
// src/auth/login_process.php
// (Session is already started in public/index.php)

// Load the database connection
require_once __DIR__ . '/../config/db.php';

// Helper: check if given columns exist on a table (works for MySQL/MariaDB)
function table_has_columns(PDO $pdo, string $table, array $columns): bool {
    try {
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` WHERE `Field` IN ($placeholders)");
        $stmt->execute($columns);
        $found = $stmt->rowCount();
        return $found === count($columns);
    } catch (Throwable $e) {
        return false;
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Ensure the PDO instance is available
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            throw new Exception("Database connection is not established.");
        }

        // Detect if teachers table has first/last name columns
        $hasNameCols = table_has_columns($pdo, 'teachers', ['first_name', 'last_name']);

        // Look up the teacher (include first/last name if present)
        $sql = "
            SELECT id, username, password, role
            " . ($hasNameCols ? ", first_name, last_name" : "") . "
            FROM teachers
            WHERE username = :username
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
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

            // === NEW: store first/last name when available ===
            $first = $hasNameCols ? (string)($user['first_name'] ?? '') : '';
            $last  = $hasNameCols ? (string)($user['last_name']  ?? '') : '';
            $_SESSION['teacher_first_name'] = $first;
            $_SESSION['teacher_last_name']  = $last;
            $_SESSION['teacher_full_name']  = trim($first . ' ' . $last);

            // === Also provide a structured payload (optional, used by guards) ===
            $_SESSION['auth'] = [
                'teacher_id' => $teacherId,
                'username'   => $user['username'],
                'role'       => $role,      // 'ADMIN' | 'TEACHER'
                'sections'   => $sections,  // [] for ADMIN = all
                'first_name' => $first,
                'last_name'  => $last,
                'full_name'  => $_SESSION['teacher_full_name'],
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
