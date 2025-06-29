<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['seller_name'])) {
    header("Location: sellerLogin.php?redirect=sellerDashboard.php");
    exit();
}


// Simulate logged-in seller ID for now (replace with real session check)
$seller_id = $_SESSION['seller_id'] ?? 1;

// Get total sales and total orders
$salesQuery = $conn->query("
  SELECT SUM(p.price) AS total_sales, COUNT(*) AS total_orders
  FROM orders o
  JOIN products p ON o.products_id = p.id
  WHERE p.seller_id = $seller_id
");
$salesData = $salesQuery->fetch_assoc();

// Get active listings
$listingsQuery = $conn->query("SELECT COUNT(*) AS active_listings FROM products WHERE seller_id = $seller_id");
$listingsData = $listingsQuery->fetch_assoc();


$notificationsQuery = $conn->query("
  SELECT message FROM notifications 
  WHERE seller_id = $seller_id 
  ORDER BY created_at DESC 
  LIMIT 1
");
$notification = $notificationsQuery->fetch_assoc();

// Get latest notification (optional)
$notificationsQuery = $conn->query("
  SELECT message FROM notifications 
  WHERE seller_id = $seller_id 
  ORDER BY created_at DESC 
  LIMIT 1
");
$notification = $notificationsQuery->fetch_assoc();


// Get recent orders
$ordersQuery = $conn->query("
  SELECT o.id, o.order_date, b.name AS customer_name, p.price
  FROM orders o
  JOIN buyers b ON o.buyers_id = b.id
  JOIN products p ON o.products_id = p.id
  WHERE p.seller_id = $seller_id
  ORDER BY o.order_date DESC
  LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seller Dashboard</title>
  <link rel="stylesheet" href="sellerDashboard.css">
</head>

<body>
  <div class="dashboard">
    <h1>Seller Dashboard</h1>
    <span>Welcome, <?php echo htmlspecialchars($_SESSION['seller_name']); ?>!</span>

    <div class="summary-container">
      <div class="card">
        <h2><?= number_format($salesData['total_sales'] ?? 0, 2) ?></h2>
        <p>Total sales</p>
        <p><?= $salesData['total_orders'] ?? 0 ?> Orders</p>
      </div>
      <div class="card">
        <h2><?= $listingsData['active_listings'] ?? 0 ?></h2>
        <p>Active Listings</p>
      </div>
      <div class="card">
        <h3>Notifications</h3>
        <div class="notification">
          <strong>New order alert</strong><br />
          <?= $notification['message'] ?? 'No new notifications.' ?>
        </div>
      </div>
    </div>

    <div class="buttons">
      <form action="sellerUpload.php"><button type="submit">Upload Product</button></form>
      <form action="uploadHistory.php"><button type="submit">Product History</button></form>
      <form action="orders.php"><button type="submit">View Orders Received</button></form>
      <form action="sellerLogout.php"><button type="submit">Logout</button></form>
    </div>

    <div class="orders">
      <h2>Recent Orders</h2>
      <table>
        <thead>
          <tr>
            <th>Order</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $ordersQuery->fetch_assoc()) : ?>
            <tr>
              <td>#<?= $row['id'] ?></td>
              <td><?= $row['order_date'] ?></td>
              <td><?= $row['customer_name'] ?></td>
              <td>$<?= number_format($row['price'], 2) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
