<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$image = $data['image'] ?? '';
$category = $data['category'] ?? '';
$user_id = $data['user_id'] ?? null;

if (!$title || !$description || !$category || !$user_id) {
  echo json_encode(['error' => 'Missing required fields']);
  exit;
}

try {
    $stmt = $pdo->prepare("SELECT is_verified FROM admins WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    $status = ($user && $user['is_verified']) ? 'approved' : 'pending';

    $insert = $pdo->prepare("INSERT INTO deals (title, description, image, category, status, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$title, $description, $image, $category, $status, $user_id]);

    echo json_encode(['success' => true, 'status' => $status]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Submission failed', 'details' => $e->getMessage()]);
}
?>