<?php
/**
 * ProductModel.php
 * Database operations for the products catalogue.
 */
require_once __DIR__ . '/Database.php';

class ProductModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /**
     * All active products (optionally filtered by category).
     */
    public function all(?string $category = null): array
    {
        if ($category && $category !== 'all') {
            $stmt = $this->db->prepare(
                'SELECT id, name, price, old_price AS oldPrice, image_path AS img,
                        rating, reviews, category
                   FROM products
                  WHERE is_active = 1 AND category = :cat
                  ORDER BY id'
            );
            $stmt->execute([':cat' => $category]);
        } else {
            $stmt = $this->db->query(
                'SELECT id, name, price, old_price AS oldPrice, image_path AS img,
                        rating, reviews, category
                   FROM products
                  WHERE is_active = 1
                  ORDER BY id'
            );
        }
        return $stmt->fetchAll();
    }

    /**
     * Top N deals (highest discount percentage).
     */
    public function topDeals(int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, price, old_price AS oldPrice, image_path AS img,
                    rating, reviews, category,
                    ROUND((1 - price / old_price) * 100) AS discount_pct
               FROM products
              WHERE is_active = 1
              ORDER BY discount_pct DESC
              LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find one product by ID (used in checkout validation).
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, price, old_price, image_path, stock
               FROM products
              WHERE id = :id AND is_active = 1
              LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Keyword search (LIKE – replace with FULLTEXT for production scale).
     */
    public function search(string $query): array
    {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            'SELECT id, name, price, old_price AS oldPrice, image_path AS img,
                    rating, reviews, category
               FROM products
              WHERE is_active = 1 AND name LIKE :q
              ORDER BY rating DESC
              LIMIT 10'
        );
        $stmt->execute([':q' => $like]);
        return $stmt->fetchAll();
    }
}
