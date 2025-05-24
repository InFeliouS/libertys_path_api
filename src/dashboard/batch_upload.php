<?php
// Include database connection
require_once __DIR__ . '/../config/db.php'; // Adjust if needed

function generateUsername($givenName, $lastName, $studentId) {
    return strtolower(substr($givenName, 0, 1)) . str_replace(' ', '', strtolower($lastName)) . $studentId;
}

function generatePassword($givenName, $middleName, $studentId) {
    $src = trim($middleName) !== '' ? $middleName : $givenName;
    $src = str_replace(' ', '', strtolower($src));
    return ucfirst(substr($src, 0, 1)) . substr($src, 1) . $studentId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle);
        $results = [];

        while (($data = fgetcsv($handle)) !== false) {
            // Map CSV columns to variables
            $givenName = $data[0] ?? '';
            $middleName = $data[1] ?? '';
            $lastName = $data[2] ?? '';
            $sectionName = $data[3] ?? '';
            $birthSex = $data[4] ?? '';

            if (!$givenName || !$lastName || !$sectionName || !in_array($birthSex, ['Male', 'Female'])) {
                $results[] = ['status' => 'error', 'message' => "Invalid data: " . implode(', ', $data)];
                continue;
            }

            // Check if the section already exists
            $stmt = $pdo->prepare("SELECT id FROM sections WHERE section_name = ?");
            $stmt->execute([$sectionName]);
            $section = $stmt->fetch();

            // If section doesn't exist, create it
            if (!$section) {
                try {
                    $pdo->beginTransaction();

                    // Insert the new section
                    $stmt = $pdo->prepare("INSERT INTO sections (section_name, start_school_year, end_school_year) VALUES (?, ?, ?)");
                    $stmt->execute([$sectionName, 2025, 2026]); // Set school year accordingly

                    // Fetch the newly created section ID
                    $sectionId = $pdo->lastInsertId();

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $results[] = ['status' => 'error', 'message' => 'Error inserting section: ' . $e->getMessage()];
                    continue;
                }
            } else {
                $sectionId = $section['id']; // Use existing section
            }

            try {
                $pdo->beginTransaction();

                // Insert student
                $stmt = $pdo->prepare("INSERT INTO students (given_name, middle_name, last_name, section_id, birth_sex) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$givenName, $middleName ?: null, $lastName, $sectionId, $birthSex]);
                $studentId = $pdo->lastInsertId();

                // Generate username and password
                $username = generateUsername($givenName, $lastName, $studentId);
                $passwordPlain = generatePassword($givenName, $middleName, $studentId);
                $passwordHash = password_hash($passwordPlain, PASSWORD_BCRYPT);

                // Insert student_account
                $stmt = $pdo->prepare("INSERT INTO student_accounts (student_id, username, password) VALUES (?, ?, ?)");
                $stmt->execute([$studentId, $username, $passwordHash]);

                // Insert student_progress default
                $stmt = $pdo->prepare("INSERT INTO student_progress (student_id) VALUES (?)");
                $stmt->execute([$studentId]);

                $pdo->commit();

                $results[] = ['status' => 'success', 'student_id' => $studentId, 'username' => $username, 'password' => $passwordPlain];
            } catch (Exception $e) {
                $pdo->rollBack();
                $results[] = ['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()];
            }
        }

        fclose($handle);

        // Output results as CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="batch_upload_results.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student ID', 'Username', 'Password', 'Status', 'Message']);
        foreach ($results as $r) {
            fputcsv($output, [
                $r['student_id'] ?? '',
                $r['username'] ?? '',
                $r['password'] ?? '',
                $r['status'],
                $r['message'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    } else {
        die('Failed to open uploaded file.');
    }
} else {
    echo 'No file uploaded';
}
