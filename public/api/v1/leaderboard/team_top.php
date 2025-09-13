<?php
// public/api/v1/leaderboard/team_top.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$root = dirname(__DIR__, 4);
require_once $root . '/vendor/autoload.php';

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

$limit   = max(1, (int)($_GET['limit'] ?? 50));
$section = trim((string)($_GET['section'] ?? 'all'));
$perfect = isset($_GET['perfect']) ? 1 : 0;

$where = [];
$params = [];
if ($section !== '' && strtolower($section) !== 'all') {
  $where[] = "section = :section";
  $params[':section'] = $section;
}
if ($perfect) {
  $where[] = "perfect = 1";
}
$whereSql = $where ? ("WHERE ".implode(" AND ", $where)) : "";

$sql = "
  SELECT
    (@r:=@r+1) AS rank,
    t.*
  FROM (
    SELECT id, player1_name, player2_name, score, time_left, correct, mistakes, perfect, section, created_at
    FROM leaderboard_team_runs
    $whereSql
    ORDER BY score DESC, created_at ASC
    LIMIT :limit
  ) t, (SELECT @r:=0) r
";
$stmt = $pdo->prepare($sql);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll();
echo json_encode(['success'=>true, 'data'=>['items'=>$items]]);
