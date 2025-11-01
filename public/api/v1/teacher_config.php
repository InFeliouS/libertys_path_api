<?php
// public/api/v1/teacher_config.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . '/../../../src/config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

try {
    $raw = file_get_contents('php://input') ?: '';
    $in = json_decode($raw, true);
    if (!is_array($in) || empty($in)) $in = $_REQUEST;

    // GET: unchanged (returns teacher's config)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (empty($_SESSION['teacher_id'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Teacher login required']);
            exit;
        }
        $teacherId = intval($_SESSION['teacher_id']);
        $stmt = $pdo->prepare("SELECT id, teacher_id, room_count, enemy_count, difficulty, created_at, updated_at FROM teacher_configs WHERE teacher_id=:tid LIMIT 1");
        $stmt->execute([':tid'=>$teacherId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['room_count']  = (int)$row['room_count'];
            $row['enemy_count'] = (int)$row['enemy_count'];
            $row['difficulty']  = (int)$row['difficulty'];
            echo json_encode(['success'=>true,'data'=>$row]);
        } else {
            echo json_encode(['success'=>true,'data'=>null,'message'=>'No config found']);
        }
        exit;
    }

    // POST: validate strictly (server-side) and create/update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_SESSION['teacher_id'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Teacher login required']);
            exit;
        }
        $teacherId = intval($_SESSION['teacher_id']);

        // required fields
        $roomCount  = isset($in['roomCount']) ? intval($in['roomCount']) : null;
        $enemyCount = isset($in['enemyCount']) ? intval($in['enemyCount']) : null;
        $difficulty = isset($in['difficulty']) ? intval($in['difficulty']) : null; // expects 0/1/2

        // basic presence check
        if ($roomCount === null || $enemyCount === null) {
            http_response_code(400);
            echo json_encode(['success'=>false,'error'=>'roomCount and enemyCount are required']);
            exit;
        }

        // server-side validation rules
        if ($roomCount < 1 || $roomCount > 8) {
            http_response_code(422);
            echo json_encode(['success'=>false,'error'=>'roomCount must be between 1 and 8']);
            exit;
        }
        if ($enemyCount < 1) {
            http_response_code(422);
            echo json_encode(['success'=>false,'error'=>'enemyCount must be at least 1']);
            exit;
        }
        if ($enemyCount > $roomCount) {
            http_response_code(422);
            echo json_encode(['success'=>false,'error'=>'enemyCount must not exceed roomCount']);
            exit;
        }
        // difficulty sanitize: accept 0/1/2 only, default to 1
        if (!is_int($difficulty) && !ctype_digit((string)$difficulty)) {
            $difficulty = 1;
        }
        $difficulty = intval($difficulty);
        if ($difficulty < 0 || $difficulty > 2) $difficulty = 1;

        // now safe to insert/update
        $sel = $pdo->prepare("SELECT id FROM teacher_configs WHERE teacher_id = :tid LIMIT 1");
        $sel->execute([':tid' => $teacherId]);
        $existing = $sel->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $upd = $pdo->prepare("UPDATE teacher_configs
                SET room_count = :r, enemy_count = :e, difficulty = :d, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id");
            $upd->execute([':r' => $roomCount, ':e' => $enemyCount, ':d' => $difficulty, ':id' => $existing['id']]);
            $id = intval($existing['id']);
        } else {
            $ins = $pdo->prepare("INSERT INTO teacher_configs (teacher_id, room_count, enemy_count, difficulty)
                VALUES (:t,:r,:e,:d)");
            $ins->execute([':t' => $teacherId, ':r' => $roomCount, ':e' => $enemyCount, ':d' => $difficulty]);
            $id = intval($pdo->lastInsertId());
        }

        // return saved row
        $fetch = $pdo->prepare("SELECT id, teacher_id, room_count, enemy_count, difficulty, created_at, updated_at FROM teacher_configs WHERE id = :id LIMIT 1");
        $fetch->execute([':id' => $id]);
        $row = $fetch->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['room_count']  = (int)$row['room_count'];
            $row['enemy_count'] = (int)$row['enemy_count'];
            $row['difficulty']  = (int)$row['difficulty'];
        }

        echo json_encode(['success'=>true,'data'=>$row,'message'=>'Saved']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Invalid method']);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
    exit;
}
