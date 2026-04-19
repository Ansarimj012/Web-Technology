<?php
/**
 * admin/products.php
 * Admin panel – List, Search, and manage all products from the DB.
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Database.php';

$success = flash_get('success');
$error   = flash_get('error');

// ── Fetch all products from DB ────────────────────────────────────────────────
$search   = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

try {
    $pdo = Database::pdo();
    $sql = 'SELECT id, name, price, old_price, image_path, rating, reviews,
                   category, stock, is_active, created_at
              FROM products';
    $conditions = [];
    $params     = [];

    if ($search) {
        $conditions[] = 'name LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    if ($category && $category !== 'all') {
        $conditions[] = 'category = :cat';
        $params[':cat'] = $category;
    }
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Count per category for sidebar stats
    $statsStmt = $pdo->query('SELECT category, COUNT(*) as cnt FROM products GROUP BY category');
    $stats = [];
    foreach ($statsStmt->fetchAll() as $row) {
        $stats[$row['category']] = $row['cnt'];
    }
    $totalStmt = $pdo->query('SELECT COUNT(*) FROM products');
    $totalProducts = $totalStmt->fetchColumn();

} catch (Exception $e) {
    $products = [];
    $error = 'Database error: ' . $e->getMessage();
    $stats = [];
    $totalProducts = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Admin – Online Store</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
  --bg:        #ffffff;   /* main dark background */
  --surface:   #ffffff;   /* cards / panels */
  --surface2:  #60d0e4;   /* hover / secondary */
  --border:    #000000;   /* borders */

  --accent:    #2b62bb;   /* blue primary */
  --accent2:   #10b981;   /* emerald success */
  --danger:    #ef4444;   /* red delete */
  --warn:      #f59e0b;   /* orange warning */

  --text:      #000000;   /* main text */
  --muted:     #000000;   /* secondary text */

  --radius:    14px;
}
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { 
  
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  font-weight: 700;
}

    /* ── TOPBAR ─────────────────────────────────────── */
.topbar {
  background: #9ae6b4;
  border-bottom: 1px solid #7fd69d;
  padding: 0 32px;
  height: 62px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
}
    .topbar-brand { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
    .topbar-brand span { color: var(--accent); }
    .topbar-right { display: flex; align-items: center; gap: 14px; }
    .topbar-link { color: var(--muted); text-decoration: none; font-size: 13px; transition: color .2s; }
    .topbar-link:hover { color: var(--text); }

    /* ── LAYOUT ─────────────────────────────────────── */
    .layout { display: grid; grid-template-columns: 220px 1fr; min-height: calc(100vh - 62px); }

    /* ── SIDEBAR ─────────────────────────────────────── */
    .sidebar {
      background: var(--surface);
      border-right: 1px solid var(--border);
      padding: 28px 18px;
    }
    .sidebar-section { margin-bottom: 28px; }
    .sidebar-label { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); margin-bottom: 10px; padding-left: 10px; }
    .sidebar-item {
      display: flex; align-items: center; justify-content: space-between;
      padding: 9px 12px; border-radius: 8px; font-size: 18px; font-weight: 500;
      color: var(--muted); text-decoration: none; cursor: pointer; transition: all .15s;
      margin-bottom: 2px;
    }
    .sidebar-item:hover { background: var(--surface2); color: var(--text); }
    .sidebar-item.active { background: rgba(141, 182, 182, 0.15); color: var(--accent); }
    .sidebar-item .cat-badge {
      background: var(--surface2); color: var(--muted);
      font-size: 15px; font-weight: 700; padding: 2px 7px; border-radius: 20px;
    }
    .sidebar-item.active .cat-badge { background: rgba(108,99,255,.2); color: var(--accent); }
    .stat-block {
      background: var(--surface2); border-radius: var(--radius); padding: 14px 16px;
      border: 1px solid var(--border); margin-bottom: 8px;
    }
    .stat-block .stat-val { font-family: 'Syne', sans-serif; font-size: 30px; font-weight: 800; }
    .stat-block .stat-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

    /* ── MAIN ─────────────────────────────────────── */
    .main { padding: 32px; overflow-x: auto; }
    .main-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
    .page-title { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 800; }
    .page-title span { color: var(--accent); }

    /* ── TOOLBAR ─────────────────────────────────────── */
    .toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-box {
      display: flex; align-items: center; gap: 8px;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 8px; padding: 0 14px; height: 38px; min-width: 240px;
    }
    .search-box input {
      background: none; border: none; outline: none; font-family: 'DM Sans', sans-serif;
      font-size: 13px; color: var(--text); width: 100%;
    }
    .search-box input::placeholder { color: var(--muted); }
    .search-box .search-icon { color: var(--muted); font-size: 14px; }

    .btn {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 0 18px; height: 38px; border-radius: 8px;
      font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 600;
      cursor: pointer; border: none; text-decoration: none; transition: all .18s;
    }
    .btn-primary { background: var(--accent); color: white; }
    .btn-primary:hover { background: #5a52e0; transform: translateY(-1px); }
    .btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--surface2); }
    .btn-danger { background: rgba(255,77,109,.12); color: var(--danger); border: 1px solid rgba(255,77,109,.25); }
    .btn-danger:hover { background: rgba(255,77,109,.22); }
    .btn-edit { background: rgba(0,212,170,.1); color: var(--accent2); border: 1px solid rgba(0,212,170,.25); }
    .btn-edit:hover { background: rgba(0,212,170,.2); }
    .btn-sm { height: 30px; padding: 0 12px; font-size: 12px; }

    /* ── FLASH MESSAGES ─────────────────────────────── */
    .flash { padding: 12px 18px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .flash-success { background: rgba(0,212,170,.12); color: var(--accent2); border: 1px solid rgba(0,212,170,.25); }
    .flash-error   { background: rgba(255,77,109,.1);  color: var(--danger);  border: 1px solid rgba(255,77,109,.25); }

    /* ── TABLE ─────────────────────────────────────── */
    .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      background: var(--surface2); padding: 12px 16px;
      text-align: left; font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px; color: var(--muted);
      border-bottom: 1px solid var(--border);
    }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--surface2); }
    tbody td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }

    .product-cell { display: flex; align-items: center; gap: 12px; }
    .product-thumb {
      width: 46px; height: 46px; border-radius: 8px; object-fit: cover;
      background: var(--surface2); flex-shrink: 0;
      border: 1px solid var(--border);
    }
    .product-name { font-weight: 600; font-size: 13px; margin-bottom: 2px; }
    .product-cat  { font-size: 11px; color: var(--muted); }

    .price-cell .new  { font-weight: 700; color: var(--accent2); }
    .price-cell .old  { font-size: 11px; color: var(--muted); text-decoration: line-through; }

    .cat-tag {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .cat-electronics { background: rgba(108,99,255,.15); color: var(--accent); }
    .cat-clothes      { background: rgba(255,169,77,.12);  color: var(--warn); }
    .cat-accessories  { background: rgba(0,212,170,.1);   color: var(--accent2); }
    .cat-other        { background: var(--surface2);       color: var(--muted); }

    .rating-cell { display: flex; align-items: center; gap: 5px; }
    .stars { color: #fbbf24; font-size: 13px; }
    .review-count { font-size: 11px; color: var(--muted); }

    .stock-cell { font-weight: 600; }
    .stock-low  { color: var(--danger); }
    .stock-ok   { color: var(--accent2); }

    .status-badge {
      display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .status-active   { background: rgba(0,212,170,.1); color: var(--accent2); }
    .status-inactive { background: rgba(255,77,109,.1); color: var(--danger); }

    .actions-cell { display: flex; gap: 6px; }

    /* ── EMPTY STATE ─────────────────────────────────── */
    .empty-state {
      text-align: center; padding: 70px 24px;
      color: var(--muted);
    }
    .empty-state .empty-icon { font-size: 52px; margin-bottom: 14px; }
    .empty-state h3 { font-family: 'Syne', sans-serif; font-size: 18px; color: var(--text); margin-bottom: 8px; }

    /* ── PAGINATION INFO ─────────────────────────────── */
    .table-footer {
      padding: 12px 16px;
      font-size: 12px; color: var(--muted);
      border-top: 1px solid var(--border);
      background: var(--surface2);
    }

    /* ── DELETE MODAL ─────────────────────────────────── */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.65); backdrop-filter: blur(4px);
      z-index: 999; align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 16px; padding: 32px; max-width: 400px; width: 90%;
      text-align: center;
    }
    .modal h3 { font-family: 'Syne', sans-serif; font-size: 20px; margin-bottom: 10px; }
    .modal p  { color: var(--muted); font-size: 14px; margin-bottom: 24px; }
    .modal-btns { display: flex; gap: 10px; justify-content: center; }

    @media(max-width:768px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { display: none; }
      .main { padding: 18px; }
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="topbar-brand">🛍️ Store <span>Admin</span></div>
  <div class="topbar-right">
    <a href="../index.php" class="topbar-link">← View Store</a>
    <a href="product_form.php" class="btn btn-primary" style="height:34px;font-size:12px;">+ Add Product</a>
  </div>
</div>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-label">Overview</div>
      <div class="stat-block">
        <div class="stat-val"><?= $totalProducts ?></div>
        <div class="stat-lbl">Total Products</div>
      </div>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-label">Categories</div>
      <a href="products.php" class="sidebar-item <?= !$category || $category === 'all' ? 'active' : '' ?>">
        🗂 All Products <span class="cat-badge"><?= $totalProducts ?></span>
      </a>
      <a href="products.php?category=electronics" class="sidebar-item <?= $category === 'electronics' ? 'active' : '' ?>">
        💻 Electronics <span class="cat-badge"><?= $stats['electronics'] ?? 0 ?></span>
      </a>
      <a href="products.php?category=clothes" class="sidebar-item <?= $category === 'clothes' ? 'active' : '' ?>">
        👗 Clothes <span class="cat-badge"><?= $stats['clothes'] ?? 0 ?></span>
      </a>
      <a href="products.php?category=accessories" class="sidebar-item <?= $category === 'accessories' ? 'active' : '' ?>">
        ⌚ Accessories <span class="cat-badge"><?= $stats['accessories'] ?? 0 ?></span>
      </a>
      <a href="products.php?category=other" class="sidebar-item <?= $category === 'other' ? 'active' : '' ?>">
        📦 Other <span class="cat-badge"><?= $stats['other'] ?? 0 ?></span>
      </a>
    </div>

  </aside>

  <!-- MAIN -->
  <main class="main">

    <div class="main-header">
      <div>
        <h1 class="page-title">Products <span>Management</span></h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">
          <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> found
          <?= $search ? ' for "' . htmlspecialchars($search) . '"' : '' ?>
        </p>
      </div>
      <a href="product_form.php" class="btn btn-primary">+ Add New Product</a>
    </div>

    <?php if ($success): ?>
      <div class="flash flash-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="flash flash-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Toolbar -->
    <form method="GET" action="products.php" style="margin-bottom:18px;">
      <?php if ($category): ?>
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
      <?php endif; ?>
      <div class="toolbar">
        <div class="search-box">
          <span class="search-icon">🔍</span>
          <input type="text" name="search" placeholder="Search products by name…"
            value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn btn-ghost">Search</button>
        <?php if ($search || $category): ?>
          <a href="products.php" class="btn btn-ghost">✕ Clear</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Table -->
    <div class="table-wrap">
      <?php if (empty($products)): ?>
        <div class="empty-state">
          <div class="empty-icon">📭</div>
          <h3>No products found</h3>
          <p style="font-size:13px;margin-bottom:18px;">
            <?= $search ? 'Try a different search term.' : 'Add your first product to get started.' ?>
          </p>
          <a href="product_form.php" class="btn btn-primary">+ Add Product</a>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Product</th>
              <th>Price</th>
              <th>Category</th>
              <th>Rating</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $i => $p): ?>
            <tr>
              <td style="color:var(--muted);font-size:12px;"><?= $p['id'] ?></td>
              <td>
                <div class="product-cell">
                  <img class="product-thumb"
                    src="../<?= htmlspecialchars($p['image_path']) ?>"
                    onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2246%22 height=%2246%22><rect fill=%22%2322263a%22 width=%2246%22 height=%2246%22/><text x=%2223%22 y=%2229%22 text-anchor=%22middle%22 font-size=%2218%22>📦</text></svg>'"
                    alt="<?= htmlspecialchars($p['name']) ?>">
                  <div>
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-cat"><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                  </div>
                </div>
              </td>
              <td class="price-cell">
                <div class="new">₹<?= number_format($p['price'], 0) ?></div>
                <div class="old">₹<?= number_format($p['old_price'], 0) ?></div>
              </td>
              <td>
                <span class="cat-tag cat-<?= htmlspecialchars($p['category']) ?>">
                  <?= ucfirst(htmlspecialchars($p['category'])) ?>
                </span>
              </td>
              <td class="rating-cell">
                <span class="stars">★</span>
                <span><?= $p['rating'] ?></span>
                <span class="review-count">(<?= number_format($p['reviews']) ?>)</span>
              </td>
              <td class="stock-cell <?= $p['stock'] < 10 ? 'stock-low' : 'stock-ok' ?>">
                <?= $p['stock'] ?>
              </td>
              <td>
                <span class="status-badge <?= $p['is_active'] ? 'status-active' : 'status-inactive' ?>">
                  <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td class="actions-cell">
                <a href="product_form.php?id=<?= $p['id'] ?>" class="btn btn-edit btn-sm">✏ Edit</a>
                <button class="btn btn-danger btn-sm"
                  onclick="confirmDelete(<?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['name'])) ?>')">
                  🗑 Delete
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="table-footer">
          Showing <?= count($products) ?> of <?= $totalProducts ?> products · Last updated: <?= date('d M Y, h:i A') ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="delete-modal">
  <div class="modal">
    <div style="font-size:48px;margin-bottom:12px;">🗑️</div>
    <h3>Delete Product?</h3>
    <p id="delete-msg">This action cannot be undone.</p>
    <form method="POST" action="product_handler.php" id="delete-form">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" id="delete-id">
      <div class="modal-btns">
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

<script>
  function confirmDelete(id, name) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-msg').textContent = `Delete "${name}"? This cannot be undone.`;
    document.getElementById('delete-modal').classList.add('open');
  }
  function closeModal() {
    document.getElementById('delete-modal').classList.remove('open');
  }
  document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
</body>
</html>
