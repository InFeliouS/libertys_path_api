<?php
// src/dashboard/create_section.php

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect route
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ./index.php?r=login");
    exit;
}

// DB connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sectionName  = trim($_POST['section_name']      ?? '');
    $startYear    = trim($_POST['start_school_year'] ?? '');
    $endYear      = trim($_POST['end_school_year']   ?? '');
    $postedCode   = strtoupper(trim($_POST['section_code'] ?? ''));

    // Basic validation
    if (
        $sectionName === '' ||
        !preg_match('/^\d{4}$/', $startYear) ||
        !preg_match('/^\d{4}$/', $endYear)
    ) {
        header("Location: ./index.php?r=sections/create");
        exit;
    }

    // === helper: generate 4-letter code ===
    function generateCode() {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        // use random_int for better randomness
        for ($i = 0; $i < 4; $i++) {
            $code .= $letters[random_int(0, 25)];
        }
        return $code;
    }

    // === Decide on final $section_code ===
    // 1) If client posted a 4-letter A-Z code, try to use it (if unique).
    // 2) Otherwise generate until we find a unique one.
    $useCode = null;

    if (preg_match('/^[A-Z]{4}$/', $postedCode)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE section_code = ?");
        $check->execute([$postedCode]);
        $exists = (int)$check->fetchColumn();
        if ($exists === 0) {
            $useCode = $postedCode;
        }
        // if exists > 0, we'll fall back to generating a new one below
    }

    // If not set / not unique, generate until unique
    if ($useCode === null) {
        $attempts = 0;
        do {
            if ($attempts++ > 50) {
                // Extremely unlikely, but safety break to avoid infinite loop
                throw new RuntimeException('Unable to generate unique section code, please try again.');
            }
            $candidate = generateCode();
            $check = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE section_code = ?");
            $check->execute([$candidate]);
            $exists = (int)$check->fetchColumn();
        } while ($exists > 0);

        $useCode = $candidate;
    }

    // Insert new section (including section_code)
    $stmt = $pdo->prepare("
        INSERT INTO sections
            (section_name, start_school_year, end_school_year, section_code)
        VALUES
            (:section, :start, :end, :code)
    ");

    try {
        $stmt->execute([
            ':section' => $sectionName,
            ':start'   => $startYear,
            ':end'     => $endYear,
            ':code'    => $useCode
        ]);
    } catch (PDOException $e) {
        // If a rare race-condition caused duplicate key error, try once more:
        if ($e->getCode() === '23000') { // integrity constraint violation (MySQL)
            // regenerate once and retry insert
            $retryAttempts = 0;
            $inserted = false;
            while ($retryAttempts++ < 5 && !$inserted) {
                $candidate = generateCode();
                $check = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE section_code = ?");
                $check->execute([$candidate]);
                if ((int)$check->fetchColumn() === 0) {
                    $useCode = $candidate;
                    try {
                        $stmt->execute([
                            ':section' => $sectionName,
                            ':start'   => $startYear,
                            ':end'     => $endYear,
                            ':code'    => $useCode
                        ]);
                        $inserted = true;
                        break;
                    } catch (PDOException $e2) {
                        // keep trying
                    }
                }
            }
            if (!$inserted) {
                // give up with an error (you can handle this more gracefully)
                throw $e;
            }
        } else {
            throw $e;
        }
    }

    // On success, go back to dashboard
    header("Location: ./index.php?r=dashboard");
    exit;
}

// GET → show the form
include __DIR__ . '/../../public/html/create_section.html';
