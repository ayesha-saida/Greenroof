<?php
session_start();
if (!isset($_SESSION['buyer_id'])) {
    header("Location: buyerLogin.php?redirect=checkOut.php");
    exit();
}

 // Your database connection file
$host = "localhost";
$username = "root";
$password = "";
$dbname = "user_accounts";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$buyer_id = $_SESSION['buyer_id'];
$product_id = 123; // This should come from your cart data
$order_date = date('Y-m-d');
$status = 'Pending';

$stmt = $conn->prepare("INSERT INTO orders (buyers_id, products_id, order_date, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $buyer_id, $product_id, $order_date, $status);

if ($stmt->execute()) {
    echo "Order inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Cart</title>
  <style>
    body { font-family: Arial, sans-serif; }
    h2, h3 { margin-top: 20px; }
    .product-grid { display: flex; gap: 20px; flex-wrap: wrap; }
    .product-card { width: 160px; border: 1px solid #ddd; border-radius: 6px; padding: 10px; text-align: center; background: #fff; }
    .add-to-cart { margin-top: 10px; padding: 6px 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .add-to-cart:hover { background-color: #218838; }
    #cart-container { margin-top: 30px; padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9; width: 100%; max-width: 500px; }
    .cart-item { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
    .cart-item img { width: 50px; height: 50px; margin-right: 10px; }
    .cart-item-details { flex: 1; }
    .cart-controls { display: flex; gap: 5px; }
    .qty-btn { padding: 2px 8px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
    .qty-btn:hover { background: #0056b3; }
    #checkout-btn { margin-top: 10px; padding: 10px 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
    #checkout-btn:hover { background-color: #0056b3; }
    #subtotal { margin-top: 10px; font-weight: bold; }
    .payment-card-list-greenroof span { cursor: pointer; margin: 10px; display: inline-block; padding: 5px; border-radius: 5px; transition: transform 0.2s; }
    .payment-card-list-greenroof span:hover { transform: scale(1.05); }
    .payment-icon { height: 50px; width: auto; }
    #invoice { display:none; border:1px solid #ccc; padding:15px; margin-top:20px; background:#fff; }
    #footer-buttons { display:none; margin-top:20px; }
    textarea { width: 100%; max-width: 500px; }
  </style>
</head>
<body>
  <h2>🛒 Checkout Page</h2>
  <p>Welcome, <?php echo $_SESSION['buyer_name']; ?>. Ready to complete your order.</p>
  <h3>📝 Order Details</h3>
  <label for="address">Delivery Address:</label><br>
  <textarea id="address" rows="3" placeholder="Enter your full delivery address"></textarea><br><br>
  <label for="delivery-time">Select Delivery Date & Time:</label><br>
  <input type="datetime-local" id="delivery-time"><br><br>
  <h2>🛒 Cart: <span id="cart-count">0</span></h2>
  <div id="cart-container">
    <h3>Your Cart</h3>
    <div id="cart-items"></div>
    <div id="subtotal"></div>
  </div>
  <h3>Select Payment Method</h3>
  <div class="payment-card-list-greenroof" id="payment-options">
    <span data-method="Cash on Delivery"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/dd7d3db1-047c-4e65-b89e-d710eb539976_BD-139-84.png" alt="cod"></span>
    <span data-method="Visa"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/27fcee2a-7768-48b2-b369-faf91829bf76_BD-140-84.png" alt="visa"></span>
    <span data-method="MasterCard"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/e369d9f9-eb41-428c-b0c2-07bd60ffdc6e_BD-63-48.png" alt="master-card"></span>
    <span data-method="bKash"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/dbfdbbea-19ca-4be1-9b8f-ecb1fabdc6f7_BD-145-86.png" alt="bKash"></span>
    <span data-method="Nagad"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/395e474e-f67e-4a29-9521-5bc693ca53df_BD-144-84.png" alt="nagad"></span>
    <span data-method="Rocket"><img class="payment-icon" src="https://img.lazcdn.com/us/domino/71587ea9-6e32-4728-b251-4513236a8ba5_BD-144-84.png" alt="rocket"></span>
  </div>
  <div id="invoice">
    <h3>🧾 Invoice Preview</h3>
    <p><strong>Name:</strong> <?php echo $_SESSION['buyer_name']; ?></p>
    <p><strong>Phone:</strong> <?php echo $_SESSION['buyer_phone'] ?? 'N/A'; ?></p>
    <p><strong>Address:</strong> <span id="invoice-address"></span></p>
    <p><strong>Delivery Time:</strong> <span id="invoice-delivery-time"></span></p>
    <p><strong>Payment Method:</strong> <span id="invoice-payment-method"></span></p>
    <div id="invoice-items"></div>
  </div>
  <div id="footer-buttons">
    <button onclick="cancelOrder()" style="background-color:#dc3545;color:white;padding:10px 16px;border:none;border-radius:4px;cursor:pointer;">Cancel</button>
    <button onclick="confirmOrder()" style="background-color:#28a745;color:white;padding:10px 16px;border:none;border-radius:4px;cursor:pointer;">Confirm Order</button>
  </div>
  <script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCountEl = document.getElementById('cart-count');
    const cartItemsEl = document.getElementById('cart-items');
    const subtotalEl = document.getElementById('subtotal');
    let selectedPaymentMethod = null;

    function updateCartDisplay() {
      let totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
      cartCountEl.textContent = totalCount;
      cartItemsEl.innerHTML = '';
      cart.forEach((item, index) => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `<img src="${item.img}" alt="${item.name}"><div class="cart-item-details"><strong>${item.name}</strong><br>${item.price.toFixed(2)} x ${item.quantity} = ${(item.price * item.quantity).toFixed(2)}<div class="cart-controls"><button class="qty-btn" onclick="changeQuantity(${index}, -1)">−</button><button class="qty-btn" onclick="changeQuantity(${index}, 1)">+</button></div></div>`;
        cartItemsEl.appendChild(cartItem);
      });
      const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
      subtotalEl.textContent = `Subtotal: ${total.toFixed(2)}`;
      localStorage.setItem('cart', JSON.stringify(cart));
    }

    function changeQuantity(index, change) {
      cart[index].quantity += change;
      if (cart[index].quantity <= 0) { cart.splice(index, 1); }
      updateCartDisplay();
    }

    document.querySelectorAll('#payment-options span').forEach(span => {
      span.addEventListener('click', () => {
        const address = document.getElementById('address').value.trim();
        const deliveryTime = document.getElementById('delivery-time').value;
        if (!address || !deliveryTime) {
          alert('Please enter address and select delivery time before choosing payment.');
          return;
        }
        if (cart.length === 0) {
          alert('Cart is empty!');
          return;
        }
        selectedPaymentMethod = span.getAttribute('data-method');
        document.getElementById('invoice-address').textContent = address;
        document.getElementById('invoice-delivery-time').textContent = new Date(deliveryTime).toLocaleString();
        document.getElementById('invoice-payment-method').textContent = selectedPaymentMethod;
        let invoiceHTML = '<ul>';
        cart.forEach(item => {
          invoiceHTML += `<li>${item.name} — ${item.quantity} × ${item.price.toFixed(2)} = ${(item.quantity * item.price).toFixed(2)}</li>`;
        });
        invoiceHTML += '</ul>';
        document.getElementById('invoice-items').innerHTML = invoiceHTML;
        document.getElementById('invoice').style.display = 'block';
        document.getElementById('footer-buttons').style.display = 'block';
      });
    });

    function cancelOrder() {
      if (confirm("Are you sure you want to cancel this order?")) {
        cart = [];
        localStorage.removeItem('cart');
        updateCartDisplay();
        alert('Order canceled.');
        window.location.reload();
      }
    }

    function confirmOrder() {
  if (!selectedPaymentMethod) {
    alert('Please select a payment method.');
    return;
  }

  const address = document.getElementById('address').value.trim();
  const deliveryTime = document.getElementById('delivery-time').value;

  if (!address || !deliveryTime) {
    alert('Address and delivery time are required.');
    return;
  }

  const orderData = {
    address: address,
    deliveryTime: deliveryTime,
    paymentMethod: selectedPaymentMethod,
    items: cart
  };

  fetch('submitOrder.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(orderData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      alert('✅ Order placed successfully! Order ID: ' + data.order_id);
      cart = [];
      localStorage.removeItem('cart');
      updateCartDisplay();
      document.getElementById('invoice').style.display = 'none';
      document.getElementById('footer-buttons').style.display = 'none';
    } else {
      alert('❌ Order failed: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.');
  });
}

    updateCartDisplay();
  </script>
</body>
</html>
