<?php
session_start();
if (!isset($_SESSION['buyer_id'])) {
  header("Location: buyerLogin.php");
  exit;
}

$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
  die("DB Connection Failed");
}

$buyer_id = $_SESSION['buyer_id'];

// Fetch latest order info
$orderInfo = $mysqli->query("SELECT * FROM orders WHERE buyer_id = $buyer_id ORDER BY id DESC LIMIT 1")->fetch_assoc();
$orderDate = $orderInfo['order_date'];
$address = $orderInfo['address'];
$deliveryTime = $orderInfo['delivery_time'];
$status = $orderInfo['status'];

// Fetch all product IDs for the last placed order
$orderID = $orderInfo['id'];
$orders = $mysqli->query("SELECT * FROM orders WHERE buyer_id = $buyer_id AND order_date = '$orderDate'");

$productDetails = [];
$totalAmount = 0;

while ($order = $orders->fetch_assoc()) {
  $productId = $order['products_id'];
  $product = $mysqli->query("SELECT * FROM products WHERE id = $productId")->fetch_assoc();
  $productDetails[] = $product;
  $totalAmount += $product['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 bg-white p-4 shadow">
  <h2 class="mb-4">🧾 Order Summary</h2>

  <p><strong>Customer:</strong> <?php echo htmlspecialchars($_SESSION['buyer_name']); ?></p>
  <p><strong>Order Date:</strong> <?php echo $orderDate; ?></p>
  <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($address); ?></p>
  <p><strong>Delivery Time:</strong> <?php echo htmlspecialchars($deliveryTime); ?></p>
  <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>

  <h4 class="mt-4">📦 Ordered Products:</h4>
  <ul class="list-group mb-3">
    <?php foreach ($productDetails as $product): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?php echo htmlspecialchars($product['name']); ?>
        <span><?php echo number_format($product['price'], 2); ?>tk</span>
      </li>
    <?php endforeach; ?>
  </ul>

  <h5 class="text-end">Total: <strong><?php echo number_format($totalAmount, 2); ?>tk</strong></h5>
  <a href="home.php" class="btn btn-primary mt-3">Back to Home</a>
</div>
</body>
</html>
