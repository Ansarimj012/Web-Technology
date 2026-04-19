<?php
/**
 * admin/product_handler.php
 * Handles POST actions: create, update, delete.
 * Always redirects after processing (PRG pattern).
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

$action = $_POST['action'] ?? '';

// ── Helper: validate product fields ───────────────────────────────────────────
function validateProduct(array $post): array {
    $errors = [];
    $data   = [];

    // Name
    $name = trim($post['name'] ?? '');
    if (strlen($name) < 2) {
        $errors['name'] = 'Product name must be at least 2 characters.';
    } elseif (strlen($name) > 255) {
        $errors['name'] = 'Product name must be under 255 characters.';
    } else {
        $data['name'] = $name;
    }

    // Category
    $allowed = ['electronics', 'clothes', 'accessories', 'other'];
    $cat = trim($post['category'] ?? '');
    if (!in_array($cat, $allowed, true)) {
        $errors['category'] = 'Please select a valid category.';
    } else {
        $data['category'] = $cat;
    }

    // Price
    $price = $post['price'] ?? '';
    if (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Price must be a positive number.';
    } else {
        $data['price'] = round((float)$price, 2);
    }

    // Old Price (MRP)
    $oldPrice = $post['old_price'] ?? '';
    if (!is_numeric($oldPrice) || (float)$oldPrice <= 0) {
        $errors['old_price'] = 'MRP must be a positive number.';
    } else {
        $data['old_price'] = round((float)$oldPrice, 2);
    }

    // MRP should be >= price
    if (empty($errors['price']) && empty($errors['old_price'])) {
        if ($data['old_price'] < $data['price']) {
            $errors['old_price'] = 'MRP (original price) should be ≥ the selling price.';
        }
    }

    // Image path
    $imgPath = trim($post['image_path'] ?? '');
    if (empty($imgPath)) {
        $errors['image_path'] = 'Image path is required.';
    } elseif (strlen($imgPath) > 255) {
        $errors['image_path'] = 'Image path is too long.';
    } else {
        $data['image_path'] = $imgPath;
    }

    // Rating
    $rating = $post['rating'] ?? 0;
    $data['rating'] = max(0.0, min(5.0, round((float)$rating, 1)));

    // Reviews
    $reviews = (int)($post['reviews'] ?? 0);
    $data['reviews'] = max(0, $reviews);

    // Stock
    $stock = $post['stock'] ?? '';
    if (!ctype_digit((string)$stock) && !is_numeric($stock)) {
        $errors['stock'] = 'Stock must be a whole number.';
    } else {
        $data['stock'] = max(0, (int)$stock);
    }

    // Active
    $data['is_active'] = isset($post['is_active']) ? 1 : 0;

    return [$errors, $data];
}

// ── CREATE ─────────────────────────────────────────────────────────────────────
if ($action === 'create') {
    [$errors, $data] = validateProduct($_POST);

    if (!empty($errors)) {
        flash_set('errors', $errors);
        flash_set('old_input', $_POST);
        redirect('product_form.php');
    }

    try {
        $pdo  = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO products
               (name, price, old_price, image_path, rating, reviews, category, stock, is_active)
             VALUES
               (:name, :price, :old_price, :image_path, :rating, :reviews, :category, :stock, :is_active)'
        );
        $stmt->execute([
            ':name'       => $data['name'],
            ':price'      => $data['price'],
            ':old_price'  => $data['old_price'],
            ':image_path' => $data['image_path'],
            ':rating'     => $data['rating'],
            ':reviews'    => $data['reviews'],
            ':category'   => $data['category'],
            ':stock'      => $data['stock'],
            ':is_active'  => $data['is_active'],
        ]);
        $newId = $pdo->lastInsertId();
        flash_set('success', "✅ Product \"{$data['name']}\" (ID #$newId) created successfully.");
        redirect('products.php');

    } catch (Exception $e) {
        error_log('Product create error: ' . $e->getMessage());
        flash_set('error', 'Database error: Could not create product. Please try again.');
        flash_set('old_input', $_POST);
        redirect('product_form.php');
    }
}

// ── UPDATE ─────────────────────────────────────────────────────────────────────
if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        flash_set('error', 'Invalid product ID.');
        redirect('products.php');
    }

    [$errors, $data] = validateProduct($_POST);

    if (!empty($errors)) {
        flash_set('errors', $errors);
        flash_set('old_input', $_POST);
        redirect("product_form.php?id=$id");
    }

    try {
        $stmt = Database::pdo()->prepare(
            'UPDATE products
                SET name       = :name,
                    price      = :price,
                    old_price  = :old_price,
                    image_path = :image_path,
                    rating     = :rating,
                    reviews    = :reviews,
                    category   = :category,
                    stock      = :stock,
                    is_active  = :is_active
              WHERE id = :id'
        );
        $stmt->execute([
            ':name'       => $data['name'],
            ':price'      => $data['price'],
            ':old_price'  => $data['old_price'],
            ':image_path' => $data['image_path'],
            ':rating'     => $data['rating'],
            ':reviews'    => $data['reviews'],
            ':category'   => $data['category'],
            ':stock'      => $data['stock'],
            ':is_active'  => $data['is_active'],
            ':id'         => $id,
        ]);
        flash_set('success', "✅ Product \"{$data['name']}\" updated successfully.");
        redirect('products.php');

    } catch (Exception $e) {
        error_log('Product update error: ' . $e->getMessage());
        flash_set('error', 'Database error: Could not update product. Please try again.');
        flash_set('old_input', $_POST);
        redirect("product_form.php?id=$id");
    }
}

// ── DELETE ─────────────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        flash_set('error', 'Invalid product ID.');
        redirect('products.php');
    }

    try {
        $pdo = Database::pdo();

        // Get name for confirmation message
        $nameStmt = $pdo->prepare('SELECT name FROM products WHERE id = :id');
        $nameStmt->execute([':id' => $id]);
        $row = $nameStmt->fetch();

        if (!$row) {
            flash_set('error', 'Product not found.');
            redirect('products.php');
        }

        // Soft delete (set is_active = 0) to protect order history integrity
        // Use hard delete only if no order_items reference this product
        $refStmt = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE product_id = :id');
        $refStmt->execute([':id' => $id]);
        $refCount = (int)$refStmt->fetchColumn();

        if ($refCount > 0) {
            // Soft delete – keep record but hide from store
            $pdo->prepare('UPDATE products SET is_active = 0 WHERE id = :id')
                ->execute([':id' => $id]);
            flash_set('success', "Product \"{$row['name']}\" deactivated (it has {$refCount} order reference(s), so it was hidden instead of deleted).");
        } else {
            // Hard delete – safe, no references
            $pdo->prepare('DELETE FROM products WHERE id = :id')
                ->execute([':id' => $id]);
            flash_set('success', "Product \"{$row['name']}\" permanently deleted.");
        }

        redirect('products.php');

    } catch (Exception $e) {
        error_log('Product delete error: ' . $e->getMessage());
        flash_set('error', 'Database error: Could not delete product. Please try again.');
        redirect('products.php');
    }
}

// Unknown action
flash_set('error', 'Unknown action.');
redirect('products.php');
