<?php
session_start();
if (!isset($_SESSION['seller_name'])) {
    header("Location: sellerLogin.php?redirect= orders.php");
    exit();
}

$seller_name = $_SESSION['seller_name'];

// Connect to DB
$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Total orders received
$stmtTotal = $mysqli->prepare("SELECT COUNT(*) AS total FROM orders 
JOIN products ON orders.products_id = products.id 
WHERE products.seller_name = ?");
$stmtTotal->bind_param("s", $seller_name);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result()->fetch_assoc();
$totalOrders = $totalResult['total'];
$stmtTotal->close();

// Total delivered
$stmtDelivered = $mysqli->prepare("SELECT COUNT(*) AS delivered FROM orders 
JOIN products ON orders.products_id = products.id 
WHERE products.seller_name = ? AND orders.status = 'delivered'");
$stmtDelivered->bind_param("s", $seller_name);
$stmtDelivered->execute();
$deliveredResult = $stmtDelivered->get_result()->fetch_assoc();
$deliveredOrders = $deliveredResult['delivered'];
$stmtDelivered->close();

// Fetch all orders
$stmtOrders = $mysqli->prepare("SELECT orders.id, buyers_id, products.name AS product_name, order_date, status 
FROM orders 
JOIN products ON orders.products_id = products.id 
WHERE products.seller_name = ? 
ORDER BY order_date DESC");
$stmtOrders->bind_param("s", $seller_name);
$stmtOrders->execute();
$ordersResult = $stmtOrders->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders Received</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    color: #333;
  }

  h2 {
    text-align: center;
    color: #4caf50;
  }

  .mb-4 {
    background-color: #e8f5e9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
  }

  .mb-4 p {
    margin: 0;
    font-size: 1.1rem;
  }

  table {
    background-color: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  th, td {
    text-align: center;
    vertical-align: middle !important;
  }

  .table th {
    background-color: #4caf50;
    color: white;
  }

  .table td {
    padding: 12px;
  }

  @media (max-width: 768px) {
    .mb-4 {
      text-align: center;
    }

    table {
      font-size: 0.9rem;
    }
  }
</style>

</head>
<body class="container py-5">

  <h2 class="mb-4">Orders Received</h2>

  <div class="mb-4">
    <p><strong>Total Orders Received:</strong> <?= $totalOrders ?></p>
    <p><strong>Delivered Orders:</strong> <?= $deliveredOrders ?></p>
    <p><strong>Pending Orders:</strong> <?= $totalOrders - $deliveredOrders ?></p>
  </div>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Order ID</th>
        <th>Product Name</th>
        <th>Buyer ID</th>
        <th>Order Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $ordersResult->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= $row['buyers_id'] ?></td>
          <td><?= $row['order_date'] ?></td>
          <td><?= ucfirst($row['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>

<?php $mysqli->close(); ?>
