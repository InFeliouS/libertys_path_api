<?php
/** strict JSON array endpoint for Unity */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
// If you’ll call from WebGL later, uncomment next line:
// header('Access-Control-Allow-Origin: *');

try {
    // Adjust this require to your actual config path
    $root = dirname(__DIR__, 4); // /public/api/v1/guard_questions -> project root
    require_once $root . '/src/config/db.php'; // must define $pdo (PDO)

    $sql = "SELECT question_text, choice1, choice2, choice3, choice4, correct_index
            FROM guard_questions
            ORDER BY RAND() LIMIT 50";
    $stmt = $pdo->query($sql);
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    $items = [];
    foreach ($rows as $r) {
        $items[] = [
            'prompt'       => (string)$r['question_text'],
            'choices'      => [
                (string)$r['choice1'],
                (string)$r['choice2'],
                (string)$r['choice3'],
                (string)$r['choice4']
            ],
            'correctIndex' => (int)$r['correct_index']
        ];
    }

    echo json_encode([
        'success' => true,
        'data'    => ['items' => $items],
        'error'   => null
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data'    => ['items' => []],
        'error'   => 'DB error'
    ], JSON_UNESCAPED_UNICODE);
}
