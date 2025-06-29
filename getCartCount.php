<?php
session_start();

$cart = $_SESSION['cart'] ?? [];
$totalQuantity = 0;

foreach ($cart as $item) {
    $totalQuantity += $item['quantity'];
}

echo json_encode(['count' => $totalQuantity]);
