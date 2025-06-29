<?php
session_start();

// DB connection
$mysqli = new mysqli("localhost", "root", "", "user_accounts");
if ($mysqli->connect_error) {
  die("Database connection failed: " . $mysqli->connect_error);
}

// Fetch products
$result = $mysqli->query("SELECT * FROM products");
$products = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GreenRoof Home page</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="homeStyle.css" />
</head>

<body>
<header>
  <div class="top-header">
    <span><a href="home.php">Home</a></span>
    <span><a href="help&support.html">Help & Support</a></span>

    <?php if (isset($_SESSION['buyer_id'])): ?>
      <span>Welcome, <?php echo htmlspecialchars($_SESSION['buyer_name']); ?>!</span>
      <span><a href="buyerLogout.php">Logout</a></span>
    <?php else: ?>
      <span><a href="buyerLogin.php">Login</a></span>
    <?php endif; ?>

    <span><a href="sellerSignup.php">Seller</a></span>
    <span><a href="checkOut.php"> <i class="ri-shopping-cart-line"></i> </a></span>
    
  </div>

  <div class="middle-header">
    <h2 class="m-0">GreenRoof Market</h2>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search in GreenRoof" />
      <button onclick="applyFilters()">🔍</button>
    </div>

    <div class="col-md-1">
      <select class="form-select" id="categoryFilter">
        <option value="all">All Types</option>
        <option value="vegetables">Vegetables</option>
        <option value="leaf-vegetables">Leaf vegetables</option>
        <option value="fruits">Fruits</option>
        <option value="dairy">Dairy</option>
        <option value="dry-fruits-&-nuts">Dry Fruits & Nuts</option>
      </select>
    </div>

    <div class="col-md-1">
      <select class="form-select" id="areaFilter">
        <option value="">All Areas</option>
        <option value="banani">Banani</option>
        <option value="mohakhali">Mohakhali</option>
        <option value="gulshan">Gulshan</option>
        <option value="mirpur">Mirpur</option>
        <option value="uttara">Uttara</option>
        <option value="notun_bazar">Notun Bazar</option>
        <option value="mogbazar">Mogbazar</option>
        <option value="rampura">Rampura</option>
        <option value="bashaboo">Bashaboo</option>
        <option value="khilgaon">Khilgaon</option>
      </select>
    </div>
  </div>
</header>

<div class="carousel">
  <img src="image/hero_5.jpg" alt="Slide 1" />
  <img src="image/hero2.jpg" alt="Slide 2" />
  <img src="image/hero3.jpg" alt="Slide 3" />
</div>

<div class="headline">
  <h2>A rooftop garden crops selling website.</h2>
  <p>Choose fresh, local, and green! Rooftop gardening promotes healthy living and connects people to their food source.</p>
</div>

<div class="card-fs-content-header">
  <a href="#">Product List</a>
</div>

<div class="product-grid" id="product-grid">
  <?php foreach ($products as $product): ?>
    <div class="mode-card-hover"
         data-category="<?php echo htmlspecialchars($product['category']); ?>"
         data-area="<?php echo htmlspecialchars($product['area']); ?>"
         data-name="<?php echo strtolower(htmlspecialchars($product['name'])); ?>">

      <a href="#">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
        <div>
          <span class="product-name"><?php echo htmlspecialchars($product['name']); ?></span><br>
          <?php echo htmlspecialchars($product['price']); ?>tk
        </div>
      </a>

      <div class="cart-actions">
        <i class="ri-shopping-cart-line"></i>
        Cart: <span class="cart-count" id="cart-count-<?php echo strtolower($product['name']); ?>" data-name="<?php echo strtolower($product['name']); ?>">0</span><br>
        <button class="add-to-cart"
                onclick="addToCart('<?php echo htmlspecialchars($product['name']); ?>',
                                   <?php echo $product['price']; ?>,
                                   '<?php echo htmlspecialchars($product['image']); ?>')">
          Add to Cart
        </button>
        <p><small>Seller: <?php echo htmlspecialchars($product['seller_name']); ?></small></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="footer-first">
    <ul>
      <li>
        <h1>Customer Care</h1>
      </li>
      <li>
        <a href="helpCenter.html">Help Centre</a>
      </li>
      <li><a href="howToBuy.html">How to Buy</a></li>
      <li><a href="returnsAndRefunds.html">Returns and Refunds</a></li>
      <li><a href="contactUs.html">Contact Us</a></li>
      <li><a href="terms&Condition.html">Terms and Conditions</a></li>
    </ul>
    <div class="Izd-footer-h2">Payment Method</div>
        <div class="payment-card-list-farmcus">

          <span>

            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/dd7d3db1-047c-4e65-b89e-d710eb539976_BD-139-84.png" alt="cod"
              data-spm-anchor-id="a2a0e.tm80335411.0.i0.735212f7FAcxZ3">

          </span>

          <span>
            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/27fcee2a-7768-48b2-b369-faf91829bf76_BD-140-84.png" alt="visa"
              data-spm-anchor-id="a2a0e.tm80335411.0.i1.735212f7dgYUFt">
          </span>
          <span>
            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/e369d9f9-eb41-428c-b0c2-07bd60ffdc6e_BD-63-48.png"
              alt="master-card">
          </span>


          <span>
            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/dbfdbbea-19ca-4be1-9b8f-ecb1fabdc6f7_BD-145-86.png" alt="bKash">
          </span>
          <span>
            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/395e474e-f67e-4a29-9521-5bc693ca53df_BD-144-84.png" alt="nagad">
          </span>

          <span>
            <img class="payment-icon"
              src="https://img.lazcdn.com/us/domino/71587ea9-6e32-4728-b251-4513236a8ba5_BD-144-84.png" alt="rocket">
          </span>
          </div>
  </div>
  <footer class="bg-light text-center py-3 mt-4">
    <p class="mb-1">About | Contact | Terms</p>
    <p class="mb-0">copyright@ayeshasultana2025</p>
  </footer>

<!-- JavaScript -->
<script>
  
  function addToCart(name, price, img) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existing = cart.find(item => item.name === name);
    if (existing) {
      existing.quantity++;
    } else {
      cart.push({ name, price: parseFloat(price), img, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
  }

  function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    document.querySelectorAll('.cart-count[data-name]').forEach(span => {
      const name = span.dataset.name.toLowerCase();
      const item = cart.find(i => i.name.toLowerCase() === name);
      span.textContent = item ? item.quantity : 0;
    });
  }

  function applyFilters() {
    const searchInput = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const selectedCategory = document.getElementById('categoryFilter')?.value || 'all';
    const selectedArea = document.getElementById('areaFilter')?.value || '';

    const cards = document.querySelectorAll('.product-grid .mode-card-hover');

    cards.forEach(card => {
      const name = card.querySelector('.product-name').textContent.toLowerCase();
      const category = card.getAttribute('data-category');
      const area = card.getAttribute('data-area');

      const matchesSearch = name.includes(searchInput);
      const matchesCategory = selectedCategory === 'all' || category === selectedCategory;
      const matchesArea = selectedArea === '' || area === selectedArea;

      card.style.display = matchesSearch && matchesCategory && matchesArea ? 'block' : 'none';
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('searchInput')?.addEventListener('keyup', event => {
      if (event.key === 'Enter') applyFilters();
    });

    document.getElementById('categoryFilter')?.addEventListener('change', applyFilters);
    document.getElementById('areaFilter')?.addEventListener('change', applyFilters);

    updateCartDisplay();
    applyFilters();
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>