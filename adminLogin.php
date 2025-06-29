<?php
session_start();
include 'connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: adminDashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <style>
    <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }

 body {
  background: #f4f4f4;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  padding: 0 16px; /* adds breathing room on mobile screens */
}

  form {
  background: white;
  padding: 30px 25px;
  padding-right: 47px;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  box-sizing: border-box;
}

  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
  }

  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }

  input[type="submit"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    background-color: #2d2d2d;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  input[type="submit"]:hover {
    background-color: #444;
  }

  .error {
    color: red;
    margin-bottom: 15px;
    text-align: center;
    font-size: 0.9em;
  }

@media (max-width: 500px) {
  form {
    padding: 20px 20px; /* Slightly tighter on mobile */
  }

  h2 {
    font-size: 1.4em;
  }
}
</style>
</head>
<body>
  <form method="post">
    <h2>Admin Login</h2>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="submit" value="Login" />
  </form>
</body>
</html>
