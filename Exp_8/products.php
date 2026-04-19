<?php
/**
 * products.php – Products listing page with filters, search and sorting.
 */
require_once __DIR__ . '/includes/session.php';

$user     = auth_user();
$category = htmlspecialchars($_GET['category'] ?? '');
$search   = htmlspecialchars($_GET['search'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products – Online Store</title>
  <link rel="stylesheet" href="styles/shared.css">
  <link rel="stylesheet" href="styles/product.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo"><a href="index.php"><span class="brand">🛍️ Online Store</span></a></div>
  <div class="search-wrapper">
    <input type="text" id="search-input" placeholder="Search products…"
      value="<?= htmlspecialchars($search) ?>">
    <span class="search-icon" onclick="handleSearchEnter()">🔍</span>
    <div id="search-dropdown"></div>
  </div>
  <div class="nav-right">
    <?php if ($user): ?>
      <span style="color:white;font-weight:600;"><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
      <a href="php/logout.php" class="nav-btn" style="color:white;">Logout</a>
    <?php else: ?>
      <a href="login.php" id="nav-login-link">Login</a>
    <?php endif; ?>
    <a href="products.php" style="color:white;font-weight:600;">Products</a>
    <a href="cart.php" class="cart-link">🛒 Cart <span id="cart-badge">0</span></a>
  </div>
</nav>

<!-- PAGE LAYOUT -->
<div class="products-layout">

  <!-- SIDEBAR FILTERS -->
  <aside class="sidebar">
    <h3>Filters</h3>

    <div class="filter-section">
      <h4>Category</h4>
      <label>
        <input type="radio" name="category" value="all"
          <?= ($category === '' || $category === 'all') ? 'checked' : '' ?>
          onchange="applyFilters()"> All
      </label>
      <label>
        <input type="radio" name="category" value="electronics"
          <?= $category === 'electronics' ? 'checked' : '' ?>
          onchange="applyFilters()"> Electronics
      </label>
      <label>
        <input type="radio" name="category" value="clothes"
          <?= $category === 'clothes' ? 'checked' : '' ?>
          onchange="applyFilters()"> Clothes
      </label>
      <label>
        <input type="radio" name="category" value="accessories"
          <?= $category === 'accessories' ? 'checked' : '' ?>
          onchange="applyFilters()"> Accessories
      </label>
    </div>

    <div class="filter-section">
      <h4>Price Range</h4>
      <input type="range" id="price-range" min="0" max="90000" value="90000"
        oninput="updatePriceLabel(); applyFilters()">
      <div class="price-label">Up to ₹<span id="price-val">90,000</span></div>
    </div>

    <div class="filter-section">
      <h4>Min Rating</h4>
      <select id="rating-filter" onchange="applyFilters()">
        <option value="0">All Ratings</option>
        <option value="3">3★ &amp; above</option>
        <option value="4">4★ &amp; above</option>
        <option value="4.5">4.5★ &amp; above</option>
      </select>
    </div>

    <div class="filter-section">
      <h4>Sort By</h4>
      <select id="sort-filter" onchange="applyFilters()">
        <option value="default">Relevance</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="rating">Top Rated</option>
        <option value="discount">Best Discount</option>
      </select>
    </div>

    <button class="clear-btn" onclick="clearFilters()">✕ Clear Filters</button>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="products-main">
    <div class="products-header">
      <h2 id="products-heading">
        <?php
          if ($search)   echo 'Results for "' . htmlspecialchars($search) . '"';
          elseif ($category && $category !== 'all') echo ucfirst(htmlspecialchars($category));
          else echo 'All Products';
        ?>
      </h2>
      <span id="products-count" style="color:#888;font-size:14px;"></span>
    </div>
    <div class="product-grid" id="products-grid">
      <div class="loading-msg">Loading products…</div>
    </div>
  </main>

</div>

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
  // Pass PHP values into JS
  const PHP_CATEGORY = <?= json_encode($category) ?>;
  const PHP_SEARCH   = <?= json_encode($search) ?>;

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

  function updatePriceLabel() {
    const val = parseInt(document.getElementById('price-range').value);
    document.getElementById('price-val').textContent = val.toLocaleString();
  }

  function applyFilters() {
    const category  = document.querySelector('input[name="category"]:checked').value;
    const maxPrice  = parseInt(document.getElementById('price-range').value);
    const minRating = parseFloat(document.getElementById('rating-filter').value);
    const sort      = document.getElementById('sort-filter').value;
    const searchQ   = PHP_SEARCH.toLowerCase() ||
                      document.getElementById('search-input').value.trim().toLowerCase();

    let results = Store.products.filter(p => {
      if (category !== 'all' && p.category !== category) return false;
      if (p.price > maxPrice)   return false;
      if (p.rating < minRating) return false;
      if (searchQ && !p.name.toLowerCase().includes(searchQ)) return false;
      return true;
    });

    if (sort === 'price-asc')  results.sort((a, b) => a.price - b.price);
    if (sort === 'price-desc') results.sort((a, b) => b.price - a.price);
    if (sort === 'rating')     results.sort((a, b) => b.rating - a.rating);
    if (sort === 'discount')   results.sort((a, b) => (1 - a.price/a.oldPrice) - (1 - b.price/b.oldPrice));

    const grid  = document.getElementById('products-grid');
    const count = document.getElementById('products-count');
    count.textContent = `${results.length} product${results.length !== 1 ? 's' : ''} found`;

    if (!results.length) {
      grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#aaa;font-size:16px;">😕 No products found. Try adjusting filters.</div>';
    } else {
      grid.innerHTML = results.map(productCard).join('');
    }
  }

  function clearFilters() {
    document.querySelector('input[name="category"][value="all"]').checked = true;
    document.getElementById('price-range').value  = 90000;
    document.getElementById('rating-filter').value = '0';
    document.getElementById('sort-filter').value   = 'default';
    updatePriceLabel();
    applyFilters();
  }

  function handleSearchEnter() {
    const q = document.getElementById('search-input').value.trim();
    if (q) window.location.href = `products.php?search=${encodeURIComponent(q)}`;
  }

  document.getElementById('search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') handleSearchEnter();
  });

  applyFilters();
</script>
</body>
</html>
