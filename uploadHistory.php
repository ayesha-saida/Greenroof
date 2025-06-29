<?php
session_start();
if (!isset($_SESSION['seller_name'])) {
    header("Location: sellerLogin.php?redirect=uploadHistory.php");
    exit();
}

// For testing only — remove this when login works properly
// $_SESSION['seller_name'] = 'demo_seller';


$seller = $_SESSION['seller_name'];

// ✅ Database connection (PDO only)
$host = 'localhost';
$db   = 'user_accounts';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ✅ Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_name = ?");
    $stmt->execute([$id, $seller]);
    header("Location: uploadHistory.php");
    exit();
}

// ✅ Handle filters
$categoryFilter = $_GET['categoryFilter'] ?? '';
$areaFilter = $_GET['areaFilter'] ?? '';

$query = "SELECT * FROM products WHERE seller_name = ?";
$params = [$seller];

if (!empty($categoryFilter)) {
    $query .= " AND categoryFilter = ?";
    $params[] = $categoryFilter;
}
if (!empty($areaFilter)) {
    $query .= " AND areaFilter = ?";
    $params[] = $areaFilter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload History</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    img.product-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
  </style>
</head>
<body class="container py-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Upload History</h2>
    <div>
      <a href="sellerUpload.php" class="btn btn-primary me-2">Upload New</a>
      <a href="sellerLogout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <!-- Filter Form -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <option value="vegetables" <?= $categoryFilter == 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
        <option value="leaf-vegetables" <?= $categoryFilter == 'leaf-vegetables' ? 'selected' : '' ?>>Leaf Vegetables</option>
        <option value="fruits" <?= $categoryFilter == 'fruits' ? 'selected' : '' ?>>Fruits</option>
        <option value="dairy" <?= $categoryFilter == 'dairy' ? 'selected' : '' ?>>Dairy</option>
        <option value="dry-fruits-&-nuts" <?= $categoryFilter == 'dry-fruits-&-nuts' ? 'selected' : '' ?>>Dry Fruits & Nuts</option>
      </select>
    </div>

    <div class="col-sm-6 col-md-4">
      <select name="area" class="form-select">
        <option value="">All Areas</option>
        <?php
        $areas = ['banani', 'mohakhali', 'gulshan', 'mirpur' , 'uttara', 'notun_bazar', 'mogbazar', 'rampura', 'bashaboo', 'khilgaon'];
        foreach ($areas as $area) {
            $selected = $areaFilter == $area ? 'selected' : '';
            echo "<option value=\"$area\" $selected>" . ucfirst(str_replace('_', ' ', $area)) . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-4 d-grid d-md-block">
      <button type="submit" class="btn btn-success me-2">Filter</button>
      <a href="uploadHistory.php" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  <?php if (count($products) > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price (Tk)</th>
            <th>Category</th>
            <th>Area</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
            <tr>
              <td><img src="<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="Product Image"></td>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td><?= number_format($product['price'], 2) ?></td>
              <td><?= htmlspecialchars($product['category']) ?></td>
              <td><?= htmlspecialchars($product['area']) ?></td>
              <td>
                <a href="editProduct.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="uploadHistory.php?delete=<?= $product['id'] ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-muted">No products found.</p>
  <?php endif; ?>

</body>
</html>
