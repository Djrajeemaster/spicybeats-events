<?php
session_start();  // Start the session to store login status

$admin_user = 'admin';
$admin_pass = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if username and password match the admin credentials
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;  // Set session variable for admin login
        
        // Use a relative path here to avoid extra `/admin/`
        header('Location: dashboard.php');  // Relative path
        exit;
    } else {
        $error = 'Invalid credentials';  // Display error message for invalid credentials
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    input { margin: 8px 0; padding: 8px; width: 200px; }
    button { padding: 8px 16px; }
  </style>
</head>
<body>
  <h2>Admin Login</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required /><br>
    <label>Password:</label><br>
    <input type="password" name="password" required /><br>
    <button type="submit">Login</button>
  </form>
</body>
</html>
