<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $status = 'approved';
    $category = $_GET['category'] ?? null;

    $sql = "
        SELECT d.*, 
               COALESCE(SUM(CASE WHEN v.vote_type = 'up' THEN 1 
                                 WHEN v.vote_type = 'down' THEN -1 
                                 ELSE 0 END), 0) AS votes
        FROM deals d
        LEFT JOIN votes v ON d.id = v.deal_id
        WHERE d.status = ?
    ";
    $params = [$status];

    if ($category) {
        $sql .= " AND d.category = ?";
        $params[] = $category;
    }

    $sql .= " GROUP BY d.id ORDER BY d.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $deals = $stmt->fetchAll();

    echo json_encode($deals);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch deals', 'details' => $e->getMessage()]);
}
?>
