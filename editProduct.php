<?php
session_start();
if (!isset($_SESSION['seller_name'])) {
    header("Location: sellerLogin.php?redirect=editProduct.php");
    exit();
}


$seller = $_SESSION['seller_name'];
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    die("Invalid product ID.");
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch product details
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ? AND seller_name = ?");
$stmt->bind_param("is", $productId, $seller);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found or you don't have permission to edit it.");
}

$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $price = (float)$_POST['price'];
    $category = htmlspecialchars(trim($_POST['category']));
    $area = htmlspecialchars(trim($_POST['area']));
    $imagePath = $product['image']; // Keep existing image unless a new one is uploaded

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        $image = $_FILES['image'];
        $imageMimeType = mime_content_type($image['tmp_name']);
        $imageSize = $image['size'];

        if (!in_array($imageMimeType, $allowedTypes)) {
            die("Invalid image format. Only JPG, PNG, and WebP are allowed.");
        }

        if ($imageSize > $maxFileSize) {
            die("Image too large. Max 2MB allowed.");
        }

        $imageDir = "uploads/";
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        if (file_exists($product['image'])) {
            unlink($product['image']);
        }

        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('img_', true) . '.' . $extension;
        $imagePath = $imageDir . $uniqueName;

        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            die("Failed to save image.");
        }
    }

    $stmt = $mysqli->prepare("UPDATE products SET name = ?, price = ?, image = ?, category = ?, area = ? WHERE id = ?");
    $stmt->bind_param("sdsssi", $name, $price, $imagePath, $category, $area, $productId);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='uploadHistory.php';</script>";
    } else {
        echo "Database error: " . $stmt->error;
    }

    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    img.product-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }
  </style>
</head>
<body class="container py-5">
  <h2 class="mb-4">Edit Product</h2>

  <form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
      <label for="name" class="form-label">Product Name</label>
      <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required />
    </div>

    <div class="col-md-6">
      <label for="price" class="form-label">Price (Tk)</label>
      <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required />
    </div>

    <div class="col-md-6">
      <label for="category" class="form-label">Category</label>
      <select name="category" id="category" class="form-select" required>
        <option value="vegetables" <?= $product['category'] === 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
        <option value="leaf-vegetables" <?= $product['category'] === 'leaf-vegetables' ? 'selected' : '' ?>>Leaf Vegetables</option>
        <option value="fruits" <?= $product['category'] === 'fruits' ? 'selected' : '' ?>>Fruits</option>
        <option value="dairy" <?= $product['category'] === 'dairy' ? 'selected' : '' ?>>Dairy</option>
        <option value="dry-fruits-&-nuts" <?= $product['category'] === 'dry-fruits-&-nuts' ? 'selected' : '' ?>>Dry Fruits & Nuts</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="area" class="form-label">Area</label>
      <select name="area" id="area" class="form-select" required>
        <option value="banani" <?= $product['area'] === 'banani' ? 'selected' : '' ?>>Banani</option>
        <option value="mohakhali" <?= $product['area'] === 'mohakhali' ? 'selected' : '' ?>>Mohakhali</option>
        <option value="gulshan" <?= $product['area'] === 'gulshan' ? 'selected' : '' ?>>Gulshan</option>
        <option value="mirpur" <?= $product['area'] === 'mirpur' ? 'selected' : '' ?>>Mirpur</option>
        <option value="uttara" <?= $product['area'] === 'uttara' ? 'selected' : '' ?>>Uttara</option>
        <option value="notun_bazar" <?= $product['area'] === 'notun_bazar' ? 'selected' : '' ?>>Notun Bazar</option>
        <option value="mogbazar" <?= $product['area'] === 'mogbazar' ? 'selected' : '' ?>>Mogbazar</option>
        <option value="rampura" <?= $product['area'] === 'rampura' ? 'selected' : '' ?>>Rampura</option>
        <option value="bashaboo" <?= $product['area'] === 'bashaboo' ? 'selected' : '' ?>>Bashaboo</option>
        <option value="khilgaon" <?= $product['area'] === 'khilgaon' ? 'selected' : '' ?>>Khilgaon</option>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">Current Image</label><br>
      <img src="<?= $product['image'] ?>" class="product-img" alt="Current Product Image" />
    </div>

    <div class="col-md-6">
      <label for="image" class="form-label">Replace Image</label>
      <input type="file" name="image" id="image" class="form-control" accept="image/*" />
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary">Update Product</button>
    </div>
  </form>
</body>
</html>
