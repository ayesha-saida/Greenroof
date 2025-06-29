<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

$cart = $data['cart'];
$paymentMethod = $data['payment_method'];
$address = $data['address'];
$deliveryTime = $data['delivery_time'];

if (!isset($_SESSION['buyer_id'])) {
  http_response_code(403);
  echo "User not logged in.";
  exit;
}

$buyer_id = $_SESSION['buyer_id'];

$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
  http_response_code(500);
  echo "Database connection failed.";
  exit;
}

foreach ($cart as $item) {
  $product_id = $item['id'];

  $stmt = $mysqli->prepare("INSERT INTO orders (buyer_id, products_id, order_date, status, address, delivery_time) VALUES (?, ?, CURDATE(), 'Pending', ?, ?)");
  $stmt->bind_param("iiss", $buyer_id, $product_id, $address, $deliveryTime);
  $stmt->execute();
}

echo "Order placed successfully!";
?>
