<?php
session_start();
$seller = $_SESSION['seller_name'] ?? '';
if (!$seller) die("Please log in.");

$mysqli = new mysqli("localhost", "root", "", "user_accounts");
$stmt = $mysqli->prepare("SELECT * FROM products WHERE seller_name = ?");
$stmt->bind_param("s", $seller);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>My Uploaded Products</h2>
  <a href="logout.php" class="btn btn-outline-secondary mb-3">Logout</a>
  <table class="table table-bordered">
    <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Category</th><th>Area</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><img src="<?= $row['image'] ?>" width="80" height="60"></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>Tk <?= number_format($row['price'], 2) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= htmlspecialchars($row['area']) ?></td>
          <td>
            <a href="editProduct.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="deleteProduct.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
