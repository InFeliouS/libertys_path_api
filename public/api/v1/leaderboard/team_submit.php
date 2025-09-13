<?php
// public/api/v1/leaderboard/team_submit.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false, 'error'=>'Method not allowed']); exit;
}

$root = dirname(__DIR__, 4); // up to project root
require_once $root . '/vendor/autoload.php';

// load env
if (file_exists($root.'/.env')) {
  $dotenv = Dotenv\Dotenv::createImmutable($root);
  $dotenv->safeLoad();
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'libertys_path_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'error'=>'DB connection failed']); exit;
}

// Ensure table exists (safe if already created)
$pdo->exec("
CREATE TABLE IF NOT EXISTS leaderboard_team_runs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  player1_name VARCHAR(64) NOT NULL,
  player2_name VARCHAR(64) NOT NULL,
  score INT NOT NULL,
  time_left INT NOT NULL DEFAULT 0,
  correct INT NOT NULL DEFAULT 0,
  mistakes TINYINT UNSIGNED NOT NULL DEFAULT 0,
  perfect TINYINT UNSIGNED NOT NULL DEFAULT 0,
  section VARCHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_score (score, created_at),
  KEY idx_section_score (section, score, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Read JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['success'=>false, 'error'=>'Invalid JSON']); exit;
}

// Basic validation + defaults
$player1 = trim((string)($data['player1_name'] ?? ''));
$player2 = trim((string)($data['player2_name'] ?? ''));
$score   = (int)($data['score'] ?? 0);
$time    = (int)($data['time_left'] ?? 0);
$correct = (int)($data['correct'] ?? 0);
$mist    = (int)($data['mistakes'] ?? 0);
$perf    = (int)($data['perfect'] ?? 0);
$section = trim((string)($data['section'] ?? ''));

if ($player1 === '' || $player2 === '') {
  http_response_code(422);
  echo json_encode(['success'=>false, 'error'=>'Missing player names']); exit;
}

try {
  $stmt = $pdo->prepare("
    INSERT INTO leaderboard_team_runs
    (player1_name, player2_name, score, time_left, correct, mistakes, perfect, section)
    VALUES (:p1, :p2, :score, :time_left, :correct, :mistakes, :perfect, :section)
  ");
  $stmt->execute([
    ':p1'=>$player1, ':p2'=>$player2,
    ':score'=>$score, ':time_left'=>$time, ':correct'=>$correct,
    ':mistakes'=>$mist, ':perfect'=>$perf, ':section'=>($section!==''?$section:null)
  ]);
  echo json_encode(['success'=>true, 'message'=>'Saved']);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false, 'error'=>'Insert failed']);
}
