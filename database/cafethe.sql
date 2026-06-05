SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS cafethe
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE cafethe;

DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'vendeur') NOT NULL DEFAULT 'vendeur',
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sku VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    sale_type ENUM('poids', 'unite') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    vat_rate DECIMAL(4,2) NOT NULL,
    stock DECIMAL(10,2) NOT NULL DEFAULT 0,
    image VARCHAR(255) NULL,
    origin VARCHAR(150) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(180) NULL UNIQUE,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    favorites TEXT NULL,
    abandoned_cart TEXT NULL
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    client_id INT NULL,
    status ENUM('pending', 'preparing', 'shipped', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'completed',
    payment_method ENUM('cb', 'especes', 'cheque') NOT NULL,
    delivery_method ENUM('magasin', 'livraison') NOT NULL DEFAULT 'magasin',
    total_ht DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_vat DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_ttc DECIMAL(10,2) NOT NULL DEFAULT 0,
    sale_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_sales_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_sales_client
        FOREIGN KEY (client_id)
        REFERENCES clients(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    vat_rate DECIMAL(4,2) NOT NULL,
    total_ht DECIMAL(10,2) NOT NULL,
    total_vat DECIMAL(10,2) NOT NULL,
    total_ttc DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_sale_items_sale
        FOREIGN KEY (sale_id)
        REFERENCES sales(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_sale_items_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

INSERT INTO users (name, email, password, role, is_active) VALUES
('Admin CafThé', 'admin@cafethe.local', '$2y$10$FqSxpp8Aqpld/J.AZVL1e.uLkEyLmBOdw114DqLbTbhXaxLM2fWSy', 'admin', 1),
('Vendeur CafThé', 'vendeur@cafethe.local', '$2y$10$FqSxpp8Aqpld/J.AZVL1e.uLkEyLmBOdw114DqLbTbhXaxLM2fWSy', 'vendeur', 1);

INSERT INTO categories (name, description) VALUES
('Thé', 'Thés en vrac et thés premium.'),
('Café', 'Cafés en grains et cafés haut de gamme.'),
('Accessoire', 'Accessoires pour le thé et le café.');

INSERT INTO products (category_id, sku, name, description, sale_type, price, vat_rate, stock, image, origin, is_active) VALUES
(1, 'THE-VERT-001', 'Thé vert premium', 'Thé vert haut de gamme issu de l’agriculture durable.', 'poids', 8.50, 5.50, 2500, NULL, 'Japon', 1),
(2, 'CAF-ARAB-001', 'Café arabica', 'Café en grains 100% arabica.', 'poids', 12.90, 5.50, 3000, NULL, 'Colombie', 1),
(3, 'ACC-INF-001', 'Infuseur inox', 'Accessoire pour infusion du thé.', 'unite', 6.90, 20.00, 25, NULL, NULL, 1);

INSERT INTO clients (name, email, phone, address, favorites, abandoned_cart) VALUES
('Client Test', 'client@test.local', '0600000000', 'Blois, France', 'Thé vert, café arabica', NULL);
