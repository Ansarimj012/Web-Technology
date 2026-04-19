<?php
require_once __DIR__ . '/includes/session.php';
$user    = auth_user();
$success = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Store – Shop Smart</title>
  <link rel="stylesheet" href="styles/shared.css">
  <link rel="stylesheet" href="styles/main.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo"><a href="index.php"><span class="brand">🛍️ Online Store</span></a></div>
  <div class="search-wrapper">
    <input type="text" id="search-input" placeholder="Search products, brands and more…">
    <span class="search-icon" onclick="handleSearchEnter()">🔍</span>
    <div id="search-dropdown"></div>
  </div>
  <div class="nav-right">
    <?php if ($user): ?>
      <span id="nav-user-name" style="color:white;font-weight:600;"><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
      <a href="php/logout.php" class="nav-btn" style="color:white;cursor:pointer;">Logout</a>
    <?php else: ?>
      <a href="login.php" id="nav-login-link">Login</a>
    <?php endif; ?>
    <a href="products.php">Products</a>
    <a href="cart.php" class="cart-link">🛒 Cart <span id="cart-badge">0</span></a>
  </div>
</nav>

<?php if ($success): ?>
<div style="background:#f0fff4;color:#276749;border-bottom:1px solid #9ae6b4;padding:12px 24px;font-size:14px;text-align:center;">
  ✅ <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<!-- HERO BANNER -->
<div class="banner-section">
  <div class="banner-card red">
    <h2>Best of boAt</h2><p>From ₹799</p><span>Valentine's Sale 🔥</span>
    <a href="products.php?category=electronics" class="banner-btn">Shop Now</a>
  </div>
  <div class="banner-card blue">
    <h2>Realme P4 Power 5G</h2><p>From ₹23,999</p><span>#1 Bestseller 🏆</span>
    <a href="products.php?category=electronics" class="banner-btn">Explore</a>
  </div>
  <div class="banner-card dark">
    <h2>Intel Core Ultra</h2><p>Up to 11x Faster</p><span>Special Offer ⚡</span>
    <a href="products.php?category=electronics" class="banner-btn">Buy Now</a>
  </div>
</div>

<!-- CATEGORIES -->
<div class="categories">
  <div class="category" onclick="filterCategory('all')"><div class="cat-icon">🏠</div><span>All</span></div>
  <div class="category" onclick="filterCategory('clothes')"><div class="cat-icon">👗</div><span>Fashion</span></div>
  <div class="category" onclick="filterCategory('electronics')"><div class="cat-icon">💻</div><span>Electronics</span></div>
  <div class="category" onclick="filterCategory('accessories')"><div class="cat-icon">⌚</div><span>Accessories</span></div>
  <div class="category" onclick="window.location.href='products.php?category=electronics'"><div class="cat-icon">📱</div><span>Mobiles</span></div>
  <div class="category"><div class="cat-icon">💄</div><span>Beauty</span></div>
  <div class="category"><div class="cat-icon">🏋️</div><span>Sports</span></div>
  <div class="category"><div class="cat-icon">🪑</div><span>Furniture</span></div>
  <div class="category"><div class="cat-icon">🧸</div><span>Toys</span></div>
</div>

<h2 class="section-title">🔥 Top Deals</h2>
<div class="product-grid" id="top-deals-grid"></div>

<h2 class="section-title" id="products-section-title">✨ All Products</h2>
<div class="product-grid" id="all-products-grid"></div>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-col">
      <h4>🛍️ Online Store</h4>
      <p style="font-size:13px;line-height:1.7;">Your one-stop destination for fashion, electronics, and more.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Categories</h4>
      <ul>
        <li><a href="products.php?category=electronics">Electronics</a></li>
        <li><a href="products.php?category=clothes">Clothes</a></li>
        <li><a href="products.php?category=accessories">Accessories</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <ul>
        <li><a href="#">Help Center</a></li>
        <li><a href="#">Return Policy</a></li>
        <li><a href="#">Track Order</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">© 2026 Online Store. All rights reserved.</div>
</footer>

<script src="js/store.js"></script>
<script>
  function productCard(p) {
    const discount = Math.round((1 - p.price / p.oldPrice) * 100);
    return `
      <div class="product-card">
        <span class="discount-badge">-${discount}%</span>
        <img src="${p.img}" alt="${p.name}" loading="lazy">
        <div class="rating">⭐ ${p.rating} <span>(${p.reviews.toLocaleString()})</span></div>
        <h4>${p.name}</h4>
        <div class="price">
          <span class="old-price">₹${p.oldPrice.toLocaleString()}</span>
          <span class="new-price">₹${p.price.toLocaleString()}</span>
        </div>
        <button class="add-to-cart-btn" onclick="Store.addToCart(${p.id})">Add to Cart 🛒</button>
      </div>`;
  }

  let currentFilter = 'all';
  function renderProducts(filter = 'all') {
    currentFilter = filter;
    const allGrid = document.getElementById('all-products-grid');
    const title   = document.getElementById('products-section-title');
    let products  = filter === 'all' ? Store.products : Store.products.filter(p => p.category === filter);
    allGrid.innerHTML = products.map(productCard).join('');
    title.textContent = filter === 'all' ? '✨ All Products' : `✨ ${filter.charAt(0).toUpperCase() + filter.slice(1)}`;
  }
  function filterCategory(cat) { renderProducts(cat); document.getElementById('products-section-title').scrollIntoView({ behavior: 'smooth' }); }
  function handleSearchEnter() {
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = `products.php?search=${encodeURIComponent(q)}`;
  }

  const topDeals = [...Store.products].sort((a, b) => (1 - a.price/a.oldPrice) - (1 - b.price/b.oldPrice)).slice(0,4).reverse();
  document.getElementById('top-deals-grid').innerHTML = topDeals.map(productCard).join('');
  renderProducts('all');
</script>
</body>
</html>
