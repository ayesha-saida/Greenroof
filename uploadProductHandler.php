<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

// Basic validation
if (
  !isset($_POST['name'], $_POST['price'], $_POST['category'], $_POST['area'], $_POST['seller']) ||
  !isset($_FILES['image']) || $_FILES['image']['error'] !== 0
) {
  die("Invalid form submission.");
}

// Image validation
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

// Handle safe upload
$imageDir = "uploads/";
if (!is_dir($imageDir)) {
  mkdir($imageDir, 0777, true);
}

$extension = pathinfo($image['name'], PATHINFO_EXTENSION);
$uniqueName = uniqid('img_', true) . '.' . $extension;
$imagePath = $imageDir . $uniqueName;

if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
  die("Failed to save image.");
}

// Sanitize text inputs
$name = htmlspecialchars(trim($_POST['name']));
$price = (float) $_POST['price'];
$category = htmlspecialchars(trim($_POST['category']));
$area = htmlspecialchars(trim($_POST['area']));
$seller = htmlspecialchars(trim($_POST['seller']));

// Insert into DB
$stmt = $mysqli->prepare("INSERT INTO products (name, price, image, category, area, seller_name) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssss", $name, $price, $imagePath, $category, $area, $seller);

if ($stmt->execute()) {
  echo "<script>alert('Product uploaded successfully!'); window.location.href='home.php';</script>";
} else {
  echo "Database error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
