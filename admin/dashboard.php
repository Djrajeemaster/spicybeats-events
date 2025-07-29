<?php
session_start();  // Start the session to check if the user is logged in

// Check if the admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');  // If not logged in, redirect to the login page
    exit;
}

require_once __DIR__ . '/../config/db.php';  // Database connection

// Filtering & Sorting logic
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$perPage = 10; // Number of deals per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Base query for deals
$sql = "SELECT * FROM deals WHERE 1";
if ($categoryFilter) {
    $sql .= " AND category = :category";
}
if ($statusFilter) {
    $sql .= " AND status = :status";
}
$sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";

// Prepare and execute query
$stmt = $pdo->prepare($sql);
if ($categoryFilter) {
    $stmt->bindParam(':category', $categoryFilter);
}
if ($statusFilter) {
    $stmt->bindParam(':status', $statusFilter);
}
$stmt->execute();
$deals = $stmt->fetchAll();

// Pagination
$totalDealsQuery = "SELECT COUNT(*) FROM deals WHERE 1";
$stmt = $pdo->prepare($totalDealsQuery);
$stmt->execute();
$totalDeals = $stmt->fetchColumn();
$totalPages = ceil($totalDeals / $perPage);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .action-btn { margin-right: 8px; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .action-btn.approve { background-color: #28a745; color: white; }
    .action-btn.approve:hover { background-color: #218838; }
    .action-btn.reject { background-color: #dc3545; color: white; }
    .action-btn.reject:hover { background-color: #c82333; }
    .action-btn.delete { background-color: #ffc107; color: white; }
    .action-btn.delete:hover { background-color: #e0a800; }
    .pagination a { margin: 0 5px; text-decoration: none; color: blue; }
  </style>
</head>
<body>
  <h2>Admin Dashboard</h2>
  <p><a href="logout.php">Logout</a></p>

  <!-- Filters -->
  <form method="GET">
    <select name="category">
      <option value="">All Categories</option>
      <option value="food" <?php if ($categoryFilter == 'food') echo 'selected'; ?>>Food</option>
      <option value="electronics" <?php if ($categoryFilter == 'electronics') echo 'selected'; ?>>Electronics</option>
      <option value="clothing" <?php if ($categoryFilter == 'clothing') echo 'selected'; ?>>Clothing</option>
    </select>

    <select name="status">
      <option value="">All Statuses</option>
      <option value="pending" <?php if ($statusFilter == 'pending') echo 'selected'; ?>>Pending</option>
      <option value="approved" <?php if ($statusFilter == 'approved') echo 'selected'; ?>>Approved</option>
      <option value="rejected" <?php if ($statusFilter == 'rejected') echo 'selected'; ?>>Rejected</option>
    </select>

    <button type="submit">Filter</button>
  </form>

  <!-- Deals Table -->
  <table>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($deals as $deal): ?>
      <tr>
        <td><?= htmlspecialchars($deal['id']) ?></td>
        <td><?= htmlspecialchars($deal['title']) ?></td>
        <td><?= htmlspecialchars($deal['status']) ?></td>
        <td>
          <?php if ($deal['status'] === 'pending'): ?>
            <a class="action-btn approve" href="approve.php?id=<?= $deal['id'] ?>">Approve</a>
            <a class="action-btn reject" href="reject.php?id=<?= $deal['id'] ?>">Reject</a>
          <?php else: ?>
            <span>No action</span>
          <?php endif; ?>
          <a class="action-btn delete" href="delete.php?id=<?= $deal['id'] ?>" onclick="return confirm('Delete this deal?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <!-- Pagination Links -->
  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>&category=<?= $categoryFilter ?>&status=<?= $statusFilter ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</body>
</html>
