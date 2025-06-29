<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $login = trim($_POST['phoneNumber_email']);
    $password = $_POST['password'];

    //  Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM sellers WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['seller_name'] = $user['name'];
            $_SESSION['seller_id'] = $user['id'];

            header("Location: sellerDashboard.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ No account found with that email or phone number.";
    }
    
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Login</title>
  <link rel="stylesheet" href="sellerLogin.css">
</head>

<body>
<div class="form-container">
    <h2>Seller Login</h2>
    <form action="sellerLogin.php" method="POST">
      <label for="phoneNumber_email">Phone Number or Email:</label>
      <input type="text" id="phoneNumber_email" name="phoneNumber_email" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <!-- This picks up ?redirect=cart.php or similar in URL -->
  <input type="hidden" name="redirect" value="<?php echo $_GET['redirect'] ?? ''; ?>">

      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="sellerSignup.php">Register here</a></p>
  </div>

</body>

</html>