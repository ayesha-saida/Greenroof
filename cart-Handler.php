<?php
session_start();

header('Content-Type: application/json');

// Require login
if (!isset($_SESSION['buyer_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in to add to cart.']);
    exit;
}

// Read POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['price']) || !isset($data['img'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing product data']);
    exit;
}

$name = $data['name'];
$price = (float)$data['price'];
$img = $data['img'];

// Initialize session cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update item
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['name'] === $name) {
        $item['quantity'] += 1;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = [
        'name' => $name,
        'price' => $price,
        'img' => $img,
        'quantity' => 1
    ];
}

echo json_encode(['success' => true, 'message' => 'Item added to cart']);
