<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// DB connection
$conn = new mysqli('localhost', 'root', '', 'user_accounts');
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit();
}

// Get data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['items']) || !is_array($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

// Validate basic fields
$address = trim($data['address']);
$delivery_time = $data['deliveryTime'];
$payment_method = $data['paymentMethod'];
$items = $data['items'];

if (!$address || !$delivery_time || !$payment_method || empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    exit();
}

$conn->begin_transaction();

try {
    // Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, order_date, status, address, delivery_time, payment_method) VALUES (?, CURDATE(), 'Pending', ?, ?, ?)");
    $stmt->bind_param("isss", $buyer_id, $address, $delivery_time, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert items
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        // Assume you add product_id in the client-side cart
        if (!isset($item['product_id'], $item['quantity'], $item['price'])) {
            throw new Exception('Invalid item format');
        }

        $pid = (int)$item['product_id'];
        $qty = (int)$item['quantity'];
        $price = (float)$item['price'];

        $stmtItem->bind_param("iiid", $order_id, $pid, $qty, $price);
        $stmtItem->execute();
    }

    $stmtItem->close();
    $conn->commit();

    echo json_encode(['status' => 'success', 'order_id' => $order_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
