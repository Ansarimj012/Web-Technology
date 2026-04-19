-- ============================================================
--  Online Store – Database Schema
--  Engine : MySQL 8.0+
--  Charset: utf8mb4 (full Unicode + emoji support)
-- ============================================================

CREATE DATABASE IF NOT EXISTS online_store
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE online_store;

-- ── 1. USERS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100)    NOT NULL,
    email         VARCHAR(255)    NOT NULL,
    phone         VARCHAR(15)     NOT NULL,
    password_hash VARCHAR(255)    NOT NULL,          -- bcrypt
    city          VARCHAR(100)    NOT NULL DEFAULT '',
    state         VARCHAR(100)    NOT NULL DEFAULT '',
    pin           VARCHAR(10)     NOT NULL DEFAULT '',
    remember_token VARCHAR(64)    NULL DEFAULT NULL, -- hashed "remember me" token
    is_active     TINYINT(1)      NOT NULL DEFAULT 1,
    created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_remember (remember_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. PRODUCTS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    name        VARCHAR(255)   NOT NULL,
    price       DECIMAL(10,2)  NOT NULL,
    old_price   DECIMAL(10,2)  NOT NULL,
    image_path  VARCHAR(255)   NOT NULL,
    rating      DECIMAL(3,1)   NOT NULL DEFAULT 0.0,
    reviews     INT UNSIGNED   NOT NULL DEFAULT 0,
    category    ENUM('electronics','clothes','accessories','other')
                               NOT NULL DEFAULT 'other',
    stock       INT UNSIGNED   NOT NULL DEFAULT 100,
    is_active   TINYINT(1)     NOT NULL DEFAULT 1,
    created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_products_category (category),
    KEY idx_products_active   (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. ORDERS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    order_code      VARCHAR(20)    NOT NULL,           -- e.g. ORD-A1B2C3
    user_id         INT UNSIGNED   NULL DEFAULT NULL,  -- NULL = guest order
    subtotal        DECIMAL(10,2)  NOT NULL,
    discount        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    delivery_charge DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    total           DECIMAL(10,2)  NOT NULL,
    -- Shipping snapshot (copied at order time so address changes don't affect history)
    ship_name       VARCHAR(100)   NOT NULL,
    ship_phone      VARCHAR(15)    NOT NULL,
    ship_address    VARCHAR(255)   NOT NULL,
    ship_city       VARCHAR(100)   NOT NULL,
    ship_state      VARCHAR(100)   NOT NULL,
    ship_pin        VARCHAR(10)    NOT NULL,
    -- Payment
    payment_method  ENUM('card','upi','cod') NOT NULL DEFAULT 'cod',
    payment_status  ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    -- Order lifecycle
    status          ENUM('confirmed','processing','shipped','delivered','cancelled')
                                   NOT NULL DEFAULT 'confirmed',
    placed_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_code (order_code),
    KEY idx_orders_user   (user_id),
    KEY idx_orders_status (status),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. ORDER ITEMS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    order_id    INT UNSIGNED   NOT NULL,
    product_id  INT UNSIGNED   NOT NULL,
    product_name VARCHAR(255)  NOT NULL,   -- snapshot
    unit_price  DECIMAL(10,2)  NOT NULL,   -- price paid
    old_price   DECIMAL(10,2)  NOT NULL,
    quantity    INT UNSIGNED   NOT NULL DEFAULT 1,
    line_total  DECIMAL(10,2)  NOT NULL,

    PRIMARY KEY (id),
    KEY idx_order_items_order   (order_id),
    KEY idx_order_items_product (product_id),
    CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders   (id) ON DELETE CASCADE,
    CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 5. LOGIN ATTEMPTS (brute-force protection) ───────────────
CREATE TABLE IF NOT EXISTS login_attempts (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    email       VARCHAR(255)  NOT NULL,
    ip_address  VARCHAR(45)   NOT NULL,
    attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_la_email (email),
    KEY idx_la_ip    (ip_address),
    KEY idx_la_time  (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 6. SESSIONS (optional DB-backed sessions) ────────────────
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id  VARCHAR(128)  NOT NULL,
    user_id     INT UNSIGNED  NOT NULL,
    ip_address  VARCHAR(45)   NOT NULL,
    user_agent  VARCHAR(255)  NOT NULL DEFAULT '',
    last_active DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (session_id),
    KEY idx_us_user (user_id),
    CONSTRAINT fk_us_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SEED DATA – Products (mirrors store.js)
-- ============================================================
INSERT INTO products (name, price, old_price, image_path, rating, reviews, category) VALUES
('Laptop',                              70000.00, 84999.00, 'Images/Laptop.avif',          4.2,  321,   'electronics'),
('Moto G96 5G (Pantone Blue, 256GB)',   18999.00, 22999.00, 'Images/motorola.jpg',          4.1,  7695,  'electronics'),
('Protonic Headphones',                  1768.00,  2999.00, 'Images/headphone.jpg',         4.3,  1689,  'electronics'),
('iPhone 13',                           67359.00, 82499.00, 'Images/phone.jpg',             4.4,  119,   'electronics'),
('Tibra Attire Men Kurta',                373.00,  1499.00, 'Images/kurta.jpg',             3.1,  10321, 'clothes'),
('Kriska Women Viscose Rayon Kurta',      559.00,  2499.00, 'Images/Kurti.avif',            3.9,  155,   'clothes'),
('Ladies Ethnic Wear',                    559.00,  2499.00, 'Images/Ladies.avif',           4.0,  519,   'clothes'),
('Denim Jeans',                          1559.00,  2499.00, 'Images/Jeans.webp',            4.2,  119,   'clothes'),
('RED TAPE Men Shoes',                   1568.00,  2999.00, 'Images/Redtapshoes.jpg',       4.5,  1689,  'accessories'),
('Smart Watch',                          1268.00,  2999.00, 'Images/watch.jpg',             4.1,  2689,  'accessories'),
('Gold Locket',                          1159.00,  2499.00, 'Images/locket.jpg',            4.0,  190,   'accessories'),
('Women Fashion Top',                     799.00,  1999.00, 'Images/femaleclothes.jpg',     4.1,  432,   'clothes');

-- Demo user (password: demo123)
INSERT INTO users (name, email, phone, password_hash, city, state, pin) VALUES
('Demo User', 'demo@store.com', '9000000000',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Bengaluru', 'Karnataka', '560001');
