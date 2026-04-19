<?php
/**
 * admin/product_form.php
 * Create a new product or Edit an existing one.
 * ?id=N  → edit mode;  no id → create mode
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Database.php';

$errors   = flash_get('errors', []);
$old      = flash_get('old_input', []);
$errorMsg = flash_get('error');

$isEdit  = false;
$product = null;
$id      = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = Database::pdo()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        if ($product) {
            $isEdit = true;
        } else {
            flash_set('error', 'Product not found.');
            redirect('products.php');
        }
    } catch (Exception $e) {
        flash_set('error', 'Database error: ' . $e->getMessage());
        redirect('products.php');
    }
}

// Merge flash old_input on top of db values (after validation fail)
$v = array_merge($product ?? [], $old);

function val(array $v, string $k, string $default = ''): string {
    return htmlspecialchars($v[$k] ?? $default);
}
function err(array $errors, string $k): string {
    return !empty($errors[$k])
        ? '<span class="field-err">⚠ ' . htmlspecialchars($errors[$k]) . '</span>'
        : '';
}
$categories = ['electronics', 'clothes', 'accessories', 'other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Edit Product' : 'Add Product' ?> – Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
     :root {
  --bg:       #ffffff;
  --surface:  #ffffff;
  --surface2: #f8fafc;
  --border:   #dbe2ea;

  --accent:   #7dd3fc;   /* soft sky blue */
  --accent2:  #10b981;   /* green */
  --danger:   #ef4444;
  --warn:     #f59e0b;

  --text:     #111827;
  --muted:    #4b5563;

  --radius:   12px;
}
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

    /* TOPBAR */
   .topbar {
  background: #7dd3fc;
  border-bottom: 1px solid #38bdf8;
  padding: 0 32px;
  height: 62px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
}
    .topbar-brand { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; }
    .topbar-brand span { color: var(--accent); }
    .topbar-right { display: flex; align-items: center; gap: 14px; }
    .back-link { color: var(--muted); text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 6px; transition: color .2s; }
    .back-link:hover { color: var(--text); }

    /* LAYOUT */
    .page { max-width: 900px; margin: 0 auto; padding: 36px 24px; }
    .page-header { margin-bottom: 32px; }
    .page-title { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; }
    .page-title .accent { color: var(--accent); }
    .page-subtitle { color: var(--muted); font-size: 14px; margin-top: 6px; }

    /* GRID */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-grid .full { grid-column: 1 / -1; }

    /* CARD */
    .card {
  background: #ffffff;
  border: 1px solid #dbe2ea;
  border-radius: 14px;
  padding: 28px;
  margin-bottom: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}
    .card-title {
      font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px; color: var(--muted);
      margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);
    }

    /* FORM FIELDS */
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
 .form-group input,
.form-group select,
.form-group textarea {
  background: #ffffff;
  border: 1.5px solid #dbe2ea;
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 14px;
  color: #111827;
  font-weight: 700;
}
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(108,99,255,.15);
    }
    .form-group input.has-error,
    .form-group select.has-error,
    .form-group textarea.has-error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(255,77,109,.1);
    }
    .form-group select option { background: var(--surface2); }
    .form-group textarea { resize: vertical; min-height: 90px; }
    .field-err { color: var(--danger); font-size: 12px; margin-top: 2px; }
    .field-hint { color: var(--muted); font-size: 11px; }

    /* INPUT PREFIX */
    .input-wrap { position: relative; }
    .input-prefix {
      position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
      color: var(--muted); font-size: 14px; font-weight: 600; pointer-events: none;
    }
    .input-wrap input { padding-left: 26px; }

    /* RANGE INPUT */
    .range-group { display: flex; align-items: center; gap: 12px; }
    .range-group input[type=range] {
      flex: 1; padding: 0; background: none; border: none;
      accent-color: var(--accent);
    }
    .range-group input[type=range]:focus { box-shadow: none; }
    .range-val {
      background: var(--surface2); border: 1px solid var(--border);
      border-radius: 6px; padding: 4px 10px; font-size: 13px; min-width: 48px; text-align: center;
    }

    /* IMAGE PREVIEW */
    .img-preview-wrap {
      background: var(--surface2); border: 2px dashed var(--border);
      border-radius: var(--radius); padding: 20px;
      display: flex; align-items: center; gap: 16px;
      transition: border-color .2s;
    }
    .img-preview-wrap:hover { border-color: var(--accent); }
    #img-preview {
      width: 72px; height: 72px; border-radius: 8px; object-fit: cover;
      background: var(--surface); border: 1px solid var(--border); flex-shrink: 0;
    }
    .img-preview-text { flex: 1; }
    .img-preview-text p { font-size: 13px; color: var(--muted); margin-top: 4px; }

    /* TOGGLE */
    .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 4px 0; }
    .toggle-label { font-size: 14px; font-weight: 500; }
    .toggle-sub   { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .toggle { position: relative; display: inline-block; width: 44px; height: 24px; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; cursor: pointer; inset: 0;
      background: var(--surface2); border-radius: 24px;
      transition: .25s;
      border: 1.5px solid var(--border);
    }
    .toggle-slider::before {
      content: ''; position: absolute; width: 16px; height: 16px;
      background: var(--muted); border-radius: 50%; left: 3px; top: 50%;
      transform: translateY(-50%); transition: .25s;
    }
    .toggle input:checked + .toggle-slider { background: var(--accent); border-color: var(--accent); }
    .toggle input:checked + .toggle-slider::before { transform: translate(20px, -50%); background: white; }

    /* FLASH */
    .flash { padding: 12px 18px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
    .flash-error { background: rgba(255,77,109,.1); color: var(--danger); border: 1px solid rgba(255,77,109,.25); }

    /* BUTTONS */
    .btn {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 0 24px; height: 44px; border-radius: 10px;
      font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 600;
      cursor: pointer; border: none; text-decoration: none; transition: all .18s;
    }
    .btn-primary {
  background: #7dd3fc;
  color: #111827;
}

.btn-primary:hover {
  background: #38bdf8;
}
   
    .btn-ghost { background: var(--surface); color: var(--text); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--surface2); }
    .form-actions { display: flex; gap: 12px; align-items: center; justify-content: flex-end; }

    @media(max-width:640px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-grid .full { grid-column: 1; }
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="topbar-brand">🛍️ Store <span>Admin</span></div>
  <div class="topbar-right">
    <a href="products.php" class="back-link">← Back to Products</a>
  </div>
</div>

<div class="page">

  <div class="page-header">
    <h1 class="page-title">
      <?= $isEdit ? '✏️ Edit <span class="accent">Product</span>' : '➕ Add <span class="accent">Product</span>' ?>
    </h1>
    <p class="page-subtitle">
      <?= $isEdit
        ? 'Update the details for "' . htmlspecialchars($product['name']) . '" (ID #' . $id . ')'
        : 'Fill in the details below to add a new product to the store.' ?>
    </p>
  </div>

  <?php if ($errorMsg): ?>
    <div class="flash flash-error">❌ <?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <form method="POST" action="product_handler.php" novalidate id="product-form">
    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <!-- ── BASIC INFO ──────────────────────────────────────── -->
    <div class="card">
      <div class="card-title">📋 Basic Information</div>
      <div class="form-grid">

        <div class="form-group full">
          <label>Product Name *</label>
          <input type="text" name="name" placeholder="e.g. Sony WH-1000XM5 Headphones"
            value="<?= val($v, 'name') ?>"
            class="<?= !empty($errors['name']) ? 'has-error' : '' ?>">
          <?= err($errors, 'name') ?>
        </div>

        <div class="form-group">
          <label>Category *</label>
          <select name="category" class="<?= !empty($errors['category']) ? 'has-error' : '' ?>">
            <option value="">— Select category —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>"
                <?= (($v['category'] ?? '') === $cat) ? 'selected' : '' ?>>
                <?= ucfirst($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?= err($errors, 'category') ?>
        </div>

        <div class="form-group">
          <label>Stock Quantity *</label>
          <input type="number" name="stock" min="0" max="99999" placeholder="100"
            value="<?= val($v, 'stock', '100') ?>"
            class="<?= !empty($errors['stock']) ? 'has-error' : '' ?>">
          <?= err($errors, 'stock') ?>
        </div>

      </div>
    </div>

    <!-- ── PRICING ──────────────────────────────────────────── -->
    <div class="card">
      <div class="card-title">💰 Pricing</div>
      <div class="form-grid">

        <div class="form-group">
          <label>Selling Price (₹) *</label>
          <div class="input-wrap">
            <span class="input-prefix">₹</span>
            <input type="number" name="price" min="0" step="0.01" placeholder="999.00"
              value="<?= val($v, 'price') ?>"
              class="<?= !empty($errors['price']) ? 'has-error' : '' ?>">
          </div>
          <?= err($errors, 'price') ?>
        </div>

        <div class="form-group">
          <label>Original / MRP (₹) *</label>
          <div class="input-wrap">
            <span class="input-prefix">₹</span>
            <input type="number" name="old_price" min="0" step="0.01" placeholder="1999.00"
              value="<?= val($v, 'old_price') ?>"
              class="<?= !empty($errors['old_price']) ? 'has-error' : '' ?>">
          </div>
          <?= err($errors, 'old_price') ?>
          <span class="field-hint" id="discount-hint"></span>
        </div>

      </div>
    </div>

    <!-- ── MEDIA ──────────────────────────────────────────── -->
    <div class="card">
      <div class="card-title">🖼️ Product Image</div>
      <div class="form-group">
        <label>Image Path (relative to project root) *</label>
        <div class="img-preview-wrap">
          <img id="img-preview"
            src="<?= $isEdit ? '../' . htmlspecialchars($product['image_path']) : '' ?>"
            onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2272%22 height=%2272%22><rect fill=%22%2322263a%22 width=%2272%22 height=%2272%22/><text x=%2236%22 y=%2244%22 text-anchor=%22middle%22 font-size=%2228%22>📦</text></svg>'"
            alt="Preview">
          <div class="img-preview-text">
            <input type="text" name="image_path" placeholder="Images/product.jpg"
              value="<?= val($v, 'image_path') ?>"
              oninput="updatePreview(this.value)"
              class="<?= !empty($errors['image_path']) ? 'has-error' : '' ?>">
            <p>Enter a relative path like <code style="color:var(--accent2);">Images/product.jpg</code>. Preview updates as you type.</p>
          </div>
        </div>
        <?= err($errors, 'image_path') ?>
      </div>
    </div>

    <!-- ── RATINGS ──────────────────────────────────────────── -->
    <div class="card">
      <div class="card-title">⭐ Ratings & Reviews</div>
      <div class="form-grid">

        <div class="form-group">
          <label>Rating (0 – 5)</label>
          <div class="range-group">
            <input type="range" name="rating" min="0" max="5" step="0.1"
              value="<?= val($v, 'rating', '4.0') ?>"
              oninput="document.getElementById('rating-val').textContent = parseFloat(this.value).toFixed(1)">
            <span class="range-val" id="rating-val"><?= number_format(floatval($v['rating'] ?? 4.0), 1) ?></span>
          </div>
          <?= err($errors, 'rating') ?>
        </div>

        <div class="form-group">
          <label>Number of Reviews</label>
          <input type="number" name="reviews" min="0" placeholder="0"
            value="<?= val($v, 'reviews', '0') ?>"
            class="<?= !empty($errors['reviews']) ? 'has-error' : '' ?>">
          <?= err($errors, 'reviews') ?>
        </div>

      </div>
    </div>

    <!-- ── VISIBILITY ──────────────────────────────────────── -->
    <div class="card">
      <div class="card-title">👁️ Visibility</div>
      <div class="toggle-row">
        <div>
          <div class="toggle-label">Active / Visible</div>
          <div class="toggle-sub">Inactive products won't appear in the store.</div>
        </div>
        <label class="toggle">
          <input type="checkbox" name="is_active" value="1"
            <?= ($v['is_active'] ?? 1) ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>
    </div>

    <!-- ── ACTIONS ──────────────────────────────────────────── -->
    <div class="form-actions">
      <a href="products.php" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-primary" id="submit-btn">
        <?= $isEdit ? '💾 Save Changes' : '➕ Add Product' ?>
      </button>
    </div>

  </form>
</div>

<script>
  // Live image preview
  function updatePreview(path) {
    const img = document.getElementById('img-preview');
    img.src = path ? '../' + path : '';
  }

  // Live discount hint
  function updateDiscount() {
    const price    = parseFloat(document.querySelector('[name=price]').value)    || 0;
    const oldPrice = parseFloat(document.querySelector('[name=old_price]').value) || 0;
    const hint = document.getElementById('discount-hint');
    if (price > 0 && oldPrice > 0 && oldPrice >= price) {
      const pct = Math.round((1 - price / oldPrice) * 100);
      hint.textContent = `💡 ${pct}% discount`;
      hint.style.color = 'var(--accent2)';
    } else if (price > 0 && oldPrice > 0 && oldPrice < price) {
      hint.textContent = '⚠ MRP should be ≥ selling price';
      hint.style.color = 'var(--warn)';
    } else {
      hint.textContent = '';
    }
  }
  document.querySelector('[name=price]')?.addEventListener('input', updateDiscount);
  document.querySelector('[name=old_price]')?.addEventListener('input', updateDiscount);
  updateDiscount();

  // Client validation
  document.getElementById('product-form').addEventListener('submit', function(e) {
    let ok = true;
    const fields = [
      { name: 'name',       test: v => v.trim().length >= 2,  msg: 'Product name required (min 2 chars).' },
      { name: 'category',   test: v => v !== '',               msg: 'Please select a category.' },
      { name: 'price',      test: v => parseFloat(v) > 0,      msg: 'Enter a valid price.' },
      { name: 'old_price',  test: v => parseFloat(v) > 0,      msg: 'Enter a valid MRP.' },
      { name: 'image_path', test: v => v.trim().length > 0,    msg: 'Image path is required.' },
      { name: 'stock',      test: v => parseInt(v) >= 0,       msg: 'Stock must be 0 or more.' },
    ];
    fields.forEach(f => {
      const input = this.querySelector(`[name="${f.name}"]`);
      if (!input) return;
      const oldErr = input.closest('.form-group')?.querySelector('.field-err');
      if (oldErr) oldErr.remove();
      input.classList.remove('has-error');
      if (!f.test(input.value)) {
        ok = false;
        input.classList.add('has-error');
        const span = document.createElement('span');
        span.className = 'field-err';
        span.textContent = '⚠ ' + f.msg;
        input.closest('.form-group').appendChild(span);
      }
    });
    if (!ok) { e.preventDefault(); return; }
    const btn = document.getElementById('submit-btn');
    btn.textContent = 'Saving…';
    btn.disabled = true;
  });
</script>
</body>
</html>
