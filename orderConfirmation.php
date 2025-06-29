<?php
session_start();

// Optional: prevent direct access if no recent order
if (!isset($_SESSION['buyer_id'])) {
    header("Location: buyerLogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      text-align: center;
      background-color: #f0fff0;
    }
    h1 {
      color: #28a745;
    }
    .message {
      margin-top: 20px;
      font-size: 1.2em;
    }
    .home-link {
      margin-top: 30px;
      display: inline-block;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .home-link:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <h1>✅ Thank You for Your Order!</h1>
  <div class="message">
    Hi <strong><?php echo htmlspecialchars($_SESSION['buyer_name']); ?></strong>, your order has been placed successfully.<br>
    We will deliver it to your provided address shortly.
  </div>

  <a href="home.php" class="home-link">Return to Home</a>

</body>
</html>
