<?php
// src/utils/student_helpers.php

function insert_attempt_rows_if_missing(PDO $pdo, int $student_id): void
{
    $tables = [
        'first_qpr_attempts',
        'second_qpr_attempts',
        'third_qpr_attempts',
        'fourth_qpr_attempts'
    ];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT 1 FROM $table WHERE student_id = ?");
        $stmt->execute([$student_id]);

        if ($stmt->rowCount() === 0) {
            $insert = $pdo->prepare("INSERT INTO $table (student_id) VALUES (?)");
            $insert->execute([$student_id]);
        }
    }
}
