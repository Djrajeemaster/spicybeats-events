<?php
session_start();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $insert = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $insert->execute([$username, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: index.html');
            exit;
        }
    } else {
        $error = 'All fields are required';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
</head>
<body>
<h2>User Signup</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Username:<input type="text" name="username" required></label><br>
    <label>Password:<input type="password" name="password" required></label><br>
    <button type="submit">Sign Up</button>
</form>
</body>
</html>
