<?php
include 'connect.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: adminLogin.php");
  exit;
}

// Dashboard counts
$buyerCount = $conn->query("SELECT COUNT(*) AS count FROM buyers")->fetch_assoc()['count'];
$sellerCount = $conn->query("SELECT COUNT(*) AS count FROM sellers")->fetch_assoc()['count'];

// Fetch tables
$buyers = $conn->query("SELECT * FROM buyers");
$sellers = $conn->query("
  SELECT s.*, COUNT(p.id) AS product_count 
  FROM sellers s
  LEFT JOIN products p ON s.id = p.seller_id
  GROUP BY s.id
");
$products = $conn->query("
  SELECT p.*, s.name AS seller_name 
  FROM products p
  JOIN sellers s ON p.seller_id = s.id
");
$orders = $conn->query("
  SELECT o.*, b.name AS buyers_name, p.name AS products_name
  FROM orders o
  JOIN buyers b ON o.buyers_id = b.id
  JOIN products p ON o.products_id = p.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="adminDash.css"/>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h2>ADMIN DASHBOARD</h2>
      <nav>
        <ul>
          <li class="active">Dashboard</li>
          <li>Buyers</li>
          <li>Sellers</li>
          <li>Orders</li>
          <li>Products</li>
          <li>Logout</li>
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <header class="header">
        <h1>ADMIN DASHBOARD</h1>
        <button class="logout-btn" onclick="window.location.href='adminLogout.php'">Logout</button>
      </header>

      <section class="stats">
        <div class="card">
          <h2><?= $buyerCount ?></h2>
          <p>Buyers</p>
        </div>
        <div class="card">
          <h2><?= $sellerCount ?></h2>
          <p>Sellers</p>
        </div>
      </section>

      <section class="tables">

        <!-- Buyers Table -->
        <div class="table-container">
          <h3>Buyers</h3>
          <table>
            <thead>
              <tr><th>Name</th><th>Email</th><th>Signup Date</th></tr>
            </thead>
            <tbody>
              <?php while ($row = $buyers->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['signup_date']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Sellers Table -->
        <div class="table-container">
          <h3>Sellers</h3>
          <table>
            <thead>
              <tr><th>Name</th><th>Email</th><th>Signup Date</th><th>Products</th></tr>
            </thead>
            <tbody>
              <?php while ($row = $sellers->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['signup_date']) ?></td>
                <td><?= $row['product_count'] ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
          <h3>Orders</h3>
          <table>
            <thead>
              <tr><th>Buyer</th><th>Product</th><th>Order Date</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php while ($row = $orders->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['buyers_name']) ?></td>
                <td><?= htmlspecialchars($row['products_name']) ?></td>
                <td><?= htmlspecialchars($row['orders_date']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Products Table -->
        <div class="table-container">
          <h3>Products</h3>
          <table>
            <thead>
              <tr><th>Product</th><th>Seller</th><th>Upload Date</th></tr>
            </thead>
            <tbody>
              <?php while ($row = $products->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['sellers_name']) ?></td>
                <td><?= htmlspecialchars($row['upload_date']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </section>
    </main>
  </div>
  <script src="adminDash.js"></script>
</body>
</html>
